<?php

namespace App\Http\Controllers;

use App\Models\OnlineOrder;
use App\Models\DeliveryRider;
use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class RiderDashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;
        
        if (!$rider) {
            abort(403, 'You are not authorized as a delivery rider.');
        }

        // Get rider's orders
        $assignedOrders = OnlineOrder::with(['items'])
            ->where('delivery_rider_id', $rider->id)
            ->whereIn('status', ['out_for_delivery', 'ready'])
            ->latest()
            ->get();

        $deliveredOrders = OnlineOrder::with(['items'])
            ->where('delivery_rider_id', $rider->id)
            ->where('status', 'delivered')
            ->latest()
            ->limit(10)
            ->get();

        $settings = StoreSetting::firstOrCreate();
        $storeLat = $settings->store_latitude ?? -3.3869;
        $storeLng = $settings->store_longitude ?? 36.6883;

        $routes = [];
        if ($settings->openrouteservice_api_key) {
            foreach ($assignedOrders as $order) {
                if ($order->delivery_latitude && $order->delivery_longitude) {
                    try {
                        $response = Http::withHeaders([
                            'Authorization' => $settings->openrouteservice_api_key,
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
                        // Ignore
                    }
                }
            }
        }

        return view('rider.dashboard', compact('rider', 'assignedOrders', 'deliveredOrders', 'storeLat', 'storeLng', 'routes'));
    }

    public function myOrders()
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;
        
        if (!$rider) {
            abort(403, 'You are not authorized as a delivery rider.');
        }

        $orders = OnlineOrder::with(['items'])
            ->where('delivery_rider_id', $rider->id)
            ->latest()
            ->paginate(20);

        return view('rider.my-orders', compact('rider', 'orders'));
    }

    public function todayOrders()
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;
        
        if (!$rider) {
            abort(403, 'You are not authorized as a delivery rider.');
        }

        $title = "Today's Deliveries";
        $orders = OnlineOrder::with(['items'])
            ->where('delivery_rider_id', $rider->id)
            ->whereDate('created_at', Carbon::today())
            ->latest()
            ->paginate(20);

        return view('rider.orders-list', compact('rider', 'orders', 'title'));
    }

    public function assignedOrders()
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;
        
        if (!$rider) {
            abort(403, 'You are not authorized as a delivery rider.');
        }

        $title = "Assigned Orders";
        $orders = OnlineOrder::with(['items'])
            ->where('delivery_rider_id', $rider->id)
            ->where('status', 'ready')
            ->latest()
            ->paginate(20);

        return view('rider.orders-list', compact('rider', 'orders', 'title'));
    }

    public function transitOrders()
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;
        
        if (!$rider) {
            abort(403, 'You are not authorized as a delivery rider.');
        }

        $title = "In Transit";
        $orders = OnlineOrder::with(['items'])
            ->where('delivery_rider_id', $rider->id)
            ->where('status', 'out_for_delivery')
            ->latest()
            ->paginate(20);

        return view('rider.orders-list', compact('rider', 'orders', 'title'));
    }

    public function deliveredOrders()
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;
        
        if (!$rider) {
            abort(403, 'You are not authorized as a delivery rider.');
        }

        $title = "Delivered";
        $orders = OnlineOrder::with(['items'])
            ->where('delivery_rider_id', $rider->id)
            ->where('status', 'delivered')
            ->latest()
            ->paginate(20);

        return view('rider.orders-list', compact('rider', 'orders', 'title'));
    }

    public function failedOrders()
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;
        
        if (!$rider) {
            abort(403, 'You are not authorized as a delivery rider.');
        }

        $title = "Failed Deliveries";
        $orders = OnlineOrder::with(['items'])
            ->where('delivery_rider_id', $rider->id)
            ->where('status', 'failed')
            ->latest()
            ->paginate(20);

        return view('rider.orders-list', compact('rider', 'orders', 'title'));
    }

    public function routePlanner()
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;
        
        if (!$rider) {
            abort(403, 'You are not authorized as a delivery rider.');
        }

        $settings = StoreSetting::firstOrCreate();
        $assignedOrders = OnlineOrder::with(['items'])
            ->where('delivery_rider_id', $rider->id)
            ->whereIn('status', ['out_for_delivery', 'ready'])
            ->latest()
            ->get();

        return view('rider.route-planner', compact('rider', 'settings', 'assignedOrders'));
    }

    public function liveLocation()
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;
        
        if (!$rider) {
            abort(403, 'You are not authorized as a delivery rider.');
        }

        return view('rider.live-location', compact('rider'));
    }

    public function codPayments()
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;
        
        if (!$rider) {
            abort(403, 'You are not authorized as a delivery rider.');
        }

        $orders = OnlineOrder::with(['items'])
            ->where('delivery_rider_id', $rider->id)
            ->where('payment_method', 'cash_on_delivery')
            ->latest()
            ->paginate(20);

        return view('rider.cod-payments', compact('rider', 'orders'));
    }

    public function paymentHistory()
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;
        
        if (!$rider) {
            abort(403, 'You are not authorized as a delivery rider.');
        }

        $orders = OnlineOrder::with(['items'])
            ->where('delivery_rider_id', $rider->id)
            ->whereNotNull('payment_status')
            ->latest()
            ->paginate(20);

        return view('rider.payment-history', compact('rider', 'orders'));
    }

    public function customers()
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;
        
        if (!$rider) {
            abort(403, 'You are not authorized as a delivery rider.');
        }

        $customers = OnlineOrder::select('customer_name', 'customer_phone', 'customer_email')
            ->where('delivery_rider_id', $rider->id)
            ->distinct()
            ->latest()
            ->paginate(20);

        return view('rider.customers', compact('rider', 'customers'));
    }

    public function notifications()
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;
        
        if (!$rider) {
            abort(403, 'You are not authorized as a delivery rider.');
        }

        return view('rider.notifications', compact('rider'));
    }

    public function showOrder(OnlineOrder $order)
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;
        
        if (!$rider || $order->delivery_rider_id !== $rider->id) {
            abort(403, 'You are not authorized to view this order.');
        }

        $order->load(['items.product']);
        return view('rider.order-show', compact('order'));
    }

    public function updateOrderStatus(Request $request, OnlineOrder $order)
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;
        
        if (!$rider || $order->delivery_rider_id !== $rider->id) {
            abort(403, 'You are not authorized to update this order.');
        }

        $request->validate([
            'status' => 'required|in:out_for_delivery,delivered',
            'delivery_code_input' => 'nullable|string'
        ]);

        // Validate delivery code if marking as delivered
        if ($request->status === 'delivered' && trim($request->delivery_code_input) !== $order->delivery_code) {
            return back()->withErrors(['delivery_code_input' => 'Invalid delivery code. Please enter the correct 4-digit code.'])->withInput();
        }

        $order->update(['status' => $request->status]);

        // Log status change
        \App\Models\OnlineOrderStatusHistory::create([
            'online_order_id' => $order->id,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'notes' => 'Status updated by rider: ' . $rider->name,
            'user_id' => $user->id
        ]);

        return back()->with('success', 'Order status updated successfully!');
    }
}
