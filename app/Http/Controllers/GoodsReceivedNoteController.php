<?php

namespace App\Http\Controllers;

use App\Models\GoodsReceivedNote;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
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
        $purchaseOrders = PurchaseOrder::with('items.product')->where('status', 'pending')->get();
        return view('purchasing.grn-create', compact('suppliers', 'products', 'purchaseOrders'));
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

            // Update product quantity
            $product = Product::find($productData['product_id']);
            $product->increment('quantity', $productData['quantity']);
        }

        // If purchase order is linked, mark it as received
        if ($request->purchase_order_id) {
            $po = PurchaseOrder::find($request->purchase_order_id);
            $po->update(['status' => 'received']);
        }

        return redirect()->route('purchasing.grn')->with('success', 'Goods Received Note created successfully!');
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
