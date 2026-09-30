<?php

namespace App\Http\Controllers;

use App\Models\SaleReturn;
use App\Models\SaleReturnItem;
use App\Models\Sale;
use App\Models\Product;
use App\Models\AccountingEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;

class SaleReturnController extends Controller {
    public function index() {
        $returns = SaleReturn::with(['sale', 'user'])->orderBy('created_at', 'desc')->get();
        return view('sales.returns', compact('returns'));
    }

    public function show(SaleReturn $return) {
        $return->load(['sale', 'user', 'items.saleItem.product']);
        return view('sales.returns-show', compact('return'));
    }

    public function downloadPDF(SaleReturn $return) {
        $return->load(['sale', 'user', 'items.saleItem.product']);
        
        // Prepare data for the template
        $paymentData = [
            'orderReference' => $return->return_number,
            'createdAt' => $return->created_at,
            'customer_name' => $return->sale->customer->name ?? 'Anonymous',
            'id' => $return->id,
            'description' => 'Product Return',
            'channel' => 'Cash',
            'status' => 'SUCCESS',
            'collectedCurrency' => 'TZS',
            'collectedAmount' => $return->total,
            'amount' => $return->total,
            'currency' => 'TZS',
        ];
        
        $pdf = new Dompdf();
        $pdf->loadHtml(view('sales.returns-pdf', compact('return', 'paymentData'))->render());
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();
        return $pdf->stream('return-' . $return->return_number . '.pdf');
    }

    public function create($saleId) {
        $sale = Sale::with('items.product')->findOrFail($saleId);
        return view('sales.returns', compact('sale'));
    }

