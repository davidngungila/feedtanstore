<?php

namespace App\Http\Controllers;

use App\Models\OnlineOrder;
use App\Models\Customer;
use App\Models\DeliveryRider;
use App\Models\RiderDispatchBatch;
use App\Models\RiderDispatchRequest;
use App\Models\StoreSetting;
use App\Models\User;
use App\Support\Geo;
use App\Services\Tracking\TrackingService;
use App\Services\Notifications\NotificationService;
use App\Services\MessagingService;
use App\Mail\RiderWelcomeEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class MarketingOfficerController extends Controller
{
    public function __construct(
        private readonly NotificationService $notifications,
    ) {
    }
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
        $orders = OnlineOrder::with('rider', 'customer', 'riderDispatchRequests.dispatchBatch.requests.order')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('marketing-officer.orders', compact('orders'));
    }

    public function orderDetails($id)
    {
        $order = OnlineOrder::with('rider', 'customer', 'items.product')->findOrFail($id);
        $dispatchRequest = RiderDispatchRequest::with('acceptedRider')
            ->where('online_order_id', $order->id)
            ->latest()
            ->first();

        // All orders currently needing rider assignment (same eligibility as the bulk dispatch page)
        $bulkOrders = $this->ordersNeedingAssignment();

        $riders = DeliveryRider::where('is_active', true)->orderBy('name')->get();

        $store = StoreSetting::first();
        $storeLat = $store->store_latitude ?? -3.3869;
        $storeLng = $store->store_longitude ?? 36.6883;

        $defaultRadius = 5.0;

        // Auto-bulk: pre-check this order plus the other orders whose customers
        // are within the cluster radius of this delivery point. Orders without
        // coordinates cannot be grouped, so they are never pre-selected.
        $nearbyIds = [];
        if ($order->delivery_latitude !== null && $order->delivery_longitude !== null) {
            foreach ($bulkOrders->filter(fn ($o) => $o->delivery_latitude !== null && $o->delivery_longitude !== null) as $bulkOrder) {
                $km = Geo::haversine(
                    (float) $order->delivery_latitude,
                    (float) $order->delivery_longitude,
                    (float) $bulkOrder->delivery_latitude,
                    (float) $bulkOrder->delivery_longitude
                ) / 1000;

                if ($km <= $defaultRadius) {
                    $nearbyIds[] = $bulkOrder->id;
                }
            }
        }

        $ordersForMap = $bulkOrders
            ->filter(fn ($o) => $o->delivery_latitude !== null && $o->delivery_longitude !== null)
            ->map(fn ($o) => [
                'id' => $o->id,
                'order_number' => $o->order_number,
                'customer_name' => $o->customer_name,
                'address' => $o->delivery_address,
                'lat' => (float) $o->delivery_latitude,
                'lng' => (float) $o->delivery_longitude,
                'total' => (float) $o->total,
            ])->values();

        return view('marketing-officer.order-details', compact(
            'order', 'dispatchRequest', 'bulkOrders', 'riders', 'storeLat', 'storeLng',
            'defaultRadius', 'nearbyIds', 'ordersForMap'
        ));
    }

    /**
     * Orders in the "needs rider assignment" stage: fully packaged and
     * reconciled, not yet assigned to any rider. This matches the stage that
     * enables the single dispatch request flow.
     */
    private function ordersNeedingAssignment()
    {
        return OnlineOrder::with('customer', 'items')
            ->whereNull('delivery_rider_id')
            ->where('packaging_status', 'completed')
            ->where('reconciliation_status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();
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

    public function sendDispatchRequest($id)
    {
        $order = OnlineOrder::findOrFail($id);

        if ($order->packaging_status !== 'completed') {
            return redirect()->back()->with('error', 'Cannot send dispatch request until packaging is completed');
        }

        if ($order->reconciliation_status !== 'completed') {
            return redirect()->back()->with('error', 'Cannot send dispatch request until reconciliation is completed');
        }

        if ($order->delivery_rider_id) {
            return redirect()->back()->with('error', 'A rider is already assigned to this order');
        }

        $alreadyInBatch = RiderDispatchRequest::where('online_order_id', $order->id)
            ->whereNotNull('dispatch_batch_id')
            ->where('status', 'pending')
            ->exists();

        if ($alreadyInBatch) {
            return redirect()->back()->with('error', 'This order is already part of a bulk dispatch batch. Wait for the batch to be accepted or expire.');
        }

        // Cancel any previous pending request for this order (single or batch)
        RiderDispatchRequest::where('online_order_id', $order->id)
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);

        RiderDispatchRequest::create([
            'online_order_id' => $order->id,
            'status' => 'pending',
            'expires_at' => now()->addMinutes(30),
        ]);

        $dispatch = RiderDispatchRequest::where('online_order_id', $order->id)
            ->where('status', 'pending')
            ->latest()
            ->first();

        if ($dispatch) {
            $this->notifications->sendDispatchRequestNotification($dispatch);
        }

        return redirect()->back()->with('success', 'Dispatch request sent to all available riders. Waiting for a rider to accept.');
    }

    public function cancelDispatchRequest($id)
    {
        $order = OnlineOrder::findOrFail($id);

        RiderDispatchRequest::where('online_order_id', $order->id)
            ->where('status', 'pending')
            ->update(['status' => 'cancelled']);

        return redirect()->back()->with('success', 'Dispatch request cancelled. You can send a new one anytime.');
    }

    /**
     * Bulk dispatch builder: pick several orders, group them by customer
     * location proximity, and send them out as one (or more) batches.
     */
    public function bulkDispatch(Request $request)
    {
        $orders = $this->ordersNeedingAssignment();

        $riders = DeliveryRider::where('is_active', true)->orderBy('name')->get();

        $store = StoreSetting::first();
        $storeLat = $store->store_latitude ?? -3.3869;
        $storeLng = $store->store_longitude ?? 36.6883;

        // Default radius used for clustering nearby customers (km)
        $defaultRadius = 5.0;

        // Orders pre-selected from the orders page (?order_ids[]=...)
        $preselected = collect($request->query('order_ids', []))->map(fn ($id) => (int) $id)->all();

        // Map data precomputed so the view can pass a plain array to @json.
        // Only orders with coordinates are plotted; the rest dispatch individually.
        $ordersForMap = $orders
            ->filter(fn ($o) => $o->delivery_latitude !== null && $o->delivery_longitude !== null)
            ->map(fn ($o) => [
                'id' => $o->id,
                'order_number' => $o->order_number,
                'customer_name' => $o->customer_name,
                'address' => $o->delivery_address,
                'lat' => (float) $o->delivery_latitude,
                'lng' => (float) $o->delivery_longitude,
                'total' => (float) $o->total,
            ])->values();

        return view('marketing-officer.bulk-dispatch', compact(
            'orders', 'riders', 'store', 'storeLat', 'storeLng', 'defaultRadius', 'preselected', 'ordersForMap'
        ));
    }

    /**
     * Create bulk dispatch batches: cluster selected orders by proximity and
     * broadcast each cluster as a single batch to riders.
     */
    public function sendBulkDispatch(Request $request)
    {
        $validated = $request->validate([
            'order_ids' => 'required|array|min:1',
            'order_ids.*' => 'exists:online_orders,id',
            'radius_km' => 'nullable|numeric|min:1|max:100',
            'rider_id' => 'nullable|exists:delivery_riders,id',
            'notes' => 'nullable|string|max:500',
        ]);

        $orders = OnlineOrder::whereIn('id', $validated['order_ids'])->get();

        $eligible = $orders->filter(function ($order) {
            return $order->delivery_rider_id === null
                && $order->packaging_status === 'completed'
                && $order->reconciliation_status === 'completed';
        });

        if ($eligible->isEmpty()) {
            return redirect()->back()->with('error', 'None of the selected orders are eligible for dispatch.');
        }

        $radiusKm = (float) ($validated['radius_km'] ?? 5);
        $targetRiderId = $validated['rider_id'] ?? null;

        // Only orders with delivery coordinates can be location-grouped into a
        // bulk batch. A batch is only created when it groups two or more orders;
        // lone orders (no coordinates or too far from any other selected order)
        // are excluded and must be dispatched individually.
        $withCoordinates = $eligible->filter(fn ($o) => $o->delivery_latitude !== null && $o->delivery_longitude !== null);

        $clusters = array_values(array_filter(
            $this->clusterOrders($withCoordinates, $radiusKm),
            fn ($cluster) => count($cluster['orders']) > 1
        ));

        if (empty($clusters)) {
            return redirect()->back()->with('error', 'No bulk batch was created: none of the selected orders could be grouped with at least one other order by location. Dispatch the selected orders individually instead.');
        }

        $created = [];

        DB::transaction(function () use ($clusters, $targetRiderId, $validated, &$created) {
            foreach ($clusters as $cluster) {
                $batch = RiderDispatchBatch::create([
                    'status' => 'pending',
                    'created_by' => Auth::id(),
                    'target_rider_id' => $targetRiderId,
                    'expires_at' => now()->addMinutes(5),
                    'notes' => $validated['notes'] ?? null,
                ]);

                foreach ($cluster['orders'] as $order) {
                    // Cancel any previous pending dispatch request for this order
                    RiderDispatchRequest::where('online_order_id', $order->id)
                        ->where('status', 'pending')
                        ->update(['status' => 'cancelled']);

                    RiderDispatchRequest::create([
                        'online_order_id' => $order->id,
                        'dispatch_batch_id' => $batch->id,
                        'status' => 'pending',
                        'expires_at' => $batch->expires_at,
                    ]);
                }

                $created[] = $batch;
            }
        });

        foreach ($created as $batch) {
            $this->notifications->sendDispatchBatchNotification($batch);
        }

        $orderCount = collect($clusters)->sum(fn ($c) => count($c['orders']));

        return redirect()->route('marketing-officer.dispatch-batches')
            ->with('success', "Bulk dispatch created: ".count($created)." batch(es) covering {$orderCount} order(s). Riders can now accept.");
    }

    /**
     * List all dispatch batches so the marketing officer can monitor acceptance.
     */
    public function batches()
    {
        $batches = RiderDispatchBatch::with('requests.order', 'acceptedRider', 'targetRider', 'creator')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('marketing-officer.batches', compact('batches'));
    }

    /**
     * Show the details of a single dispatch batch, including its orders,
     * rider responses and a delivery map.
     */
    public function batchDetails($id)
    {
        $batch = RiderDispatchBatch::with('requests.order', 'acceptedRider', 'targetRider', 'creator', 'responses.rider')
            ->findOrFail($id);

        $orders = $batch->requests->pluck('order')->filter();

        $store = StoreSetting::first();
        $storeLat = $store->store_latitude ?? -3.3869;
        $storeLng = $store->store_longitude ?? 36.6883;

        $ordersForMap = $orders
            ->filter(fn ($o) => $o->delivery_latitude !== null && $o->delivery_longitude !== null)
            ->map(fn ($o) => [
                'id' => $o->id,
                'order_number' => $o->order_number,
                'customer_name' => $o->customer_name,
                'address' => $o->delivery_address,
                'lat' => (float) $o->delivery_latitude,
                'lng' => (float) $o->delivery_longitude,
                'total' => (float) $o->total,
            ])->values();

        return view('marketing-officer.batch-details', compact('batch', 'orders', 'storeLat', 'storeLng', 'ordersForMap'));
    }

    /**
     * Cancel a pending dispatch batch: release all of its orders back to the
     * "needing rider assignment" pool and notify the targeted riders.
     */
    public function cancelBatch($id)
    {
        $batch = RiderDispatchBatch::with('requests')->findOrFail($id);

        if ($batch->status !== 'pending') {
            return redirect()->back()->with('error', 'Only pending batches can be cancelled.');
        }

        DB::transaction(function () use ($batch) {
            $batch->update(['status' => 'cancelled']);

            RiderDispatchRequest::where('dispatch_batch_id', $batch->id)
                ->where('status', 'pending')
                ->update(['status' => 'cancelled']);
        });

        $this->notifications->sendDispatchBatchCancelledNotification($batch);

        return redirect()->route('marketing-officer.dispatch-batches')
            ->with('success', "Batch #{$batch->id} cancelled. Its orders are available for a new dispatch.");
    }

    /**
     * Greedy proximity clustering of orders (centroid-based).
     *
     * @param  \Illuminate\Support\Collection<int, OnlineOrder>  $orders
     * @return array<int, array{centroid: array{0: float, 1: float}, orders: array<int, OnlineOrder>}>
     */
    private function clusterOrders($orders, float $radiusKm): array
    {
        $clusters = [];

        foreach ($orders as $order) {
            $lat = (float) $order->delivery_latitude;
            $lng = (float) $order->delivery_longitude;

            $placed = false;

            foreach ($clusters as $index => $cluster) {
                $distance = Geo::haversine(
                    $cluster['centroid'][0],
                    $cluster['centroid'][1],
                    $lat,
                    $lng
                ) / 1000;

                if ($distance <= $radiusKm) {
                    $clusterOrders = $cluster['orders'];
                    $clusterOrders[] = $order;

                    $avgLat = collect($clusterOrders)->avg(fn ($o) => (float) $o->delivery_latitude);
                    $avgLng = collect($clusterOrders)->avg(fn ($o) => (float) $o->delivery_longitude);

                    $clusters[$index] = [
                        'centroid' => [$avgLat, $avgLng],
                        'orders' => $clusterOrders,
                    ];

                    $placed = true;
                    break;
                }
            }

            if (! $placed) {
                $clusters[] = [
                    'centroid' => [$lat, $lng],
                    'orders' => [$order],
                ];
            }
        }

        return $clusters;
    }

    public function dispatchStatus($id)
    {
        $order = OnlineOrder::findOrFail($id);
        $dispatchRequest = RiderDispatchRequest::with('acceptedRider')
            ->where('online_order_id', $order->id)
            ->latest()
            ->first();

        return response()->json([
            'rider' => $order->rider ? [
                'id' => $order->rider->id,
                'name' => $order->rider->name,
                'phone' => $order->rider->phone,
            ] : null,
            'dispatch_request' => $dispatchRequest ? [
                'id' => $dispatchRequest->id,
                'status' => $dispatchRequest->status,
                'accepted_rider' => $dispatchRequest->acceptedRider ? $dispatchRequest->acceptedRider->name : null,
            ] : null,
        ]);
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

    public function liveTracking($id)
    {
        $order = OnlineOrder::with(['rider', 'rider.latestLocation', 'statusHistory'])->findOrFail($id);

        $session = app(TrackingService::class)->activeSessionForOrder($order);

        if (! $session) {
            return redirect()
                ->route('marketing-officer.order-details', $order->id)
                ->with('error', 'No active delivery session for this order yet.');
        }

        $payload = app(TrackingService::class)->sessionPayload($session);

        // Reverb client configuration for Laravel Echo (matches env / production reverse proxy)
        $reverb = [
            'key' => config('reverb.apps.apps.0.key'),
            'host' => config('reverb.servers.reverb.hostname') ?: config('reverb.apps.apps.0.options.host'),
            'port' => config('reverb.apps.apps.0.options.port'),
            'scheme' => config('reverb.apps.apps.0.options.scheme'),
            'useTLS' => (bool) config('reverb.apps.apps.0.options.useTLS'),
        ];

        $storeSettings = \App\Models\StoreSetting::first();

        return view('marketing-officer.live-tracking', compact('order', 'session', 'payload', 'reverb', 'storeSettings'));
    }

    public function recalculateRoute($id)
    {
        $order = OnlineOrder::with('rider')->findOrFail($id);

        $session = app(TrackingService::class)->activeSessionForOrder($order);

        if (! $session) {
            return response()->json(['message' => 'No active delivery session'], 404);
        }

        return response()->json([
            'route' => app(TrackingService::class)->recalculateRoute($session),
            'recalculated_at' => now()->toIso8601String(),
        ]);
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

    public function createRider()
    {
        return view('marketing-officer.riders-create');
    }

    public function storeRider(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:255',
            'vehicle_type' => 'nullable|string|max:255',
            'vehicle_plate' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        $generatedPassword = Str::random(12);

        \DB::beginTransaction();
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($generatedPassword),
                'phone' => $request->phone,
                'role' => 'rider',
            ]);

            $rider = DeliveryRider::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'vehicle_type' => $request->vehicle_type,
                'vehicle_plate' => $request->vehicle_plate,
                'is_active' => $request->has('is_active'),
                'user_id' => $user->id,
            ]);

            \DB::commit();

            try {
                Mail::to($user->email)->send(new RiderWelcomeEmail($rider->load('user'), $generatedPassword));
            } catch (\Exception $e) {
                \Log::error('Failed to send welcome email to rider', [
                    'rider_id' => $rider->id,
                    'email' => $user->email,
                    'error' => $e->getMessage(),
                ]);
            }

            try {
                $messagingService = new MessagingService();
                $smsText = "Welcome to Feedtan Delivery Team, {$rider->name}! Your account is ready. Login with email: {$user->email} and password: {$generatedPassword}. Download the rider app to start delivering. Change password after first login.";
                $messagingService->sendSms($request->phone, $smsText);
            } catch (\Exception $e) {
                \Log::error('Failed to send welcome SMS to rider', [
                    'rider_id' => $rider->id,
                    'phone' => $request->phone,
                    'error' => $e->getMessage(),
                ]);
            }

            return redirect()->route('marketing-officer.riders')->with('success', 'Delivery Rider created successfully! Welcome email and SMS sent with login credentials.');
        } catch (\Exception $e) {
            \DB::rollBack();
            return back()->with('error', 'Failed to create rider: ' . $e->getMessage());
        }
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
