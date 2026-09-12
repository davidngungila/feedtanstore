<?php

namespace App\Http\Controllers;

use App\Models\OfflineTransaction;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OfflineSyncController extends Controller
{
    // Store offline transaction locally (pending queue)
    public function queue(Request $request)
    {
        $data=$request->validate([
            'local_transaction_id'=>'required|string|unique:offline_transactions,local_transaction_id|unique:sales,local_transaction_id',
            'transaction_type'=>'nullable|string|in:sale,return',
            'payload'=>'required|array',
            'offline_created_at'=>'required|date',
            'device_info'=>'nullable|string',
        ]);
        $offline = OfflineTransaction::create([
            'local_transaction_id'=>$data['local_transaction_id'],
            'transaction_type'=>$data['transaction_type']??'sale',
            'payload'=>$data['payload'],
            'cashier_id'=>Auth::id() ?? $request->input('cashier_id'),
            'branch_id'=>$request->input('branch_id'),
            'sync_status'=>'pending',
            'offline_created_at'=>$data['offline_created_at'],
            'device_info'=>$data['device_info']??$request->userAgent(),
            'ip_address'=>$request->ip(),
        ]);
        return response()->json(['message'=>'Queued','offline'=>$offline],201);
    }

    // Sync pending offline transactions to central DB
    public function sync(Request $request)
    {
        // Can be called with single or batch
        $request->validate([
            'transactions'=>'sometimes|array',
            'transactions.*.local_transaction_id'=>'required|string',
            'transactions.*.payload'=>'required|array',
            'transactions.*.offline_created_at'=>'required|date',
            'local_transaction_id'=>'sometimes|string',
            'payload'=>'sometimes|array',
        ]);

        $transactions = $request->input('transactions');
        if (!$transactions && $request->filled('local_transaction_id')) {
            $transactions = [[
                'local_transaction_id'=>$request->local_transaction_id,
                'payload'=>$request->payload,
                'offline_created_at'=>$request->offline_created_at,
                'device_info'=>$request->device_info,
            ]];
        }
        if (!$transactions) {
            // Sync all pending for this cashier
            $pendings = OfflineTransaction::where('sync_status','pending')->where('cashier_id',$request->user()->id ?? Auth::id())->get();
            $results=[];
            foreach ($pendings as $pending) {
                $results[]=$this->syncSingle($pending);
            }
            return response()->json(['synced'=> $results]);
        }

        $results=[];
        foreach ($transactions as $tx) {
            // Idempotency: if local_transaction_id already exists as sale, skip duplicate
            $existingSale = Sale::where('local_transaction_id',$tx['local_transaction_id'])->first();
            if ($existingSale) {
                // also mark offline transaction as synced if exists
                OfflineTransaction::where('local_transaction_id',$tx['local_transaction_id'])->update(['sync_status'=>'synced','synced_at'=>now(),'synced_sale_id'=>$existingSale->id]);
                $results[]=['local_transaction_id'=>$tx['local_transaction_id'],'status'=>'already_synced','sale_id'=>$existingSale->id];
                continue;
            }
            $offline = OfflineTransaction::firstOrCreate(
                ['local_transaction_id'=>$tx['local_transaction_id']],
                [
                    'transaction_type'=>'sale',
                    'payload'=>$tx['payload'],
                    'cashier_id'=>$request->user()->id ?? Auth::id(),
                    'branch_id'=>$tx['branch_id']??null,
                    'offline_created_at'=>$tx['offline_created_at'],
                    'device_info'=>$tx['device_info']??$request->userAgent(),
                    'ip_address'=>$request->ip(),
                    'sync_status'=>'pending',
                ]
            );
            $results[]=$this->syncSingle($offline);
        }
        return response()->json(['results'=>$results]);
    }

    private function syncSingle(OfflineTransaction $offline): array
    {
        try {
            $offline->update(['sync_status'=>'syncing','sync_attempts'=>$offline->sync_attempts+1]);
            $payload = is_string($offline->payload) ? json_decode($offline->payload,true) : $offline->payload;
            $result = DB::transaction(function() use ($offline,$payload){
                // Validate stock availability
                foreach ($payload['items'] ?? [] as $item) {
                    $product = Product::find($item['id'] ?? $item['product_id'] ?? null);
                    if (!$product) throw new \Exception('Product not found: '.json_encode($item));
                    if ($product->quantity < ($item['quantity']??0)) throw new \Exception("Insufficient stock for {$product->name}");
                }
                $invoiceNumber = $payload['invoice_number'] ?? 'INV-'.date('YmdHis').'-'.Str::random(4);
                // Ensure unique
                while (Sale::where('invoice_number',$invoiceNumber)->exists()) $invoiceNumber='INV-'.date('YmdHis').'-'.Str::random(4);

                $subtotal = collect($payload['items'])->sum(fn($i)=> ($i['price']??$i['unit_price']??0) * ($i['quantity']??0));
                $sale = Sale::create([
                    'invoice_number'=>$invoiceNumber,
                    'local_transaction_id'=>$offline->local_transaction_id,
                    'sync_status'=>'synced',
                    'synced_at'=>now(),
                    'offline_created_at'=>$offline->offline_created_at,
                    'device_info'=>$offline->device_info,
                    'customer_id'=>$payload['customer_id']??null,
                    'user_id'=>$offline->cashier_id,
                    'sales_rep_id'=>$offline->cashier_id,
                    'sales_channel'=>$payload['sales_channel']??'in_store',
                    'branch_id'=>$offline->branch_id,
                    'cash_drawer_session_id'=>$payload['cash_drawer_session_id']??null,
                    'subtotal'=>$subtotal,
                    'tax'=>$payload['tax']??0,
                    'discount'=>$payload['discount']??0,
                    'total'=>$payload['total']??$subtotal,
                    'paid'=>$payload['paid']??$payload['total']??$subtotal,
                    'change'=> max(0, ($payload['paid']??0) - ($payload['total']??$subtotal)),
                    'payment_method'=>$payload['payment_method']??'cash',
                    'type'=>'cash',
                    'status'=>'completed',
                    'notes'=>$payload['notes']??'Synced offline transaction',
                ]);
                foreach ($payload['items'] as $itemData) {
                    $productId = $itemData['id'] ?? $itemData['product_id'];
                    $qty = $itemData['quantity'];
                    $price = $itemData['price'] ?? $itemData['unit_price'] ?? 0;
                    $sale->items()->create([
                        'product_id'=>$productId,
                        'quantity'=>$qty,
                        'unit_price'=>$price,
                        'discount'=>0,
                        'total'=>$qty*$price,
                    ]);
                    $product=Product::find($productId);
                    $product->decrement('quantity',$qty);
                    StockMovement::create([
                        'product_id'=>$productId,
                        'movement_type'=>'sale',
                        'quantity'=>$qty,
                        'reference_type'=>Sale::class,
                        'reference_id'=>$sale->id,
                        'user_id'=>$offline->cashier_id,
                        'notes'=>'Offline sync sale '.$sale->invoice_number,
                    ]);
                }
                // Revenue calculations COGS
                $cogs=0;
                foreach ($sale->items as $si){ $prod=Product::find($si->product_id); $cogs+= ($prod->cost_price??0)*$si->quantity; }
                $sale->update(['cost_of_goods_sold'=>$cogs,'gross_sales'=>$sale->total,'gross_profit'=>$sale->total-$cogs]);
                AuditService::log('sync_offline_sale','sales', Sale::class, $sale->id, null, $sale->toArray(), 'Offline sale synced '.$offline->local_transaction_id);
                $offline->update(['sync_status'=>'synced','synced_at'=>now(),'synced_sale_id'=>$sale->id]);
                return $sale;
            });
            return ['local_transaction_id'=>$offline->local_transaction_id,'status'=>'synced','sale_id'=>$result->id,'invoice'=>$result->invoice_number];
        } catch (\Throwable $e) {
            $offline->update(['sync_status'=>'failed','last_error'=>$e->getMessage()]);
            AuditService::log('failed_offline_sync','sales', OfflineTransaction::class, $offline->id, null, ['error'=>$e->getMessage()], 'Sync failed '.$offline->local_transaction_id);
            return ['local_transaction_id'=>$offline->local_transaction_id,'status'=>'failed','error'=>$e->getMessage()];
        }
    }

    public function status(Request $request)
    {
        $cashierId=$request->user()->id ?? Auth::id();
        $pending=OfflineTransaction::where('cashier_id',$cashierId)->where('sync_status','pending')->count();
        $failed=OfflineTransaction::where('cashier_id',$cashierId)->where('sync_status','failed')->count();
        $synced=OfflineTransaction::where('cashier_id',$cashierId)->where('sync_status','synced')->count();
        $queue=OfflineTransaction::where('cashier_id',$cashierId)->latest()->limit(20)->get();
        if ($request->expectsJson()) return response()->json(compact('pending','failed','synced','queue'));
        return view('offline.status', compact('pending','failed','synced','queue'));
    }

    public function retry(Request $request, OfflineTransaction $offline)
    {
        if ($offline->sync_status!=='failed') return back()->with('error','Only failed can be retried');
        $result=$this->syncSingle($offline);
        if ($result['status']==='synced') return back()->with('success','Retried and synced');
        return back()->with('error','Retry failed: '.$result['error']);
    }
}
