<?php

namespace App\Http\Controllers;

use App\Models\GoodsReceivedNote;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\AccountingEntry;
use Illuminate\Http\Request;
use Dompdf\Dompdf;

class GoodsReceivedNoteController extends Controller
{
    public function index()
    {
        $grns = GoodsReceivedNote::with(['supplier', 'purchaseOrder'])->get();
        return view('purchasing.grn', compact('grns'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        $purchaseOrders = PurchaseOrder::with('items.product')->where('status', 'pending')->where('approval_status', 'approved')->whereNotNull('sent_at')->get();
        $selectedPurchaseOrder = null;
        if (request()->has('purchase_order_id')) {
            $selectedPurchaseOrder = PurchaseOrder::with('items.product')->where('approval_status', 'approved')->whereNotNull('sent_at')->find(request()->purchase_order_id);
        }
        return view('purchasing.grn-create', compact('suppliers', 'products', 'purchaseOrders', 'selectedPurchaseOrder'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'received_date' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        // Validate PO is sent if provided
        if ($request->purchase_order_id) {
            $po = PurchaseOrder::findOrFail($request->purchase_order_id);
            if (!$po->sent_at) {
                return back()->with('error', 'Cannot receive goods for a purchase order that has not been sent to the supplier!');
            }
        }

        $grnNumber = 'GRN-' . date('YmdHis');
        $total = 0;

        foreach ($request->products as $product) {
            $total += $product['quantity'] * $product['unit_price'];
        }

        $grn = GoodsReceivedNote::create([
            'grn_number' => $grnNumber,
            'supplier_id' => $request->supplier_id,
            'purchase_order_id' => $request->purchase_order_id,
            'received_date' => $request->received_date,
            'notes' => $request->notes,
            'total' => $total,
            'status' => 'received',
        ]);

        foreach ($request->products as $productData) {
            $itemTotal = $productData['quantity'] * $productData['unit_price'];
            $grn->items()->create([
                'product_id' => $productData['product_id'],
                'quantity' => $productData['quantity'],
                'unit_price' => $productData['unit_price'],
                'total' => $itemTotal,
                'expiry_date' => $productData['expiry_date'] ?? null,
            ]);

            // Update product quantity, cost price, and selling price
            $product = Product::find($productData['product_id']);
            $product->increment('quantity', $productData['quantity']);
            $product->update([
                'cost_price' => $productData['unit_price'],
                'selling_price' => $productData['selling_price'],
            ]);
        }

        // If purchase order is linked, mark it as received
        if ($request->purchase_order_id) {
            $po = PurchaseOrder::find($request->purchase_order_id);
            $po->update(['status' => 'received']);
        }
        
        $this->createAccountingEntries($grn);

        // Dispatch job to send notifications
        \App\Jobs\SendGRNNotifications::dispatch($grn);

        return redirect()->route('purchasing.grn')->with('success', 'Goods Received Note created successfully! Notifications will be sent shortly.');
    }
    
    protected function createAccountingEntries(GoodsReceivedNote $grn)
    {
        $inventoryAccount = \App\Models\Account::where('name', 'Inventory')->first();
        $accountsPayableAccount = \App\Models\Account::where('name', 'Accounts Payable')->first();

        $journalNumber = 'JE-GRN-' . date('Ymd') . '-' . str_pad(\App\Models\JournalEntry::count() + 1, 4, '0', STR_PAD_LEFT);

        $journalEntry = \App\Models\JournalEntry::create([
            'journal_number' => $journalNumber,
            'entry_number' => $journalNumber,
            'entry_date' => now(),
            'description' => 'Goods Received: ' . $grn->grn_number,
            'reference_type' => GoodsReceivedNote::class,
            'reference_id' => $grn->id,
            'is_manual' => false,
        ]);

        // Debit Inventory
        AccountingEntry::create([
            'journal_entry_id' => $journalEntry->id,
            'reference_number' => $grn->grn_number,
            'reference_type' => GoodsReceivedNote::class,
            'account' => 'Inventory',
            'account_id' => $inventoryAccount?->id,
            'type' => 'debit',
            'amount' => $grn->total,
            'description' => 'Inventory received'
        ]);
        
        // Credit Accounts Payable
        AccountingEntry::create([
            'journal_entry_id' => $journalEntry->id,
            'reference_number' => $grn->grn_number,
            'reference_type' => GoodsReceivedNote::class,
            'account' => 'Accounts Payable',
            'account_id' => $accountsPayableAccount?->id,
            'type' => 'credit',
            'amount' => $grn->total,
            'description' => 'Goods received on credit'
        ]);
    }

    public function show(GoodsReceivedNote $grn)
    {
        $grn->load(['supplier', 'purchaseOrder', 'items.product']);
        return view('purchasing.grn-show', compact('grn'));
    }

    public function downloadPDF(GoodsReceivedNote $grn)
    {
        $grn->load(['supplier', 'purchaseOrder', 'items.product']);
        $pdf = new Dompdf();
        $pdf->loadHtml(view('purchasing.grn-pdf', compact('grn'))->render());
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();
        return $pdf->stream($grn->grn_number . '.pdf');
    }

    public function edit(GoodsReceivedNote $grn)
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        $purchaseOrders = PurchaseOrder::with('items.product')->get();
        $grn->load('items');
        return view('purchasing.grn-edit', compact('grn', 'suppliers', 'products', 'purchaseOrders'));
    }

    public function update(Request $request, GoodsReceivedNote $grn)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_order_id' => 'nullable|exists:purchase_orders,id',
            'received_date' => 'required|date',
        ]);

        $grn->update($request->all());

        return redirect()->route('purchasing.grn')->with('success', 'Goods Received Note updated successfully!');
    }

    public function destroy(GoodsReceivedNote $grn)
    {
        // Decrement product quantities
        foreach ($grn->items as $item) {
            $product = Product::find($item->product_id);
            $product->decrement('quantity', $item->quantity);
        }

        $grn->delete();
        return redirect()->route('purchasing.grn')->with('success', 'Goods Received Note deleted successfully!');
    }
}
