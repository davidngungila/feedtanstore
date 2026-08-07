<?php

namespace App\Http\Controllers;

use App\Models\StockRequest;
use App\Models\StockRequestItem;
use App\Models\Product;
use App\Models\OnlineOrder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockRequestController extends Controller
{
    public function index()
    {
        $stockRequests = StockRequest::with(['user', 'onlineOrder', 'items.product'])
            ->where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        
        return view('stock-requests.index', compact('stockRequests'));
    }

    public function create()
    {
        $products = Product::with('category')->get();
        $onlineOrders = OnlineOrder::with('items')->where('status', 'confirmed')->get();
        $preSelectedOrderId = session('online_order_id');
        
        return view('stock-requests.create', compact('products', 'onlineOrders', 'preSelectedOrderId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'request_type' => 'required|in:online_order,store_use',
            'online_order_id' => 'nullable|required_if:request_type,online_order|exists:online_orders,id',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity_requested' => 'required|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        $stockRequest = StockRequest::create([
            'user_id' => Auth::id(),
            'online_order_id' => $request->request_type === 'online_order' ? $request->online_order_id : null,
            'request_type' => $request->request_type,
            'status' => 'pending',
            'notes' => $request->notes,
        ]);

        foreach ($request->products as $productData) {
            StockRequestItem::create([
                'stock_request_id' => $stockRequest->id,
                'product_id' => $productData['product_id'],
                'quantity_requested' => $productData['quantity_requested'],
                'quantity_approved' => 0,
                'notes' => $productData['notes'] ?? null,
            ]);
        }

        return redirect()->route('stock-requests.show', $stockRequest)
            ->with('success', 'Stock request submitted successfully.');
    }

    public function show($stockRequest)
    {
        $stockRequest = StockRequest::with(['user', 'onlineOrder', 'items.product', 'approvedBy'])->findOrFail($stockRequest);
        return view('stock-requests.show', compact('stockRequest'));
    }

    public function approve(Request $request, $stockRequest)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.quantity_approved' => 'required|integer|min:0',
        ]);

        $stockRequest = StockRequest::findOrFail($stockRequest);
        
        // Update stock request status
        $stockRequest->status = 'approved';
        $stockRequest->approved_by = Auth::id();
        $stockRequest->approved_at = now();
        $stockRequest->save();

        // Update item quantities and issue stock
        foreach ($request->items as $itemId => $itemData) {
            $stockRequestItem = StockRequestItem::findOrFail($itemId);
            $stockRequestItem->quantity_approved = $itemData['quantity_approved'];
            $stockRequestItem->save();

            // Issue stock if approved quantity > 0
            if ($itemData['quantity_approved'] > 0) {
                $product = Product::findOrFail($stockRequestItem->product_id);
                
                // Check if enough stock is available
                if ($product->quantity >= $itemData['quantity_approved']) {
                    // Create stock movement record
                    \App\Models\StockMovement::create([
                        'product_id' => $product->id,
                        'movement_type' => 'out',
                        'quantity' => $itemData['quantity_approved'],
                        'reference_type' => 'stock_request',
                        'reference_id' => $stockRequest->id,
                        'user_id' => Auth::id(),
                        'notes' => 'Issued for stock request ' . $stockRequest->request_number,
                    ]);

                    // Update product quantity
                    $product->quantity -= $itemData['quantity_approved'];
                    $product->save();
                }
            }
        }

        // Mark as completed if all items are issued
        $allIssued = $stockRequest->items()->where('quantity_approved', '>', 0)->count() > 0;
        if ($allIssued) {
            $stockRequest->status = 'completed';
            $stockRequest->save();
        }

        return redirect()->back()->with('success', 'Stock request approved and products issued successfully');
    }

    public function reject($stockRequest)
    {
        $stockRequest = StockRequest::findOrFail($stockRequest);
        $stockRequest->status = 'rejected';
        $stockRequest->approved_by = Auth::id();
        $stockRequest->approved_at = now();
        $stockRequest->save();

        return redirect()->back()->with('success', 'Stock request rejected successfully');
    }
}
