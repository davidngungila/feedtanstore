<?php

namespace App\Http\Controllers;

use App\Models\OnlineOrder;
use App\Models\DeliveryRider;
use App\Models\StoreSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
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

        // Get rider's orders - include pending acceptance orders
        $assignedOrders = OnlineOrder::with(['items'])
            ->where('delivery_rider_id', $rider->id)
            ->whereIn('status', ['confirmed', 'ready', 'out_for_delivery'])
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
            ->whereIn('status', ['confirmed', 'ready', 'out_for_delivery'])
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

        return view('rider.route-planner', compact('rider', 'settings', 'assignedOrders', 'storeLat', 'storeLng', 'routes'));
    }

    public function liveLocation()
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;
        
        if (!$rider) {
            abort(403, 'You are not authorized as a delivery rider.');
        }

        $latestLocation = $rider->latest_location;
        $currentLat = $latestLocation->latitude ?? null;
        $currentLng = $latestLocation->longitude ?? null;

        return view('rider.live-location', compact('rider', 'currentLat', 'currentLng'));
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

        // Send thank you message when order is delivered
        $messageSent = false;
        if ($request->status === 'delivered') {
            try {
                // Check if SMS communication profile is configured
                $smsProfile = \App\Models\CommunicationProfile::where('type', 'sms')->where('is_active', true)->first();
                
                if (!$smsProfile || empty($smsProfile->sms_api_key)) {
                    Log::warning('SMS communication profile not configured or missing API key for order #' . $order->short_customer_reference);
                } else {
                    $messagingService = new \App\Services\MessagingService();
                    $customerPhone = $this->formatPhoneNumber($order->customer_phone);
                    $storeUrl = url('/');
                    $message = "Thank you for your order #{$order->short_customer_reference}! Your delivery has been completed successfully. We hope you enjoy your purchase. Order again at {$storeUrl} for more great products!";
                    
                    $result = $messagingService->sendSms($customerPhone, $message);
                    
                    if ($result['success']) {
                        $messageSent = true;
                        Log::info('Thank you message sent successfully for order #' . $order->short_customer_reference . ' to ' . $customerPhone);
                    } else {
                        Log::warning('Thank you message failed for order #' . $order->short_customer_reference . ': ' . json_encode($result['response']));
                    }
                }
            } catch (\Exception $e) {
                // Log error but don't fail the delivery update
                Log::error('Failed to send thank you message for order #' . $order->short_customer_reference . ': ' . $e->getMessage());
            }
        }

        $successMessage = 'Order status updated successfully!';
        if ($messageSent) {
            $successMessage .= ' Thank you message sent to customer.';
        } else if ($request->status === 'delivered') {
            $successMessage .= ' (SMS not configured - thank you message not sent)';
        }

        return back()->with('success', $successMessage);
    }

    public function acceptOrder(OnlineOrder $order)
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;
        
        if (!$rider || $order->delivery_rider_id !== $rider->id) {
            abort(403, 'You are not authorized to accept this order.');
        }
        
        $order->update([
            'rider_acceptance_status' => 'accepted',
            'rider_accepted_at' => now(),
            'status' => 'out_for_delivery'
        ]);
        
        // Log status change
        \App\Models\OnlineOrderStatusHistory::create([
            'online_order_id' => $order->id,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'notes' => 'Order accepted by rider: ' . $rider->name,
            'user_id' => $user->id
        ]);
        
        return back()->with('success', 'Order accepted successfully!');
    }
    
    public function rejectOrder(OnlineOrder $order)
    {
        $user = Auth::user();
        $rider = $user->deliveryRider;
        
        if (!$rider || $order->delivery_rider_id !== $rider->id) {
            abort(403, 'You are not authorized to reject this order.');
        }
        
        $order->update([
            'rider_acceptance_status' => 'rejected',
            'delivery_rider_id' => null,
            'status' => 'confirmed'
        ]);
        
        // Log status change
        \App\Models\OnlineOrderStatusHistory::create([
            'online_order_id' => $order->id,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'notes' => 'Order rejected by rider: ' . $rider->name . ', unassigned',
            'user_id' => $user->id
        ]);
        
        return back()->with('success', 'Order rejected successfully!');
    }
    
    private function formatPhoneNumber($phone)
    {
        // Remove any non-numeric characters
        $phone = preg_replace('/[^0-9]/', '', $phone);
        
        // If starts with 0, replace with 255
        if (strpos($phone, '0') === 0) {
            $phone = '255' . substr($phone, 1);
        }
        
        // If doesn't start with 255, add it
        if (strpos($phone, '255') !== 0) {
            $phone = '255' . $phone;
        }
        
        return $phone;
    }
}
