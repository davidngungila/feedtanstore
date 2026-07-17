<?php

namespace App\Http\Controllers;

use App\Mail\OnlineOrderPlaced;
use App\Models\Customer;
use App\Models\OnlineOrder;
use App\Models\OnlineOrderItem;
use App\Models\OnlineOrderStatusHistory;
use App\Models\Product;
use App\Models\DeliveryRider;
use App\Models\AccountingEntry;
use App\Models\CommunicationProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Crypt;
use Dompdf\Dompdf;
use App\Services\FeedtanEcommercePaymentService;
use App\Services\MessagingService;

class OnlineOrderController extends Controller
{


    public function initiatePaymentForOrder(Request $request, $trackingIdentifier, FeedtanEcommercePaymentService $paymentService)
    {
        $order = $this->findOrderByIdentifier($trackingIdentifier);
        if (!$order) {
            abort(404);
        }
        $settings = \App\Models\StoreSetting::firstOrCreate();
        $baseUrl = $settings->store_url ?? config('app.url');
        $finalTrackingIdentifier = $order->order_number;

        if (($order->payment_method ?? 'cash') !== 'online') {
            return response()->json([
                'success' => false,
                'message' => 'This order is not set for online payment.'
            ], 400);
        }

        $phoneNumber = $this->normalizePhoneNumber($request->input('phone_number', $order->customer_phone));
        if (!$phoneNumber) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid phone number. Please use format: 255712345678.'
            ], 400);
        }

        try {
            $paymentResponse = $paymentService->initiatePayment($this->buildPaymentPayload($order, $phoneNumber, true));

            if (isset($paymentResponse['success']) && $paymentResponse['success']) {
                $this->syncOrderPaymentState($order, $paymentResponse['data'] ?? [], 'Payment initiated via FeedTan e-commerce API');
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $paymentResponse['message'] ?? 'Failed to initiate payment.',
                    'payment' => $paymentResponse,
                ], 422);
            }

            return response()->json([
                'success' => true,
                'order_number' => $order->order_number,
                'tracking_url' => $baseUrl . '/shop/tracking/' . $finalTrackingIdentifier,
                'pdf_url' => $baseUrl . '/shop/tracking/' . $finalTrackingIdentifier . '/pdf',
                'payment' => $paymentResponse
            ]);
        } catch (\Exception $e) {
            Log::error('FeedTan e-commerce payment initiation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to initiate payment. Please try again.'
            ], 500);
        }
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->input('search'));
        $statusFilter = $request->input('status', ['pending', 'confirmed', 'preparing', 'ready', 'out_for_delivery', 'delivered', 'cancelled']);
        if (!is_array($statusFilter)) {
            $statusFilter = [$statusFilter];
        }
        
        $orders = OnlineOrder::with(['items', 'rider', 'user'])
            ->whereIn('status', $statusFilter)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('order_number', 'like', '%' . $search . '%')
                        ->orWhere('customer_name', 'like', '%' . $search . '%')
                        ->orWhere('customer_phone', 'like', '%' . $search . '%')
                        ->orWhere('customer_email', 'like', '%' . $search . '%')
                        ->orWhere('delivery_address', 'like', '%' . $search . '%')
                        ->orWhere('payment_status', 'like', '%' . $search . '%')
                        ->orWhere('payment_method', 'like', '%' . $search . '%')
                        ->orWhereHas('rider', function ($riderQuery) use ($search) {
                            $riderQuery->where('name', 'like', '%' . $search . '%')
                                ->orWhere('phone', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', '%' . $search . '%');
                        });
                });
            })
            ->latest()
            ->paginate(20);
            
        $settings = \App\Models\StoreSetting::firstOrCreate();
        
        // Get store location (default to Arusha, Tanzania if not set)
        $storeLat = $settings->store_latitude ?? -3.3869; 
        $storeLng = $settings->store_longitude ?? 36.6883;
        
        $routes = [];
        $orderDistances = [];
        $orderDeliveryFees = [];
        
        if ($settings->openrouteservice_api_key) {
            foreach ($orders as $order) {
                if ($order->delivery_latitude && $order->delivery_longitude) {
                    try {
                        $response = \Illuminate\Support\Facades\Http::withHeaders([
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
                        // Log error or ignore
                    }
                }
            }
        }
        
        // Calculate distances and delivery fees for all orders
        foreach ($orders as $order) {
            if ($order->delivery_latitude && $order->delivery_longitude) {
                $distance = $settings->calculateDistance(
                    $storeLat,
                    $storeLng,
                    $order->delivery_latitude,
                    $order->delivery_longitude
                );
                $orderDistances[$order->id] = number_format($distance, 2) . ' km';
                
                // Calculate delivery fee (pass customer lat, lon, subtotal)
                $feeResult = $settings->calculateDeliveryFee($order->delivery_latitude, $order->delivery_longitude, $order->subtotal);
                if ($feeResult['fee'] == 0) {
                    $orderDeliveryFees[$order->id] = 'FREE';
                } else {
                    $orderDeliveryFees[$order->id] = 'TZS ' . number_format($feeResult['fee'], 0);
                }
            } else {
                $orderDistances[$order->id] = null;
                $orderDeliveryFees[$order->id] = null;
            }
        }

        $allStatuses = ['pending', 'confirmed', 'preparing', 'ready', 'out_for_delivery', 'delivered', 'cancelled'];
        return view('online.orders', compact('orders', 'storeLat', 'storeLng', 'routes', 'statusFilter', 'allStatuses', 'search', 'orderDistances', 'orderDeliveryFees'));
    }

    public function shop()
    {
        $query = Product::where('is_active', true)
            ->where('is_available_online', true)
            ->where('quantity', '>', 0)
            ->with(['category', 'brand', 'images']);

        $selectedCategory = null;
        if (request('category')) {
            $selectedCategory = \App\Models\Category::where('id', request('category'))
                ->orWhere('slug', request('category'))
                ->first();
            if ($selectedCategory) {
                $query->where('category_id', $selectedCategory->id);
            }
        }

        if (request('search')) {
            $searchTerm = request('search');
            $query->where('name', 'like', "%$searchTerm%");
        }

        $products = $query->latest()->paginate(20);

        $slides = \App\Models\CarouselSlide::where('is_active', true)
            ->orderBy('order')
            ->get();

        $categories = \App\Models\Category::where('is_active', true)->get();

        $settings = \App\Models\StoreSetting::firstOrCreate();

        return view('shop.index', compact('products', 'slides', 'categories', 'selectedCategory', 'settings'));
    }

    public function showProduct(Product $product)
    {
        $product->load(['category', 'brand', 'images']);
        $settings = \App\Models\StoreSetting::firstOrCreate();
        $categories = \App\Models\Category::where('is_active', true)->get();
        return view('shop.product', compact('product', 'settings', 'categories'));
    }

    public function checkout()
    {
        $settings = \App\Models\StoreSetting::firstOrCreate();
        $categories = \App\Models\Category::where('is_active', true)->get();
        return view('shop.checkout', compact('settings', 'categories'));
    }

    public function create()
    {
        $products = Product::where('is_available_online', true)->where('quantity', '>', 0)->get();
        $riders = DeliveryRider::where('is_active', true)->get();
        $customers = Customer::all();
        return view('online.orders-create', compact('products', 'riders', 'customers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'delivery_address' => 'required|string',
            'payment_method' => 'nullable|string',
            'delivery_fee' => 'nullable|numeric|min:0',
            'delivery_rider_id' => 'nullable|exists:delivery_riders,id',
            'promo_code' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        $subtotal = 0;
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $subtotal += $product->selling_price * $item['quantity'];
        }

        $deliveryFee = $request->delivery_fee ?? 0;
        [$discount, $promoError] = $this->resolveOnlinePromoDiscount($request, $subtotal);
        if ($promoError) {
            return back()->withErrors(['promo_code' => $promoError])->withInput();
        }

        $total = max(0, $subtotal + $deliveryFee - $discount);

        // First create the order without tracking token to get an ID
        $order = OnlineOrder::create([
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'delivery_code' => str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT),
            'customer_id' => $request->customer_id,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_email' => $request->customer_email,
            'delivery_address' => $request->delivery_address,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => $request->payment_method,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'delivery_fee' => $deliveryFee,
            'total' => $total,
            'delivery_rider_id' => $request->delivery_rider_id,
            'user_id' => Auth::id(),
            'notes' => $request->notes
        ]);

        // Now generate encrypted tracking token using the order ID
        $encryptedId = Crypt::encryptString($order->id);
        $trackingToken = rtrim(strtr(base64_encode($encryptedId), '+/', '-_'), '=');
        $order->update(['tracking_token' => $trackingToken]);

        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            OnlineOrderItem::create([
                'online_order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $product->selling_price,
                'total' => $product->selling_price * $item['quantity']
            ]);
        }

        // Log initial status
        OnlineOrderStatusHistory::create([
            'online_order_id' => $order->id,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'notes' => 'Order created',
            'user_id' => Auth::id()
        ]);

        return redirect()->route('online.orders')->with('success', 'Online Order created successfully!');
    }

    public function show(OnlineOrder $order)
    {
        $order->load(['items.product', 'rider', 'user']);
        $settings = \App\Models\StoreSetting::firstOrCreate();
        
        $distance = null;
        $formattedDistance = null;
        $formattedDeliveryFee = null;
        if ($order->delivery_latitude && $order->delivery_longitude) {
            $distance = $settings->calculateDistance(
                $settings->store_latitude ?? -3.3430,
                $settings->store_longitude ?? 37.3507,
                $order->delivery_latitude,
                $order->delivery_longitude
            );
            $formattedDistance = number_format($distance, 2) . ' km';
            
            // Calculate delivery fee (pass customer lat, lon, subtotal)
            $feeResult = $settings->calculateDeliveryFee($order->delivery_latitude, $order->delivery_longitude, $order->subtotal);
            if ($feeResult['fee'] == 0) {
                $formattedDeliveryFee = 'FREE';
            } else {
                $formattedDeliveryFee = 'TZS ' . number_format($feeResult['fee'], 0);
            }
        }
        
        $route = null;
        if ($settings->openrouteservice_api_key && $order->delivery_latitude && $order->delivery_longitude) {
            try {
                $storeLat = $settings->store_latitude ?? -3.3869;
                $storeLng = $settings->store_longitude ?? 36.6883;
                
                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => $settings->openrouteservice_api_key,
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
        
        return view('online.orders-show', compact('order', 'route', 'settings', 'distance', 'formattedDistance', 'formattedDeliveryFee'));
    }

    public function edit(OnlineOrder $order)
    {
        $products = Product::where('is_available_online', true)->get();
        $riders = DeliveryRider::where('is_active', true)->get();
        $customers = Customer::all();
        $order->load('items');
        return view('online.orders-edit', compact('order', 'products', 'riders', 'customers'));
    }

    public function update(Request $request, OnlineOrder $order)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'delivery_address' => 'required|string',
            'payment_method' => 'nullable|string',
            'delivery_fee' => 'nullable|numeric|min:0',
            'delivery_rider_id' => 'nullable|exists:delivery_riders,id',
            'promo_code' => 'nullable|string|max:50',
            'notes' => 'nullable|string',
            'items' => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        $subtotal = 0;
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $subtotal += $product->selling_price * $item['quantity'];
        }

        $deliveryFee = $request->delivery_fee ?? 0;
        [$discount, $promoError] = $this->resolveOnlinePromoDiscount($request, $subtotal, $order);
        if ($promoError) {
            return back()->withErrors(['promo_code' => $promoError])->withInput();
        }

        $total = max(0, $subtotal + $deliveryFee - $discount);

        $order->update([
            'customer_id' => $request->customer_id,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_email' => $request->customer_email,
            'delivery_address' => $request->delivery_address,
            'payment_method' => $request->payment_method,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'delivery_fee' => $deliveryFee,
            'total' => $total,
            'delivery_rider_id' => $request->delivery_rider_id,
            'notes' => $request->notes
        ]);

        // Delete old items
        $order->items()->delete();

        // Create new items
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            OnlineOrderItem::create([
                'online_order_id' => $order->id,
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
                'price' => $product->selling_price,
                'total' => $product->selling_price * $item['quantity']
            ]);
        }

        return redirect()->route('online.orders')->with('success', 'Online Order updated successfully!');
    }

    private function resolveOnlinePromoDiscount(Request $request, float $subtotal, ?OnlineOrder $order = null): array
    {
        $promoCode = strtoupper(trim((string) $request->input('promo_code')));
        if ($promoCode === '') {
            return [0, null];
        }

        if ($promoCode !== 'FEEDTAN5K') {
            return [0, 'Invalid promo code. Use FEEDTAN5K.'];
        }

        if ($subtotal < 30000) {
            return [0, 'FEEDTAN5K applies only on orders above TZS 30,000.'];
        }

        if ($order && (float) $order->discount >= 5000) {
            return [5000, null];
        }

        if (!$this->isFirstOnlineOrderForCustomer($request, $order)) {
            return [0, 'FEEDTAN5K is only valid for the first online order for this customer.'];
        }

        return [5000, null];
    }

    private function isFirstOnlineOrderForCustomer(Request $request, ?OnlineOrder $ignoreOrder = null): bool
    {
        $customerId = $request->filled('customer_id') ? (int) $request->input('customer_id') : null;
        $phone = trim((string) $request->input('customer_phone'));
        $email = strtolower(trim((string) $request->input('customer_email')));

        $existingOrderQuery = OnlineOrder::query();

        if ($ignoreOrder) {
            $existingOrderQuery->whereKeyNot($ignoreOrder->id);
        }

        $existingOrderQuery->where(function ($query) use ($customerId, $phone, $email) {
            if ($customerId) {
                $query->orWhere('customer_id', $customerId);
            }

            if ($phone !== '') {
                $query->orWhere('customer_phone', $phone);
            }

            if ($email !== '') {
                $query->orWhereRaw('LOWER(customer_email) = ?', [$email]);
            }
        });

        return !$existingOrderQuery->exists();
    }

    public function destroy(OnlineOrder $order)
    {
        $order->items()->delete();
        $order->delete();
        return redirect()->route('online.orders')->with('success', 'Online Order deleted successfully!');
    }

    public function updateStatus(Request $request, OnlineOrder $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,preparing,ready,out_for_delivery,delivered,cancelled',
            'payment_status' => 'nullable|in:pending,paid,failed',
            'notes' => 'nullable|string',
            'delivery_code_input' => 'nullable|string'
        ]);

        // Validate delivery code when marking as delivered
        if ($request->status === 'delivered' && $order->status !== 'delivered') {
            if (trim($request->delivery_code_input) !== $order->delivery_code) {
                return back()->withErrors(['delivery_code_input' => 'Invalid delivery code. Please enter the correct 4-digit code.'])->withInput();
            }
        }

        $oldStatus = $order->status;
        $oldPaymentStatus = $order->payment_status;

        $order->update([
            'status' => $request->status,
            'payment_status' => $request->payment_status ?? $order->payment_status
        ]);

        // Log status change
        $statusChangeNote = '';
        if ($oldStatus !== $order->status) {
            $statusChangeNote .= "Status changed from {$oldStatus} to {$order->status}";
        }
        if ($oldPaymentStatus !== $order->payment_status) {
            if ($statusChangeNote) {
                $statusChangeNote .= ' | ';
            }
            $statusChangeNote .= "Payment status changed from {$oldPaymentStatus} to {$order->payment_status}";
        }

        OnlineOrderStatusHistory::create([
            'online_order_id' => $order->id,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'notes' => $request->notes ?? $statusChangeNote,
            'user_id' => Auth::id()
        ]);

        // If order is delivered and paid, create accounting entries and reduce stock (only once)
        if ($order->status === 'delivered' && $order->payment_status === 'paid' && !$order->is_processed) {
            foreach ($order->items as $item) {
                $product = Product::find($item->product_id);
                if ($product) {
                    $product->update(['quantity' => max(0, $product->quantity - $item->quantity)]);
                }
            }

            $cashAccount = \App\Models\Account::where('name', 'Cash')->first();
            $salesAccount = \App\Models\Account::where('name', 'Sales')->first();

            $journalNumber = 'JE-ONLINE-' . date('Ymd') . '-' . str_pad(\App\Models\JournalEntry::count() + 1, 4, '0', STR_PAD_LEFT);

            $journalEntry = \App\Models\JournalEntry::create([
                'journal_number' => $journalNumber,
                'entry_date' => now(),
                'description' => 'Online Order: ' . $order->order_number,
                'reference_type' => OnlineOrder::class,
                'reference_id' => $order->id,
                'is_manual' => false,
            ]);

            AccountingEntry::create([
                'journal_entry_id' => $journalEntry->id,
                'reference_number' => $order->order_number,
                'reference_type' => OnlineOrder::class,
                'account' => 'Cash',
                'account_id' => $cashAccount?->id,
                'type' => 'debit',
                'amount' => $order->total,
                'description' => 'Online Order Payment'
            ]);

            AccountingEntry::create([
                'journal_entry_id' => $journalEntry->id,
                'reference_number' => $order->order_number,
                'reference_type' => OnlineOrder::class,
                'account' => 'Sales',
                'account_id' => $salesAccount?->id,
                'type' => 'credit',
                'amount' => $order->total,
                'description' => 'Online Order'
            ]);

            $order->update(['is_processed' => true]);
        }

        return back()->with('success', 'Order status updated successfully!');
    }

    public function assignRider(Request $request, OnlineOrder $order)
    {
        $request->validate([
            'delivery_rider_id' => 'required|exists:delivery_riders,id',
            'notes' => 'nullable|string'
        ]);

        $oldStatus = $order->status;
        $order->update([
            'delivery_rider_id' => $request->delivery_rider_id, 
            'status' => 'confirmed',
            'rider_acceptance_status' => 'pending'
        ]);

        // Log status change
        OnlineOrderStatusHistory::create([
            'online_order_id' => $order->id,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'notes' => $request->notes ?? "Rider assigned and acceptance pending",
            'user_id' => Auth::id()
        ]);

        return back()->with('success', 'Rider assigned successfully! Waiting for rider acceptance.');
    }

    public function downloadPDF(OnlineOrder $order)
    {
        $order->load(['items.product', 'rider', 'user']);
        $pdf = new Dompdf();
        $pdf->loadHtml(view('online.orders-pdf', compact('order'))->render());
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();
        return $pdf->stream($order->order_number . '.pdf');
    }

    public function calculateDeliveryFee(Request $request)
    {
        $request->validate([
            'delivery_latitude' => 'required|numeric',
            'delivery_longitude' => 'required|numeric',
            'subtotal' => 'required|numeric|min:0'
        ]);

        $settings = \App\Models\StoreSetting::firstOrCreate();
        $result = $settings->calculateDeliveryFee(
            (float) $request->delivery_latitude,
            (float) $request->delivery_longitude,
            (float) $request->subtotal
        );

        return response()->json([
            'success' => true,
            'delivery_fee' => $result['fee'],
            'distance' => $result['distance'],
            'formatted_delivery_fee' => 'TZS ' . number_format($result['fee'], 0),
            'formatted_distance' => number_format($result['distance'], 2) . ' km',
            'is_free' => $result['fee'] === 0
        ]);
    }

    public function placeOrder(Request $request, FeedtanEcommercePaymentService $paymentService)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|string|max:255',
            'customer_email' => 'nullable|email',
            'delivery_address' => 'required|string',
            'delivery_latitude' => 'nullable|numeric',
            'delivery_longitude' => 'nullable|numeric',
            'delivery_fee' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|in:cash,online,bank',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1'
        ]);

        $subtotal = 0;
        $orderItems = [];
        $cartItemsForMetadata = [];
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            if (!$product || $product->quantity < $item['quantity']) {
                return response()->json(['success' => false, 'message' => "Insufficient stock for {$product->name}"], 400);
            }
            $subtotal += $product->selling_price * $item['quantity'];
            $orderItems[] = [
                'product' => $product,
                'quantity' => $item['quantity'],
                'price' => $product->selling_price
            ];
            $cartItemsForMetadata[] = [
                'name' => $product->name,
                'quantity' => $item['quantity'],
                'price' => $product->selling_price
            ];
        }

        $deliveryFee = $request->delivery_fee ?? null;
        if ($deliveryFee === null && $request->delivery_latitude && $request->delivery_longitude) {
            $settings = \App\Models\StoreSetting::firstOrCreate();
            $result = $settings->calculateDeliveryFee(
                (float) $request->delivery_latitude,
                (float) $request->delivery_longitude,
                $subtotal
            );
            $deliveryFee = $result['fee'];
        }
        $deliveryFee = $deliveryFee ?? 0;
        $total = $subtotal + $deliveryFee;

        // First create the order without tracking token to get an ID
        $order = OnlineOrder::create([
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'delivery_code' => str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT),
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_email' => $request->customer_email,
            'delivery_address' => $request->delivery_address,
            'delivery_latitude' => $request->delivery_latitude,
            'delivery_longitude' => $request->delivery_longitude,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => $request->payment_method,
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'total' => $total,
            'notes' => 'Order placed from public shop'
        ]);

        // Now generate encrypted tracking token using the order ID
        $encryptedId = Crypt::encryptString($order->id);
        $trackingToken = rtrim(strtr(base64_encode($encryptedId), '+/', '-_'), '=');
        $order->update(['tracking_token' => $trackingToken]);

        foreach ($orderItems as $item) {
            OnlineOrderItem::create([
                'online_order_id' => $order->id,
                'product_id' => $item['product']->id,
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['price'] * $item['quantity']
            ]);
        }

        // Log initial status
        OnlineOrderStatusHistory::create([
            'online_order_id' => $order->id,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'notes' => 'Order placed from public shop'
        ]);

        // Dispatch notification job to queue
        \App\Jobs\SendOnlineOrderNotifications::dispatch($order);

        $paymentInitiated = false;
        $paymentMessage = null;

        if ($request->payment_method === 'online') {
            $normalizedPhone = $this->normalizePhoneNumber($request->customer_phone);
            if ($normalizedPhone) {
                // Dispatch payment initiation job to queue
                \App\Jobs\InitiateOnlineOrderPayment::dispatch($order, $normalizedPhone);
                $paymentInitiated = true;
                $paymentMessage = "Thank you! Your payment is being processed. Please check your phone to complete the payment.";
            } else {
                $paymentMessage = 'Invalid phone number. Please use format: 255712345678.';
            }
        }

        $settings = \App\Models\StoreSetting::firstOrCreate();
        $baseUrl = $settings->store_url ?? config('app.url');

        $trackingIdentifier = $order->order_number;
        return response()->json([
            'success' => true,
            'order_number' => $order->order_number,
            'tracking_url' => $baseUrl . '/shop/tracking/' . $trackingIdentifier,
            'pdf_url' => $baseUrl . '/shop/tracking/' . $trackingIdentifier . '/pdf',
            'payment_initiated' => $paymentInitiated,
            'payment_message' => $paymentMessage,
        ]);
    }

    public function checkPaymentStatus($trackingIdentifier, FeedtanEcommercePaymentService $paymentService)
    {
        $order = $this->findOrderByIdentifier($trackingIdentifier);
        if (!$order) {
            abort(404);
        }

        $orderReference = $this->ensureGatewayOrderReference($order);

        if ($orderReference) {
            try {
                $paymentStatus = $paymentService->checkPaymentStatus($orderReference);

                if (isset($paymentStatus['success']) && $paymentStatus['success']) {
                    $this->syncOrderPaymentState($order, $paymentStatus['data'] ?? [], 'Payment status synced from FeedTan e-commerce API');
                }

                return response()->json($paymentStatus);
            } catch (\Exception $e) {
                Log::error('FeedTan e-commerce payment status check failed: ' . $e->getMessage());
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to check payment status'
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'message' => 'No payment reference found for this order'
        ]);
    }

    public function handlePaymentCallback(Request $request)
    {
        $paymentData = $request->input('data', []);
        if (!is_array($paymentData) || empty($paymentData)) {
            $paymentData = $request->all();
        }

        $orderReference = (string) ($paymentData['order_reference'] ?? '');
        if ($orderReference === '') {
            return response()->json([
                'success' => false,
                'message' => 'Missing order_reference in callback payload.'
            ], 422);
        }

        $order = OnlineOrder::query()
            ->where('payment_order_reference', $orderReference)
            ->orWhere('order_number', $orderReference)
            ->first();

        if (!$order) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found for the provided order_reference.'
            ], 404);
        }

        $this->syncOrderPaymentState($order, $paymentData, 'Payment status synced from FeedTan callback');

        return response()->json([
            'success' => true,
            'message' => 'Payment callback processed successfully.',
            'order_number' => $order->order_number,
            'payment_status' => $order->fresh()->payment_status,
        ]);
    }

    private function buildPaymentPayload(OnlineOrder $order, ?string $phoneNumber = null, bool $refreshReference = false): array
    {
        $order->loadMissing(['items.product']);
        $gatewayOrderReference = $refreshReference
            ? $this->refreshGatewayOrderReference($order)
            : $this->ensureGatewayOrderReference($order);

        $cartItemsForMetadata = $order->items->map(function ($item) {
            $name = $item->product ? $item->product->name : 'Item';

            return [
                'name' => $name,
                'quantity' => $item->quantity,
                'price' => $item->price,
            ];
        })->values()->all();

        return [
            'amount' => (float) $order->total,
            'phone_number' => $phoneNumber ?: $this->normalizePhoneNumber($order->customer_phone),
            'payer_name' => $order->customer_name,
            'description' => "Order {$order->order_number} - Shopping Cart",
            'order_reference' => $gatewayOrderReference,
            'email' => $order->customer_email,
            'callback_url' => route('api.shop.payments.feedtan.callback'),
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'items' => $cartItemsForMetadata,
            ],
        ];
    }

    private function normalizePhoneNumber(?string $phoneNumber): ?string
    {
        $digits = preg_replace('/\D+/', '', (string) $phoneNumber);
        if (!$digits) {
            return null;
        }

        if (str_starts_with($digits, '0') && strlen($digits) === 10) {
            $digits = '255' . substr($digits, 1);
        } elseif (strlen($digits) === 9 && str_starts_with($digits, '7')) {
            $digits = '255' . $digits;
        }

        if (!str_starts_with($digits, '255') || strlen($digits) !== 12) {
            return null;
        }

        return $digits;
    }

    private function ensureGatewayOrderReference(OnlineOrder $order): string
    {
        $currentReference = strtoupper((string) $order->payment_order_reference);
        if ($currentReference !== '' && preg_match('/^[A-Z0-9]+$/', $currentReference)) {
            return $currentReference;
        }

        $generatedReference = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', (string) $order->order_number));
        if ($generatedReference === '') {
            $generatedReference = 'ORD' . $order->id . strtoupper(substr(md5((string) $order->id), 0, 8));
        }

        if ($order->payment_order_reference !== $generatedReference) {
            $order->forceFill([
                'payment_order_reference' => $generatedReference,
            ])->save();
        }

        return $generatedReference;
    }

    private function refreshGatewayOrderReference(OnlineOrder $order): string
    {
        do {
            $generatedReference = 'ORD'
                . strtoupper(base_convert((string) $order->id, 10, 36))
                . strtoupper(Str::random(10));
        } while (OnlineOrder::query()
            ->where('payment_order_reference', $generatedReference)
            ->whereKeyNot($order->id)
            ->exists());

        if ($order->payment_order_reference !== $generatedReference) {
            $order->forceFill([
                'payment_order_reference' => $generatedReference,
            ])->save();
        }

        return $generatedReference;
    }

    private function syncOrderPaymentState(OnlineOrder $order, array $paymentData, string $historyNotePrefix = 'Payment sync'): void
    {
        $gatewayStatus = strtoupper((string) ($paymentData['status'] ?? $paymentData['clickpesa_status'] ?? ''));
        $isPaid = (bool) ($paymentData['is_paid'] ?? false);

        $updates = [
            'payment_transaction_id' => $paymentData['transaction_id'] ?? $order->payment_transaction_id,
            'payment_order_reference' => $paymentData['order_reference'] ?? $this->ensureGatewayOrderReference($order),
            'clickpesa_status' => $gatewayStatus !== '' ? $gatewayStatus : $order->clickpesa_status,
        ];

        $resolvedPaymentStatus = $order->payment_status;
        if ($isPaid || in_array($gatewayStatus, ['SUCCESS', 'SETTLED'], true)) {
            $resolvedPaymentStatus = 'paid';
        } elseif ($order->payment_status === 'paid') {
            $resolvedPaymentStatus = 'paid';
        } elseif (in_array($gatewayStatus, ['FAILED', 'DECLINED', 'CANCELLED'], true)) {
            $resolvedPaymentStatus = 'failed';
        } elseif ($gatewayStatus !== '' && $order->payment_status !== 'paid') {
            $resolvedPaymentStatus = 'pending';
        }

        $updates['payment_status'] = $resolvedPaymentStatus;

        $paymentStatusChanged = $resolvedPaymentStatus !== $order->payment_status;
        $gatewayStatusChanged = ($updates['clickpesa_status'] ?? null) !== $order->clickpesa_status;
        $transactionChanged = ($updates['payment_transaction_id'] ?? null) !== $order->payment_transaction_id;

        $order->update($updates);

        if ($paymentStatusChanged || $gatewayStatusChanged || $transactionChanged) {
            $notes = [];
            if ($gatewayStatusChanged && $updates['clickpesa_status']) {
                $notes[] = 'Gateway status: ' . $updates['clickpesa_status'];
            }
            if ($paymentStatusChanged) {
                $notes[] = 'Payment status changed to ' . $resolvedPaymentStatus;
            }
            if ($transactionChanged && $updates['payment_transaction_id']) {
                $notes[] = 'Transaction ID: ' . $updates['payment_transaction_id'];
            }

            OnlineOrderStatusHistory::create([
                'online_order_id' => $order->id,
                'status' => $order->status,
                'payment_status' => $resolvedPaymentStatus,
                'notes' => trim($historyNotePrefix . ($notes ? ' | ' . implode(' | ', $notes) : '')),
            ]);
        }
    }

    private function decryptTrackingToken(string $token): ?int
    {
        $decoded = base64_decode(strtr($token, '-_', '+/') . str_repeat('=', (4 - strlen($token) % 4) % 4), true);
        if ($decoded === false) {
            return null;
        }

        try {
            return (int) Crypt::decryptString($decoded);
        } catch (\Throwable $exception) {
            return null;
        }
    }

    private function findOrderByIdentifier(string $identifier, $with = ['items', 'rider', 'statusHistory']): ?OnlineOrder
    {
        $cleanIdentifier = ltrim($identifier, '#');
        
        // First try decrypting the token
        $orderId = $this->decryptTrackingToken($cleanIdentifier);
        if ($orderId) {
            $order = OnlineOrder::with($with)->find($orderId);
            if ($order) {
                return $order;
            }
        }
        
        // Then try exact token match (for existing orders)
        $order = OnlineOrder::where('tracking_token', $cleanIdentifier)->with($with)->first();
        if ($order) {
            return $order;
        }
        
        // Then try order number
        $order = OnlineOrder::where('order_number', $cleanIdentifier)->with($with)->first();
        if ($order) {
            return $order;
        }
        
        // Then try short reference
        return OnlineOrder::where('order_number', 'LIKE', '%' . $cleanIdentifier)->with($with)->first();
    }

    public function showTracking($trackingIdentifier = null)
    {
        $order = null;
        $route = null;
        $settings = \App\Models\StoreSetting::firstOrCreate();
        $categories = \App\Models\Category::where('is_active', true)->get();

        if ($trackingIdentifier) {
            $order = $this->findOrderByIdentifier($trackingIdentifier);
        }
        
        // Also check request query parameter
        if (!$order && request('order')) {
            $order = $this->findOrderByIdentifier(request('order'));
        }

        if ($order && $settings->openrouteservice_api_key && $order->delivery_latitude && $order->delivery_longitude) {
            try {
                $storeLat = $settings->store_latitude ?? -3.3869;
                $storeLng = $settings->store_longitude ?? 36.6883;

                $response = \Illuminate\Support\Facades\Http::withHeaders([
                    'Authorization' => $settings->openrouteservice_api_key,
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
                // Ignore routing errors on the public tracking page.
            }
        }

        return view('shop.tracking', compact('order', 'route', 'settings', 'categories'));
    }

    public function downloadTrackingPDF($trackingIdentifier)
    {
        $order = $this->findOrderByIdentifier($trackingIdentifier, ['items.product', 'rider', 'user']);
        if (!$order) {
            abort(404);
        }
        $pdf = new Dompdf();
        $pdf->loadHtml(view('online.orders-pdf', compact('order'))->render());
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();
        return $pdf->stream($order->order_number . '.pdf');
    }

    public function sitemap()
    {
        $settings = \App\Models\StoreSetting::firstOrCreate();
        $baseUrl = $settings->store_url ?? config('app.url');
        
        $products = \App\Models\Product::where('is_active', true)->where('is_available_online', true)->get();
        $categories = \App\Models\Category::where('is_active', true)->get();
        
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
        
        // Add homepage
        $xml .= '<url>';
        $xml .= '<loc>' . $baseUrl . '/</loc>';
        $xml .= '<lastmod>' . now()->toW3cString() . '</lastmod>';
        $xml .= '<changefreq>daily</changefreq>';
        $xml .= '<priority>1.0</priority>';
        $xml .= '</url>';
        
        // Add shop index
        $xml .= '<url>';
        $xml .= '<loc>' . $baseUrl . '/shop</loc>';
        $xml .= '<lastmod>' . now()->toW3cString() . '</lastmod>';
        $xml .= '<changefreq>daily</changefreq>';
        $xml .= '<priority>0.9</priority>';
        $xml .= '</url>';
        
        // Add categories
        foreach ($categories as $category) {
            $xml .= '<url>';
            $xml .= '<loc>' . $baseUrl . '/shop/category/' . $category->slug . '</loc>';
            $xml .= '<lastmod>' . $category->updated_at->toW3cString() . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.8</priority>';
            $xml .= '</url>';
        }
        
        // Add products
        foreach ($products as $product) {
            $xml .= '<url>';
            $xml .= '<loc>' . $baseUrl . '/shop/product/' . $product->slug . '</loc>';
            $xml .= '<lastmod>' . $product->updated_at->toW3cString() . '</lastmod>';
            $xml .= '<changefreq>weekly</changefreq>';
            $xml .= '<priority>0.8</priority>';
            $xml .= '</url>';
        }
        
        $xml .= '</urlset>';
        
        return response($xml)->header('Content-Type', 'text/xml');
    }
}
