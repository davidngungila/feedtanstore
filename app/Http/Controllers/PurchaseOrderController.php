<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\AccountingEntry;
use Illuminate\Http\Request;
use Dompdf\Dompdf;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with('supplier')->get();
        return view('purchasing.orders', compact('purchaseOrders'));
    }

    public function create(Request $request)
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        $preselectedProduct = null;
        $preselectedQuantity = null;

        if ($request->has('product')) {
            $preselectedProduct = Product::find($request->product);
            if ($preselectedProduct) {
                // Calculate how much to reorder to get to (at least) reorder level
                $preselectedQuantity = max(1, $preselectedProduct->reorder_level - $preselectedProduct->quantity);
            }
        }

        return view('purchasing.orders-create', compact('suppliers', 'products', 'preselectedProduct', 'preselectedQuantity'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity' => 'required|numeric|min:1',
            'products.*.unit_price' => 'required|numeric|min:0',
        ]);

        $poNumber = 'PO-' . date('YmdHis');
        $subtotal = 0;

        foreach ($request->products as $product) {
            $subtotal += $product['quantity'] * $product['unit_price'];
        }

        $tax = $request->tax ?? 0;
        $discount = $request->discount ?? 0;
        $total = $subtotal + $tax - $discount;

        $purchaseOrder = PurchaseOrder::create([
            'po_number' => $poNumber,
            'supplier_id' => $request->supplier_id,
            'order_date' => $request->order_date,
            'expected_date' => $request->expected_date,
            'notes' => $request->notes,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $total,
            'status' => 'pending',
            'created_by' => auth()->id(),
            'approval_status' => 'pending'
        ]);

        foreach ($request->products as $productData) {
            $itemTotal = $productData['quantity'] * $productData['unit_price'];
            $purchaseOrder->items()->create([
                'product_id' => $productData['product_id'],
                'quantity' => $productData['quantity'],
                'unit_price' => $productData['unit_price'],
                'total' => $itemTotal,
            ]);
        }

        return redirect()->route('purchasing.orders')->with('success', 'Purchase Order created successfully!');
    }

    public function approve(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->update([
            'approval_status' => 'approved',
            'approved_by' => auth()->id(),
            'approved_at' => now()
        ]);

        // TODO: Send email notification to supplier/created by
        return redirect()->back()->with('success', 'Purchase Order approved successfully!');
    }

    public function reject(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->update([
            'approval_status' => 'rejected'
        ]);

        // TODO: Send email notification to created by
        return redirect()->back()->with('success', 'Purchase Order rejected successfully!');
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.product']);
        return view('purchasing.orders-show', compact('purchaseOrder'));
    }

    public function downloadPDF(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.product']);
        $pdf = new Dompdf();
        $pdf->loadHtml(view('purchasing.orders-pdf', compact('purchaseOrder'))->render());
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();
        return $pdf->stream($purchaseOrder->po_number . '.pdf');
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        $suppliers = Supplier::all();
        $products = Product::all();
        $purchaseOrder->load('items');
        return view('purchasing.orders-edit', compact('purchaseOrder', 'suppliers', 'products'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'order_date' => 'required|date',
            'expected_date' => 'nullable|date',
            'status' => 'required|in:pending,received,canceled',
        ]);

        $oldStatus = $purchaseOrder->status;

        $purchaseOrder->update([
            'supplier_id' => $request->supplier_id,
            'order_date' => $request->order_date,
            'expected_date' => $request->expected_date,
            'notes' => $request->notes,
            'status' => $request->status,
        ]);

        // If status changed to received, update stock from order items and create accounting entries
        if ($oldStatus !== 'received' && $request->status === 'received') {
            foreach ($purchaseOrder->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->increment('quantity', $item->quantity);
                }
            }
            
            $this->createAccountingEntries($purchaseOrder);
        }

        return redirect()->route('purchasing.orders')->with('success', 'Purchase Order updated successfully!');
    }
    
    protected function createAccountingEntries(PurchaseOrder $purchaseOrder)
    {
        $inventoryAccount = \App\Models\Account::where('name', 'Inventory')->first();
        $accountsPayableAccount = \App\Models\Account::where('name', 'Accounts Payable')->first();

        $journalNumber = 'JE-PO-' . date('Ymd') . '-' . str_pad(\App\Models\JournalEntry::count() + 1, 4, '0', STR_PAD_LEFT);

        $journalEntry = \App\Models\JournalEntry::create([
            'journal_number' => $journalNumber,
            'entry_date' => now(),
            'description' => 'Purchase Order: ' . $purchaseOrder->po_number,
            'reference_type' => PurchaseOrder::class,
            'reference_id' => $purchaseOrder->id,
            'is_manual' => false,
        ]);

        // Debit Inventory
        AccountingEntry::create([
            'journal_entry_id' => $journalEntry->id,
            'reference_number' => $purchaseOrder->po_number,
            'reference_type' => PurchaseOrder::class,
            'account' => 'Inventory',
            'account_id' => $inventoryAccount?->id,
            'type' => 'debit',
            'amount' => $purchaseOrder->total,
            'description' => 'Inventory received'
        ]);
        
        // Credit Accounts Payable
        AccountingEntry::create([
            'journal_entry_id' => $journalEntry->id,
            'reference_number' => $purchaseOrder->po_number,
            'reference_type' => PurchaseOrder::class,
            'account' => 'Accounts Payable',
            'account_id' => $accountsPayableAccount?->id,
            'type' => 'credit',
            'amount' => $purchaseOrder->total,
            'description' => 'Goods received on credit'
        ]);
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->delete();
        return redirect()->route('purchasing.orders')->with('success', 'Purchase Order deleted successfully!');
    }
}
