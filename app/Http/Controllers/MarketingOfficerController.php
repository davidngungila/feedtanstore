<?php

namespace App\Http\Controllers;

use App\Models\OnlineOrder;
use App\Models\Customer;
use App\Models\DeliveryRider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MarketingOfficerController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get online order statistics
        $totalOrders = OnlineOrder::count();
        $pendingOrders = OnlineOrder::where('status', 'pending')->count();
        $confirmedOrders = OnlineOrder::where('status', 'confirmed')->count();
        $outForDeliveryOrders = OnlineOrder::where('status', 'out_for_delivery')->count();
        $deliveredOrders = OnlineOrder::where('status', 'delivered')->count();
        $cancelledOrders = OnlineOrder::where('status', 'cancelled')->count();
        
        // Total revenue from online orders
        $totalRevenue = OnlineOrder::where('status', '!=', 'cancelled')->sum('total');
        
        // Recent orders
        $recentOrders = OnlineOrder::with('rider')
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
        
        // Orders by status
        $ordersByStatus = OnlineOrder::select('status', \DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');
        
        // Available riders
        $availableRiders = DeliveryRider::where('is_active', true)->count();
        $totalRiders = DeliveryRider::count();
        
        return view('marketing-officer.dashboard', compact(
            'totalOrders',
            'pendingOrders',
            'confirmedOrders',
            'outForDeliveryOrders',
            'deliveredOrders',
            'cancelledOrders',
            'totalRevenue',
            'recentOrders',
            'ordersByStatus',
            'availableRiders',
            'totalRiders'
        ));
    }

    public function orders()
    {
        $orders = OnlineOrder::with('rider', 'customer')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('marketing-officer.orders', compact('orders'));
    }

    public function orderDetails($id)
    {
        $order = OnlineOrder::with('rider', 'customer', 'items.product')->findOrFail($id);
        return view('marketing-officer.order-details', compact('order'));
    }

    public function updateOrderStatus(Request $request, $id)
    {
        $order = OnlineOrder::findOrFail($id);
        $order->status = $request->status;
        $order->save();
        
        return redirect()->back()->with('success', 'Order status updated successfully');
    }

    public function assignRider(Request $request, $id)
    {
        $request->validate([
            'rider_id' => 'required|exists:delivery_riders,id',
        ]);

        $order = OnlineOrder::findOrFail($id);
        
        // Restrict rider assignment until packaging is complete
        if ($order->packaging_status !== 'completed') {
            return redirect()->back()->with('error', 'Cannot assign rider until packaging is completed');
        }
        
        // Restrict rider assignment until reconciliation is complete
        if ($order->reconciliation_status !== 'completed') {
            return redirect()->back()->with('error', 'Cannot assign rider until reconciliation is completed');
        }
        
        $order->delivery_rider_id = $request->rider_id;
        $order->rider_acceptance_status = 'pending';
        $order->status = 'confirmed';
        $order->save();
        
        return redirect()->back()->with('success', 'Rider assigned successfully');
    }

    public function completeReconciliation(Request $request, $id)
    {
        $order = OnlineOrder::findOrFail($id);
        
        // Ensure packaging is complete before reconciliation
        if ($order->packaging_status !== 'completed') {
            return redirect()->back()->with('error', 'Cannot complete reconciliation until packaging is completed');
        }
        
        // Mark reconciliation as complete
        $order->reconciliation_status = 'completed';
        $order->save();
        
        return redirect()->back()->with('success', 'Reconciliation completed successfully. You can now assign a rider.');
    }

    public function updatePackagingStatus(Request $request, $id)
    {
        $request->validate([
            'packaging_status' => 'required|in:pending,in_progress,completed',
        ]);

        $order = OnlineOrder::findOrFail($id);
        $order->packaging_status = $request->packaging_status;
        
        // If packaging is completed, update order status to confirmed
        if ($request->packaging_status === 'completed') {
            $order->status = 'confirmed';
        }
        
        $order->save();
        
        return redirect()->back()->with('success', 'Packaging status updated successfully');
    }

    public function verifyAndPackageItem(Request $request, $orderId, $itemId)
    {
        $request->validate([
            'barcode' => 'required|string',
        ]);

        $order = OnlineOrder::findOrFail($orderId);
        $item = $order->items()->findOrFail($itemId);
        
        // Find product by barcode
        $product = \App\Models\Product::where('barcode', $request->barcode)->orWhere('sku', $request->barcode)->first();
        
        if (!$product) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found with this barcode'
            ], 404);
        }
        
        // Verify if the scanned product matches the order item
        if ($product->id !== $item->product_id) {
            return response()->json([
                'success' => false,
                'message' => 'Scanned product does not match the ordered product',
                'expected_product' => $item->product->name,
                'scanned_product' => $product->name
            ], 400);
        }
        
        // Check if already fully packaged
        if ($item->packaged_quantity >= $item->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'All quantity for this item has already been packaged'
            ], 400);
        }
        
        // Increment packaged quantity
        $item->packaged_quantity += 1;
        
        // Check if fully packaged
        if ($item->packaged_quantity >= $item->quantity) {
            $item->is_packaged = true;
        }
        
        $item->save();
        
        // Check if all items are packaged
        $allPackaged = $order->items()->where('is_packaged', true)->count() === $order->items()->count();
        $packagedCount = $order->items()->where('is_packaged', true)->count();
        
        // Update packaging status based on progress
        if ($allPackaged) {
            $order->packaging_status = 'completed';
            $order->status = 'confirmed';
            $order->save();
        } elseif ($packagedCount > 0 && $order->packaging_status === 'pending') {
            $order->packaging_status = 'in_progress';
            $order->save();
        }
        
        return response()->json([
            'success' => true,
            'message' => 'Product verified and quantity incremented',
            'all_packaged' => $allPackaged,
            'packaged_count' => $packagedCount,
            'total_items' => $order->items()->count(),
            'item_packaged_quantity' => $item->packaged_quantity,
            'item_total_quantity' => $item->quantity,
            'item_fully_packaged' => $item->is_packaged
        ]);
    }

    public function trackDelivery($id)
    {
        $order = OnlineOrder::with(['rider', 'rider.latestLocation', 'statusHistory'])->findOrFail($id);
        
        // Get store settings for store location
        $storeSettings = \App\Models\StoreSetting::first();
        
        // Get store location
        $storeLat = $storeSettings->store_latitude ?? -3.3869;
        $storeLng = $storeSettings->store_longitude ?? 36.6883;
        
        // Fetch route from OpenRouteService API
        $route = null;
        if ($storeSettings->openrouteservice_api_key && $order->delivery_latitude && $order->delivery_longitude) {
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
                    $route = $response->json();
                }
            } catch (\Exception $e) {
                // Do nothing if route fails to load
            }
        }
        
        return view('marketing-officer.track-delivery', compact('order', 'storeSettings', 'storeLat', 'storeLng', 'route'));
    }

    public function customers()
    {
        $customers = Customer::orderBy('name')->paginate(20);
        return view('marketing-officer.customers', compact('customers'));
    }

    public function customerDetails($id)
    {
        $customer = Customer::with('onlineOrders')->findOrFail($id);
        return view('marketing-officer.customer-details', compact('customer'));
    }

    public function riders()
    {
        $riders = DeliveryRider::with('user')->orderBy('name')->paginate(20);
        return view('marketing-officer.riders', compact('riders'));
    }

    public function riderDetails($id)
    {
        $rider = DeliveryRider::with(['user', 'latestLocation', 'onlineOrders', 'reviews'])->findOrFail($id);
        $storeSettings = \App\Models\StoreSetting::first();
        
        // Get store location
        $storeLat = $storeSettings->store_latitude ?? -3.3869;
        $storeLng = $storeSettings->store_longitude ?? 36.6883;
        
        return view('marketing-officer.rider-details', compact('rider', 'storeSettings', 'storeLat', 'storeLng'));
    }
}
