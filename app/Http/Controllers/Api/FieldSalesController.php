<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Product;
use App\Models\FieldSalesOrder;
use App\Models\FieldSalesOrderItem;
use App\Models\CustomerDemand;
use App\Models\CompetitorIntelligence;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Services\AuditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FieldSalesController extends Controller
{
    public function dashboard(Request $request)
    {
        $repId=$request->user()->id;
        $mySales=Sale::where('sales_rep_id',$repId)->orWhere('user_id',$repId)->where('sales_channel','field_sales')->count();
        $myOrders=FieldSalesOrder::where('sales_rep_id',$repId)->count();
        $pendingOrders=FieldSalesOrder::where('sales_rep_id',$repId)->where('status','pending')->count();
        $demands=CustomerDemand::where('staff_id',$repId)->count();
        $competitors=CompetitorIntelligence::where('sales_rep_id',$repId)->count();
        $totalRevenue=Sale::where('sales_rep_id',$repId)->where('sales_channel','field_sales')->sum('total') + FieldSalesOrder::where('sales_rep_id',$repId)->sum('total');
        return response()->json(compact('mySales','myOrders','pendingOrders','demands','competitors','totalRevenue'));
    }

    public function products(Request $request)
    {
        $q=Product::where('is_active',true)->with(['category','brand']);
        if ($request->filled('search')) {
            $s=$request->search;
            $q->where(function($w) use($s){ $w->where('name','like',"%$s%")->orWhere('barcode','like',"%$s%")->orWhere('sku','like',"%$s%"); });
        }
        if ($request->filled('category_id')) $q->where('category_id',$request->category_id);
        // Assigned products logic: for now show all; could filter by branch assignment
        return response()->json($q->paginate(50));
    }

    public function checkAvailability(Request $request, Product $product)
    {
        return response()->json([
            'product_id'=>$product->id,
            'name'=>$product->name,
            'quantity'=>$product->quantity,
            'is_available'=>$product->quantity>0,
            'selling_price'=>$product->selling_price,
            'cost_price'=>$product->cost_price,
        ]);
    }

    public function customers(Request $request)
    {
        $q=Customer::query();
        if ($request->filled('search')) $q->where('name','like','%'.$request->search.'%')->orWhere('phone','like','%'.$request->search.'%');
        return response()->json($q->latest()->paginate(20));
    }

    public function storeCustomer(Request $request)
    {
        $data=$request->validate([
            'name'=>'required|string|max:255',
            'phone'=>'nullable|string|max:20',
            'email'=>'nullable|email',
            'address'=>'nullable|string',
        ]);
        $customer=Customer::create($data);
        AuditService::log('create_customer','customers',Customer::class,$customer->id,null,$customer->toArray(),'Field sales created customer');
        return response()->json($customer,201);
    }

    public function createOrder(Request $request)
    {
        $data=$request->validate([
            'customer_id'=>'nullable|exists:customers,id',
            'customer_name'=>'required|string',
            'customer_phone'=>'nullable|string',
            'items'=>'required|array|min:1',
            'items.*.product_id'=>'required|exists:products,id',
            'items.*.quantity'=>'required|integer|min:1',
            'items.*.unit_price'=>'nullable|numeric|min:0',
            'discount'=>'nullable|numeric|min:0',
            'payment_method'=>'nullable|string',
            'paid_amount'=>'nullable|numeric|min:0',
            'notes'=>'nullable|string',
            'branch_id'=>'nullable|exists:branches,id',
            'local_transaction_id'=>'nullable|string|unique:field_sales_orders,local_transaction_id|unique:sales,local_transaction_id',
        ]);

        return DB::transaction(function() use ($data, $request){
            $salesRepId=$request->user()->id;
            $subtotal=0; $cogs=0;
            foreach ($data['items'] as $it){
                $product=Product::find($it['product_id']);
                $price=$it['unit_price']??$product->selling_price;
                $subtotal+=$price*$it['quantity'];
                $cogs+=($product->cost_price??0)*$it['quantity'];
                if ($product->quantity < $it['quantity']) return response()->json(['message'=>"Insufficient stock for {$product->name}. Available: {$product->quantity}"],400);
            }
            $discount=$data['discount']??0;
            $total=max(0,$subtotal-$discount);
            $paid=$data['paid_amount']??0;
            $outstanding=max(0,$total-$paid);
            $paymentStatus=$paid>= $total ? 'paid' : ($paid>0?'partially_paid':'pending');
            $localId=$data['local_transaction_id']?? (Str::uuid()->toString());

            // Idempotency check
            $existing=FieldSalesOrder::where('local_transaction_id',$localId)->first();
            if ($existing) return response()->json($existing,200);

            $order=FieldSalesOrder::create([
                'order_number'=>FieldSalesOrder::generateNumber(),
                'local_transaction_id'=>$localId,
                'customer_id'=>$data['customer_id']??null,
                'customer_name'=>$data['customer_name'],
                'customer_phone'=>$data['customer_phone']??null,
                'sales_rep_id'=>$salesRepId,
                'branch_id'=>$data['branch_id']??null,
                'subtotal'=>$subtotal,
                'discount'=>$discount,
                'total'=>$total,
                'paid_amount'=>$paid,
                'outstanding_amount'=>$outstanding,
                'payment_status'=>$paymentStatus,
                'status'=>'pending',
                'payment_method'=>$data['payment_method']??'cash',
                'cost_of_goods_sold'=>$cogs,
                'gross_profit'=>$total-$cogs,
                'sync_status'=>'synced',
                'synced_at'=>now(),
                'offline_created_at'=>now(),
                'device_info'=>$request->userAgent(),
                'notes'=>$data['notes']??null,
            ]);
            foreach ($data['items'] as $it){
                $product=Product::find($it['product_id']);
                $price=$it['unit_price']??$product->selling_price;
                FieldSalesOrderItem::create([
                    'field_sales_order_id'=>$order->id,
                    'product_id'=>$it['product_id'],
                    'quantity'=>$it['quantity'],
                    'unit_price'=>$price,
                    'cost_price'=>$product->cost_price??0,
                    'total'=>$price*$it['quantity'],
                ]);
                // Optionally decrement stock if immediate sale (paid) else reserve? For now decrement if paid/partially
                if ($paymentStatus!=='pending') {
                    $product->decrement('quantity',$it['quantity']);
                    StockMovement::create([
                        'product_id'=>$product->id,
                        'movement_type'=>'field_sale',
                        'quantity'=>$it['quantity'],
                        'reference_type'=>FieldSalesOrder::class,
                        'reference_id'=>$order->id,
                        'user_id'=>$salesRepId,
                        'notes'=>'Field sale order '.$order->order_number,
                    ]);
                }
            }
            AuditService::log('create_field_sale_order','field_sales', FieldSalesOrder::class, $order->id, null, $order->toArray(), 'Field order created');
            // Sync to sales if paid fully -> also create Sale record for revenue
            if ($paymentStatus==='paid') {
                $sale=Sale::create([
                    'invoice_number'=>'FS-INV-'.date('YmdHis').'-'.Str::random(4),
                    'local_transaction_id'=>$localId,
                    'customer_id'=>$order->customer_id,
                    'user_id'=>$salesRepId,
                    'sales_rep_id'=>$salesRepId,
                    'sales_channel'=>'field_sales',
                    'branch_id'=>$order->branch_id,
                    'subtotal'=>$subtotal,
                    'discount'=>$discount,
                    'total'=>$total,
                    'paid'=>$paid,
                    'change'=>0,
                    'payment_method'=>$order->payment_method,
                    'type'=>'cash',
                    'status'=>'completed',
                    'gross_sales'=>$total,
                    'cost_of_goods_sold'=>$cogs,
                    'gross_profit'=>$total-$cogs,
                    'sync_status'=>'synced',
                    'synced_at'=>now(),
                ]);
                foreach ($order->items as $it){
                    SaleItem::create([
                        'sale_id'=>$sale->id,
                        'product_id'=>$it->product_id,
                        'quantity'=>$it->quantity,
                        'unit_price'=>$it->unit_price,
                        'total'=>$it->total,
                    ]);
                }
            }
            return response()->json($order->load('items'),201);
        });
    }

    public function myOrders(Request $request)
    {
        $q=FieldSalesOrder::where('sales_rep_id',$request->user()->id)->with(['items.product','customer'])->latest();
        if ($request->filled('status')) $q->where('status',$request->status);
        if ($request->filled('payment_status')) $q->where('payment_status',$request->payment_status);
        return response()->json($q->paginate(20));
    }

    public function showOrder(Request $request, FieldSalesOrder $order)
    {
        if ((int)$order->sales_rep_id !== (int)$request->user()->id && $request->user()->role!=='admin') return response()->json(['message'=>'Forbidden'],403);
        return response()->json($order->load(['items.product','customer']));
    }

    public function updateOrderStatus(Request $request, FieldSalesOrder $order)
    {
        $request->validate(['status'=>'required|in:pending,confirmed,processing,delivered,cancelled','payment_status'=>'nullable|in:pending,paid,partially_paid,failed,refunded']);
        if ((int)$order->sales_rep_id !== (int)$request->user()->id && !in_array($request->user()->role,['admin','manager'],true)) return response()->json(['message'=>'Forbidden'],403);
        $order->update($request->only(['status','payment_status']));
        return response()->json($order);
    }

    public function syncOffline(Request $request)
    {
        // Batch sync for offline field sales
        $request->validate([
            'orders'=>'required|array',
            'orders.*.local_transaction_id'=>'required|string',
            'orders.*.customer_name'=>'required|string',
            'orders.*.items'=>'required|array|min:1',
            'orders.*.items.*.product_id'=>'required|exists:products,id',
            'orders.*.items.*.quantity'=>'required|integer|min:1',
            'orders.*.offline_created_at'=>'nullable|date',
        ]);
        $results=[];
        foreach ($request->orders as $ord){
            $existing=FieldSalesOrder::where('local_transaction_id',$ord['local_transaction_id'])->first();
            if ($existing){ $results[]=['local_transaction_id'=>$ord['local_transaction_id'],'status'=>'already_synced','order'=>$existing]; continue; }
            // reuse create logic
            $ord['sales_rep_id']=$request->user()->id;
            // simplified: create without stock check failure abort batch
            try {
                $subtotal=collect($ord['items'])->sum(fn($i)=> (Product::find($i['product_id'])->selling_price??0)*$i['quantity']);
                $order=FieldSalesOrder::create([
                    'order_number'=>FieldSalesOrder::generateNumber(),
                    'local_transaction_id'=>$ord['local_transaction_id'],
                    'customer_name'=>$ord['customer_name'],
                    'customer_phone'=>$ord['customer_phone']??null,
                    'customer_id'=>$ord['customer_id']??null,
                    'sales_rep_id'=>$request->user()->id,
                    'subtotal'=>$subtotal,
                    'discount'=>$ord['discount']??0,
                    'total'=>$ord['total']??$subtotal,
                    'status'=>$ord['status']??'pending',
                    'payment_status'=>$ord['payment_status']??'pending',
                    'payment_method'=>$ord['payment_method']??'cash',
                    'sync_status'=>'synced',
                    'synced_at'=>now(),
                    'offline_created_at'=>$ord['offline_created_at']??now(),
                ]);
                foreach ($ord['items'] as $it){
                    FieldSalesOrderItem::create([
                        'field_sales_order_id'=>$order->id,
                        'product_id'=>$it['product_id'],
                        'quantity'=>$it['quantity'],
                        'unit_price'=>Product::find($it['product_id'])->selling_price,
                        'total'=>Product::find($it['product_id'])->selling_price*$it['quantity'],
                    ]);
                }
                $results[]=['local_transaction_id'=>$ord['local_transaction_id'],'status'=>'synced','order'=>$order];
            } catch (\Throwable $e){
                $results[]=['local_transaction_id'=>$ord['local_transaction_id'],'status'=>'failed','error'=>$e->getMessage()];
            }
        }
        return response()->json(['results'=>$results]);
    }
}