    public function store(Request $request) {
        $request->validate([
            'sale_id' => 'required|exists:sales,id',
            'items' => 'required|array|min:1',
            'items.*.sale_item_id' => 'required|exists:sale_items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.reason' => 'nullable|string|max:500',
            'items.*.reason_code' => 'nullable|string|max:50',
            'reason' => 'nullable|string|max:1000',
            'reason_code' => 'nullable|string|max:50',
            'refund_method' => 'nullable|in:cash,mobile,card,credit,clickpesa,lipa_namba',
        ]);

        $sale = Sale::with('items')->findOrFail($request->sale_id);
        if ($sale->status !== 'completed') return back()->with('error','Only completed sales can be returned');

        // Validate return quantity does not exceed sold minus previously returned
        // Normalize items: drop unchecked rows (web form sends sale_item_id=0/off when unchecked)
        $items = collect($request->items)->filter(function ($itemData) {
            return !empty($itemData['sale_item_id']);
        })->values()->all();
        if (empty($items)) {
            if ($request->expectsJson()) return response()->json(['error' => 'Select at least one item to return'], 422);
            return back()->with('error','Select at least one item to return');
        }
        foreach ($items as $itemData) {
            $saleItem = \App\Models\SaleItem::find($itemData['sale_item_id']);
            if (!$saleItem) {
                if ($request->expectsJson()) return response()->json(['error' => 'Invalid sale item'], 422);
                return back()->with('error','Invalid sale item');
            }
            if ((int)$saleItem->sale_id !== (int)$sale->id) {
                if ($request->expectsJson()) return response()->json(['error' => 'Sale item does not belong to this sale'], 422);
                return back()->with('error','Sale item does not belong to this sale');
            }
            $alreadyReturned = SaleReturnItem::where('sale_item_id',$saleItem->id)->sum('quantity');
            $available = $saleItem->quantity - $alreadyReturned;
            if ($itemData['quantity'] > $available) {
                $msg = "Return quantity for {$saleItem->product->name} exceeds available {$available}";
                if ($request->expectsJson()) return response()->json(['error' => $msg], 422);
                return back()->with('error',$msg);
            }
            // Per-item reason is required
            $itemReason = trim($itemData['reason'] ?? '');
            if ($itemReason === '') {
                $msg = "Reason is required for {$saleItem->product->name}";
                if ($request->expectsJson()) return response()->json(['error' => $msg], 422);
                return back()->with('error',$msg);
            }
        }

        $returnNumber = 'RET-' . date('YmdHis');
        $total = 0;

        \Illuminate\Support\Facades\DB::beginTransaction();
        try {
            $return = SaleReturn::create([
                'return_number' => $returnNumber,
                'receipt_number' => $sale->invoice_number,
                'sale_id' => $request->sale_id,
                'branch_id' => $sale->branch_id ?? null,
                'location_id' => $sale->location_id ?? null,
                'user_id' => Auth::id(),
                'total' => 0,
                'refund_amount' => 0,
                'reason' => $request->reason,
                'approval_status' => in_array(Auth::user()->role,['admin','manager','store_supervisor'], true) ? 'approved' : 'pending',
                'refund_method' => $request->refund_method ?? $sale->payment_method ?? 'cash',
                'approver_id' => in_array(Auth::user()->role,['admin','manager','store_supervisor'], true) ? Auth::id() : null,
                'approved_at' => in_array(Auth::user()->role,['admin','manager','store_supervisor'], true) ? now() : null,
            ]);

            $returnItemsTotal = 0;
            $refundAmount = 0;
            foreach ($items as $itemData) {
                $saleItem = \App\Models\SaleItem::find($itemData['sale_item_id']);
                $itemTotal = $itemData['quantity'] * $saleItem->unit_price;
                $total += $itemTotal;
                $refundAmount += $itemTotal;
                $returnItemsTotal += $itemData['quantity'] * ($saleItem->product->cost_price ?? 0);

                // Per-item reason (label or free text). Fall back to overall reason.
                $itemReasonCode = $itemData['reason_code'] ?? $request->reason_code;
                $itemReasonText = trim($itemData['reason'] ?? '') !== '' ? trim($itemData['reason']) : $request->reason;
                if ($itemReasonCode && isset(SaleReturn::REASONS[$itemReasonCode])) {
                    $itemReasonText = SaleReturn::REASONS[$itemReasonCode] . ($itemReasonText && $itemReasonText !== SaleReturn::REASONS[$itemReasonCode] ? ' - ' . $itemReasonText : '');
                }

                $return->items()->create([
                    'sale_item_id' => $itemData['sale_item_id'],
                    'quantity' => $itemData['quantity'],
                    'quantity_sold' => $saleItem->quantity,
                    'unit_price' => $saleItem->unit_price,
                    'total' => $itemTotal,
                    'reason' => $itemReasonText,
                ]);

                // Only update stock if approved (manager/admin auto-approved)
                if ($return->approval_status === 'approved' && !$return->stock_updated) {
                    $product = Product::find($saleItem->product_id);
                    $product->increment('quantity', $itemData['quantity']);
                    \App\Models\StockMovement::create([
                        'product_id'=>$saleItem->product_id,
                        'movement_type'=>'return',
                        'quantity'=>$itemData['quantity'],
                        'reference_type'=>SaleReturn::class,
                        'reference_id'=>$return->id,
                        'user_id'=>Auth::id(),
                        'notes'=>'Return '.$return->return_number.' for sale '.$sale->invoice_number,
                    ]);
                }
            }

            $return->update(['total' => $total, 'refund_amount'=>$refundAmount, 'stock_updated'=> $return->approval_status==='approved']);

            if ($return->approval_status==='approved') {
                $sale = Sale::find($request->sale_id);
                if ($sale->type == 'credit' && $sale->customer_id) {
                    $customer = $sale->customer;
                    $customer->decrement('balance', $total);
                }
                $this->createAccountingEntries($return, $returnItemsTotal);
                \App\Services\AuditService::log('approve_sale_return','sales', SaleReturn::class, $return->id, null, $return->toArray(), 'Return approved immediately '.$return->return_number);
            } else {
                \App\Services\AuditService::log('create_sale_return_pending','sales', SaleReturn::class, $return->id, null, $return->toArray(), 'Return pending approval '.$return->return_number);
            }

            \Illuminate\Support\Facades\DB::commit();
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\DB::rollBack();
            if ($request->expectsJson()) return response()->json(['error' => 'Return failed: '.$e->getMessage()], 500);
            return back()->with('error','Return failed: '.$e->getMessage());
        }

        $msg = $return->approval_status==='approved' ? 'Return processed and approved!' : 'Return submitted for approval!';
        if ($request->expectsJson()) {
            $return->load(['items.saleItem.product', 'sale']);
            return response()->json(['success' => true, 'message' => $msg, 'return' => $return], 201);
        }
        return redirect()->route('sales.returns')->with('success', $msg);
    }

