<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrderRequest;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PurchaseOrderRequestController extends Controller
{
    public function index()
    {
        $requests = PurchaseOrderRequest::with('product', 'requester', 'supplier')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('storekeeper.purchase-order-requests', compact('requests'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('storekeeper.purchase-order-requests-create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'reason' => 'required|string',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.requested_quantity' => 'required|numeric|min:1',
            'products.*.reason' => 'nullable|string',
        ]);

        $baseRequestNumber = PurchaseOrderRequest::generateRequestNumber();
        $createdCount = 0;

        foreach ($request->products as $index => $productData) {
            // Add sequence suffix for bulk requests (e.g., POR-20260822-0001-1, POR-20260822-0001-2)
            $requestNumber = count($request->products) > 1 
                ? $baseRequestNumber . '-' . ($index + 1)
                : $baseRequestNumber;

            PurchaseOrderRequest::create([
                'request_number' => $requestNumber,
                'product_id' => $productData['product_id'],
                'requested_quantity' => $productData['requested_quantity'],
                'reason' => $productData['reason'] ?? $request->reason,
                'status' => 'pending',
                'requested_by' => Auth::id(),
            ]);
            $createdCount++;
        }

        return redirect()->route('purchase-order-requests')
            ->with('success', "Purchase order request submitted successfully with {$createdCount} product(s)");
    }

    public function show(PurchaseOrderRequest $purchaseOrderRequest)
    {
        $purchaseOrderRequest->load('product', 'requester', 'approver', 'supplier');
        $suppliers = \App\Models\Supplier::where('is_active', true)->get();
        return view('storekeeper.purchase-order-requests-show', compact('purchaseOrderRequest', 'suppliers'));
    }

    public function approve(Request $request, PurchaseOrderRequest $purchaseOrderRequest)
    {
        if ($purchaseOrderRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been processed');
        }

        $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'admin_notes' => 'nullable|string',
        ]);

        $purchaseOrderRequest->update([
            'status' => 'approved',
            'supplier_id' => $request->supplier_id,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'admin_notes' => $request->admin_notes,
        ]);

        return back()->with('success', 'Purchase order request approved');
    }

    public function reject(Request $request, PurchaseOrderRequest $purchaseOrderRequest)
    {
        if ($purchaseOrderRequest->status !== 'pending') {
            return back()->with('error', 'This request has already been processed');
        }

        $request->validate([
            'rejection_reason' => 'required|string',
        ]);

        $purchaseOrderRequest->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return back()->with('success', 'Purchase order request rejected');
    }

    public function process(PurchaseOrderRequest $purchaseOrderRequest)
    {
        if ($purchaseOrderRequest->status !== 'approved') {
            return back()->with('error', 'This request must be approved first');
        }

        // Create actual purchase order from request
        // This would integrate with your existing PurchaseOrder system
        $purchaseOrderRequest->update([
            'status' => 'processed',
            'processed_at' => now(),
        ]);

        return back()->with('success', 'Purchase order processed successfully');
    }

    public function receive(Request $request, PurchaseOrderRequest $purchaseOrderRequest)
    {
        if (!in_array($purchaseOrderRequest->status, ['pending', 'approved'])) {
            return back()->with('error', 'This request cannot be received in its current status');
        }

        $request->validate([
            'received_quantity' => 'required|numeric|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        if ($request->received_quantity > $purchaseOrderRequest->requested_quantity) {
            return back()->with('error', 'Received quantity cannot exceed requested quantity');
        }

        \DB::beginTransaction();
        try {
            // Create GRN for this request
            $grnNumber = 'GRN-POR-' . date('YmdHis');

            $grn = \App\Models\GoodsReceivedNote::create([
                'grn_number' => $grnNumber,
                'supplier_id' => $purchaseOrderRequest->supplier_id ?? null,
                'purchase_order_id' => null,
                'received_date' => now(),
                'notes' => $request->notes ?? 'Received from Purchase Order Request: ' . $purchaseOrderRequest->request_number,
                'total' => 0,
                'status' => 'received',
            ]);

            $product = $purchaseOrderRequest->product;
            $unitPrice = $product->cost_price ?? 0;
            $total = $request->received_quantity * $unitPrice;

            \App\Models\GrnItem::create([
                'goods_received_note_id' => $grn->id,
                'product_id' => $product->id,
                'quantity_ordered' => $purchaseOrderRequest->requested_quantity,
                'quantity_received' => $request->received_quantity,
                'quantity_accepted' => $request->received_quantity,
                'quantity_rejected' => 0,
                'unit_cost' => $unitPrice,
                'total_cost' => $total,
            ]);

            // Update product stock
            $product->increment('quantity', $request->received_quantity);

            // Update GRN total
            $grn->update(['total' => $total]);

            // Update request status
            $purchaseOrderRequest->update([
                'status' => 'received',
                'processed_at' => now(),
            ]);

            \DB::commit();

            return redirect()->route('storekeeper.purchase-order-requests.show', $purchaseOrderRequest)
                ->with('success', 'Products received successfully! Stock updated and GRN created.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Failed to receive products: ' . $e->getMessage());
        }
    }
}
