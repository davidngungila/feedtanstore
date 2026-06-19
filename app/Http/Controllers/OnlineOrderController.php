<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\OnlineOrder;
use App\Models\OnlineOrderItem;
use App\Models\OnlineOrderStatusHistory;
use App\Models\Product;
use App\Models\DeliveryRider;
use App\Models\AccountingEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Dompdf\Dompdf;
use App\Services\ClickPesaService;

class OnlineOrderController extends Controller
{
    public function index(Request $request)
    {
        $statusFilter = $request->input('status', ['pending', 'confirmed', 'preparing', 'ready', 'out_for_delivery', 'delivered', 'cancelled']);
        if (!is_array($statusFilter)) {
            $statusFilter = [$statusFilter];
        }
        
        $orders = OnlineOrder::with(['items', 'rider', 'user'])
            ->whereIn('status', $statusFilter)
            ->latest()
            ->get();
            
        $settings = \App\Models\StoreSetting::firstOrCreate();
        
        // Get store location (default to Arusha, Tanzania if not set)
        $storeLat = $settings->store_latitude ?? -3.3869; 
        $storeLng = $settings->store_longitude ?? 36.6883;
        
        $routes = [];
        
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

        $allStatuses = ['pending', 'confirmed', 'preparing', 'ready', 'out_for_delivery', 'delivered', 'cancelled'];
        return view('online.orders', compact('orders', 'storeLat', 'storeLng', 'routes', 'statusFilter', 'allStatuses'));
    }

    public function shop()
    {
        $products = Product::where('is_active', true)
            ->where('is_available_online', true)
            ->where('quantity', '>', 0)
            ->with(['category', 'brand', 'images'])
            ->latest()
            ->get();

        $slides = \App\Models\CarouselSlide::where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('shop.index', compact('products', 'slides'));
    }

    public function showProduct(Product $product)
    {
        $product->load(['category', 'brand', 'images']);
        return view('shop.product', compact('product'));
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
        $total = $subtotal + $deliveryFee;

        $order = OnlineOrder::create([
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'customer_id' => $request->customer_id,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_email' => $request->customer_email,
            'delivery_address' => $request->delivery_address,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => $request->payment_method,
            'subtotal' => $subtotal,
            'delivery_fee' => $deliveryFee,
            'total' => $total,
            'delivery_rider_id' => $request->delivery_rider_id,
            'user_id' => Auth::id(),
            'notes' => $request->notes
        ]);

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
        
        return view('online.orders-show', compact('order', 'route', 'settings'));
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
        $total = $subtotal + $deliveryFee;

        $order->update([
            'customer_id' => $request->customer_id,
            'customer_name' => $request->customer_name,
            'customer_phone' => $request->customer_phone,
            'customer_email' => $request->customer_email,
            'delivery_address' => $request->delivery_address,
            'payment_method' => $request->payment_method,
            'subtotal' => $subtotal,
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
            'notes' => 'nullable|string'
        ]);

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
        $order->update(['delivery_rider_id' => $request->delivery_rider_id, 'status' => 'out_for_delivery']);

        // Log status change
        OnlineOrderStatusHistory::create([
            'online_order_id' => $order->id,
            'status' => $order->status,
            'payment_status' => $order->payment_status,
            'notes' => $request->notes ?? "Rider assigned and status changed from {$oldStatus} to {$order->status}",
            'user_id' => Auth::id()
        ]);

        return back()->with('success', 'Rider assigned successfully!');
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

    public function placeOrder(Request $request, ClickPesaService $clickPesaService)
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

        $deliveryFee = $request->delivery_fee ?? 0;
        $total = $subtotal + $deliveryFee;

        $order = OnlineOrder::create([
            'order_number' => 'ORD-' . strtoupper(uniqid()),
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

        // Try to initiate payment with ClickPesa (but don't fail the order if it doesn't work)
        try {
            $phoneNumber = $request->customer_phone;
            // Format phone number to 255xxxxxxxxx
            if (substr($phoneNumber, 0, 1) === '0') {
                $phoneNumber = '255' . substr($phoneNumber, 1);
            }

            $paymentData = [
                'amount' => $total,
                'phone_number' => $phoneNumber,
                'payer_name' => $request->customer_name,
                'description' => "Order {$order->order_number} - Shopping Cart",
                'order_reference' => $order->order_number,
                'email' => $request->customer_email,
                'metadata' => [
                    'order_id' => $order->id,
                    'items' => $cartItemsForMetadata
                ]
            ];

            $paymentResponse = $clickPesaService->initiatePayment($paymentData);

            if (isset($paymentResponse['success']) && $paymentResponse['success']) {
                $order->update([
                    'payment_transaction_id' => $paymentResponse['data']['transaction_id'],
                    'payment_order_reference' => $paymentResponse['data']['order_reference'],
                    'clickpesa_status' => $paymentResponse['data']['status']
                ]);
            }
        } catch (\Exception $e) {
            // Do nothing - just log the error and continue
            \Log::error('ClickPesa payment initiation failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'order_number' => $order->order_number
        ]);
    }

    public function checkPaymentStatus($orderNumber, ClickPesaService $clickPesaService)
    {
        $order = OnlineOrder::where('order_number', $orderNumber)->firstOrFail();

        if ($order->payment_order_reference) {
            try {
                $paymentStatus = $clickPesaService->checkPaymentStatus($order->payment_order_reference);

                if (isset($paymentStatus['success']) && $paymentStatus['success']) {
                    $newClickpesaStatus = $paymentStatus['data']['status'];
                    $order->update(['clickpesa_status' => $newClickpesaStatus]);

                    if (in_array($newClickpesaStatus, ['SUCCESS', 'SETTLED'])) {
                        $order->update(['payment_status' => 'paid']);

                        // Update order status history
                        OnlineOrderStatusHistory::create([
                            'online_order_id' => $order->id,
                            'status' => $order->status,
                            'payment_status' => 'paid',
                            'notes' => 'Payment received successfully'
                        ]);
                    } elseif (in_array($newClickpesaStatus, ['FAILED', 'DECLINED', 'CANCELLED'])) {
                        $order->update(['payment_status' => 'failed']);
                    }
                }

                return response()->json($paymentStatus);
            } catch (\Exception $e) {
                \Log::error('ClickPesa payment status check failed: ' . $e->getMessage());
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

    public function showTracking($orderNumber)
    {
        $order = OnlineOrder::where('order_number', $orderNumber)->with(['items', 'rider', 'statusHistory'])->firstOrFail();
        return view('shop.tracking', compact('order'));
    }

    public function downloadTrackingPDF($orderNumber)
    {
        $order = OnlineOrder::where('order_number', $orderNumber)->with(['items.product', 'rider', 'user'])->firstOrFail();
        $pdf = new Dompdf();
        $pdf->loadHtml(view('online.orders-pdf', compact('order'))->render());
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();
        return $pdf->stream($order->order_number . '.pdf');
    }
}
