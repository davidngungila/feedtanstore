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
        $order = OnlineOrder::with('rider', 'customer', 'items')->findOrFail($id);
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
        $order->delivery_rider_id = $request->rider_id;
        $order->rider_acceptance_status = 'pending';
        $order->status = 'confirmed';
        $order->save();
        
        return redirect()->back()->with('success', 'Rider assigned successfully');
    }

    public function requestPackaging(Request $request, $id)
    {
        // Redirect to stock requests create page with the order pre-selected
        return redirect()->route('stock-requests.create')->with('online_order_id', $id);
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
