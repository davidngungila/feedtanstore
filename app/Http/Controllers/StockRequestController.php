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

    public function show(StockRequest $stockRequest)
    {
        $stockRequest->load(['user', 'onlineOrder', 'items.product', 'approvedBy']);
        
        return view('stock-requests.show', compact('stockRequest'));
    }
}
