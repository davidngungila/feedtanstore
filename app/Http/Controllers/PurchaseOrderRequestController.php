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
        return view('purchase-order-requests.index', compact('requests'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        return view('purchase-order-requests.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'requested_quantity' => 'required|numeric|min:1',
            'estimated_cost' => 'nullable|numeric|min:0',
            'reason' => 'nullable|string',
        ]);

        PurchaseOrderRequest::create([
            'request_number' => PurchaseOrderRequest::generateRequestNumber(),
            'product_id' => $request->product_id,
            'requested_quantity' => $request->requested_quantity,
            'estimated_cost' => $request->estimated_cost,
            'reason' => $request->reason,
            'status' => 'pending',
            'requested_by' => Auth::id(),
        ]);

        return redirect()->route('purchase-order-requests.index')
            ->with('success', 'Purchase order request submitted successfully');
    }

    public function show(PurchaseOrderRequest $purchaseOrderRequest)
    {
        $purchaseOrderRequest->load('product', 'requester', 'approver', 'supplier');
        return view('purchase-order-requests.show', compact('purchaseOrderRequest'));
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
}
