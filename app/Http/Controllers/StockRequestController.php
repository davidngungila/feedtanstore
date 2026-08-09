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
        $query = StockRequest::with(['user', 'onlineOrder', 'items.product'])
            ->orderBy('created_at', 'desc');
        
        // Marketing officers only see their own requests
        if (Auth::user()->role === 'marketing_officer') {
            $query->where('user_id', Auth::id());
        }
        
        // Storekeepers, admins, and managers see all requests
        $stockRequests = $query->paginate(20);
        
        return view('stock-requests.index', compact('stockRequests'));
    }

    public function create()
    {
        $products = Product::with('category')->get();
        $onlineOrders = OnlineOrder::with('items')->where('status', 'confirmed')->get();
        $preSelectedOrderId = session('online_order_id');
        $storeSettings = \App\Models\StoreSetting::first();
        
        // Get store location
        $storeLat = $storeSettings->store_latitude ?? -3.3869;
        $storeLng = $storeSettings->store_longitude ?? 36.6883;
        
        // Pre-fetch routes for all orders
        $routes = [];
        if ($storeSettings->openrouteservice_api_key) {
            foreach ($onlineOrders as $order) {
                if ($order->delivery_latitude && $order->delivery_longitude) {
                    try {
                        $response = \Illuminate\Support\Facades\Http::withHeaders([
                            'Authorization' => $storeSettings->openrouteservice_api_key,
                            'Content-Type' => 'application/json'
                        ])->post('https://api.openrouteservice.org/v2/directions/driving-car/geojson', [
                            'coordinates' => [
                                [$storeLng, $storeLat],
                                [$order->delivery_longitude, $order->delivery_latitude]
                            ]
                        ]);
                        
                        if ($response->successful()) {
                            $routes[$order->id] = $response->json();
                        }
                    } catch (\Exception $e) {
                        // Log error or ignore
                    }
                }
            }
        }
        
        return view('stock-requests.create', compact('products', 'onlineOrders', 'preSelectedOrderId', 'storeSettings', 'storeLat', 'storeLng', 'routes'));
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
        $stockRequest = StockRequest::findOrFail($stockRequest);
        
        // Update stock request status to approved (but don't issue stock yet)
        $stockRequest->status = 'approved';
        $stockRequest->approved_by = Auth::id();
        $stockRequest->approved_at = now();
        $stockRequest->save();

        return redirect()->back()->with('success', 'Stock request approved successfully. You can now issue the products.');
    }

    public function issue(Request $request, $stockRequest)
    {
        $request->validate([
            'items' => 'required|array',
            'items.*.quantity_issued' => 'required|integer|min:0',
        ]);

        $stockRequest = StockRequest::findOrFail($stockRequest);
        
        // Update item quantities and issue stock incrementally
        foreach ($request->items as $itemId => $itemData) {
            $stockRequestItem = StockRequestItem::findOrFail($itemId);
            $additionalQuantity = $itemData['quantity_issued'];
            
            // Only process if additional quantity > 0
            if ($additionalQuantity > 0) {
                $product = Product::findOrFail($stockRequestItem->product_id);
                
                // Check if enough stock is available
                if ($product->quantity >= $additionalQuantity) {
                    // Create stock movement record
                    \App\Models\StockMovement::create([
                        'product_id' => $product->id,
                        'movement_type' => 'out',
                        'quantity' => $additionalQuantity,
                        'reference_type' => 'stock_request',
                        'reference_id' => $stockRequest->id,
                        'user_id' => Auth::id(),
                        'notes' => 'Issued for stock request ' . $stockRequest->request_number,
                    ]);

                    // Update product quantity
                    $product->quantity -= $additionalQuantity;
                    $product->save();
                    
                    // Increment approved quantity (add to existing)
                    $stockRequestItem->quantity_approved += $additionalQuantity;
                    $stockRequestItem->save();
                }
            }
        }

        // Check if all requested items are fully issued
        $allFullyIssued = true;
        foreach ($stockRequest->items as $item) {
            if ($item->quantity_approved < $item->quantity_requested) {
                $allFullyIssued = false;
                break;
            }
        }
        
        if ($allFullyIssued) {
            $stockRequest->status = 'completed';
            $stockRequest->save();
        }

        return redirect()->back()->with('success', 'Products added to packaging successfully');
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