    public function approve(SaleReturn $return) {
        if (!in_array(Auth::user()->role,['admin','manager','store_supervisor'],true)) abort(403);
        if ($return->approval_status==='approved') return back()->with('error','Already approved');
        \Illuminate\Support\Facades\DB::transaction(function() use ($return){
            $return->update(['approval_status'=>'approved','approver_id'=>Auth::id(),'approved_at'=>now()]);
            $cogs=0;
            foreach ($return->items as $it){
                $saleItem=\App\Models\SaleItem::find($it->sale_item_id);
                $product=Product::find($saleItem->product_id);
                $product->increment('quantity',$it->quantity);
                $cogs+= $it->quantity * ($product->cost_price ?? 0);
                \App\Models\StockMovement::create(['product_id'=>$saleItem->product_id,'movement_type'=>'return','quantity'=>$it->quantity,'reference_type'=>SaleReturn::class,'reference_id'=>$return->id,'user_id'=>Auth::id(),'notes'=>'Approved return '.$return->return_number]);
            }
            $return->update(['stock_updated'=>true]);
            $this->createAccountingEntries($return,$cogs);
            \App\Services\AuditService::log('approve_sale_return','sales', SaleReturn::class, $return->id, ['approval_status'=>'pending'], ['approval_status'=>'approved'], 'Return approved '.$return->return_number);
        });
        return back()->with('success','Return approved and stock updated');
    }

    protected function createAccountingEntries(SaleReturn $saleReturn, $returnItemsTotal)
    {
        $cashAccount = \App\Models\Account::where('name', 'Cash')->first();
        $salesAccount = \App\Models\Account::where('name', 'Sales')->first();
        $inventoryAccount = \App\Models\Account::where('name', 'Inventory')->first();
        $cogsAccount = \App\Models\Account::where('name', 'Cost of Goods Sold')->first();

        $journalNumber = 'JE-RET-' . date('Ymd') . '-' . str_pad(\App\Models\JournalEntry::count() + 1, 4, '0', STR_PAD_LEFT);

        $journalEntry = \App\Models\JournalEntry::create([
            'journal_number' => $journalNumber,
            'entry_number' => $journalNumber,
            'entry_date' => now(),
            'description' => 'Sale Return: ' . $saleReturn->return_number,
            'reference_type' => SaleReturn::class,
            'reference_id' => $saleReturn->id,
            'is_manual' => false,
        ]);

        // Debit Sales to reverse the sale
        AccountingEntry::create([
            'journal_entry_id' => $journalEntry->id,
            'reference_number' => $saleReturn->return_number,
            'reference_type' => SaleReturn::class,
            'account' => 'Sales',
            'account_id' => $salesAccount?->id,
            'type' => 'debit',
            'amount' => $saleReturn->total,
            'description' => 'Sale return'
        ]);

        // Credit Cash to refund the customer
        AccountingEntry::create([
            'journal_entry_id' => $journalEntry->id,
            'reference_number' => $saleReturn->return_number,
            'reference_type' => SaleReturn::class,
            'account' => 'Cash',
            'account_id' => $cashAccount?->id,
            'type' => 'credit',
            'amount' => $saleReturn->total,
            'description' => 'Sale return refund'
        ]);

        // Debit Inventory to add back the stock
        AccountingEntry::create([
            'journal_entry_id' => $journalEntry->id,
            'reference_number' => $saleReturn->return_number,
            'reference_type' => SaleReturn::class,
            'account' => 'Inventory',
            'account_id' => $inventoryAccount?->id,
            'type' => 'debit',
            'amount' => $returnItemsTotal,
            'description' => 'Inventory returned'
        ]);

        // Credit COGS to reverse the cost of goods sold
        AccountingEntry::create([
            'journal_entry_id' => $journalEntry->id,
            'reference_number' => $saleReturn->return_number,
            'reference_type' => SaleReturn::class,
            'account' => 'Cost of Goods Sold',
            'account_id' => $cogsAccount?->id,
            'type' => 'credit',
            'amount' => $returnItemsTotal,
            'description' => 'COGS reversed'
        ]);
    }
}
