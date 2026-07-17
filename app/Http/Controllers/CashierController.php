<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Shift;
use App\Models\AccountingEntry;
use App\Models\StoreSetting;
use App\Models\OnlineOrder;
use App\Models\OnlineOrderItem;
use App\Models\Customer;
use App\Services\FeedtanEcommercePaymentService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CashierController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'cashier') {
            return redirect()->route('dashboard');
        }

        $products = Product::with(['category', 'brand', 'unit'])->where('is_active', true)->get();
        $storeSetting = StoreSetting::firstOrCreate();
        $customers = \App\Models\Customer::all();
        
        return view('cashier.dashboard', compact('products', 'storeSetting', 'customers'));
    }

    public function getProductByBarcode($barcode)
    {
        $product = Product::where('barcode', $barcode)->where('is_active', true)->first();
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        return response()->json($product);
    }

    public function searchProducts(Request $request)
    {
        $term = $request->input('term');
        $products = Product::with(['category', 'brand', 'unit'])
            ->where('is_active', true)
            ->where(function($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('barcode', 'like', "%{$term}%");
            })
            ->get();
        return response()->json($products);
    }

    public function getDashboardData()
    {
        $userId = Auth::id();
        $today = now()->startOfDay();
        
        // Get current shift
        $currentShift = Shift::where('user_id', $userId)->whereNull('closed_at')->first();
        
        // Today's sales - eager load items to avoid N+1 queries
        $todaySales = Sale::with('items')->where('user_id', $userId)
            ->where('created_at', '>=', $today)
            ->where('status', 'completed')
            ->latest()
            ->get();
        
        // Shift's sales (if shift exists) - eager load items
        $shiftSales = $currentShift 
            ? Sale::with('items')->where('shift_id', $currentShift->id)->where('status', 'completed')->get()
            : collect();
        
        // Calculate totals
        $todayTotal = $todaySales->sum('total');
        $shiftTotal = $shiftSales->sum('total');
        $todayItems = $todaySales->sum(fn($sale) => $sale->items->sum('quantity'));
        $shiftItems = $shiftSales->sum(fn($sale) => $sale->items->sum('quantity'));
        
        // Payment breakdown
        $todayBreakdown = [
            'cash' => $todaySales->where('payment_method', 'cash')->sum('total'),
            'card' => $todaySales->where('payment_method', 'card')->sum('total'),
            'mobile' => $todaySales->where('payment_method', 'mobile')->sum('total'),
        ];
        
        $shiftBreakdown = [
            'cash' => $shiftSales->where('payment_method', 'cash')->sum('total'),
            'card' => $shiftSales->where('payment_method', 'card')->sum('total'),
            'mobile' => $shiftSales->where('payment_method', 'mobile')->sum('total'),
        ];
        
        return response()->json([
            'todayTotal' => $todayTotal,
            'shiftTotal' => $shiftTotal,
            'todayItems' => $todayItems,
            'shiftItems' => $shiftItems,
            'todayBreakdown' => $todayBreakdown,
            'shiftBreakdown' => $shiftBreakdown,
            'transactions' => $todaySales->take(10)->map(fn($sale) => [
                'id' => $sale->id,
                'invoice_number' => $sale->invoice_number,
                'total' => $sale->total,
                'payment_method' => $sale->payment_method,
                'created_at' => $sale->created_at->format('H:i:s'),
                'items_count' => $sale->items->count()
            ]),
            'currentShift' => $currentShift
        ]);
    }

    public function completeSale(Request $request)
    {
        try {
            \Log::info('Starting complete sale', ['request_data' => $request->all()]);
            
            $data = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'discount' => 'nullable|numeric',
                'paid' => 'required|numeric|min:0',
                'payment_method' => 'required|string|in:cash,card,mobile',
                'customer_id' => 'nullable|exists:customers,id'
            ]);

            \Log::info('Validation passed', $data);

            // Check stock availability
            foreach ($data['items'] as $item) {
                $product = Product::find($item['id']);
                if (!$product) {
                    return response()->json(['error' => 'Product not found'], 400);
                }
                if ($product->quantity < $item['quantity']) {
                    return response()->json(['error' => "Insufficient stock for {$product->name}. Available: {$product->quantity}"], 400);
                }
            }

            $invoiceNumber = 'INV-' . date('YmdHis');
            $subtotal = collect($data['items'])->sum(fn($item) => $item['price'] * $item['quantity']);
            $tax = 0; // 0% tax
            $discount = $data['discount'] ?? 0;
            $total = $subtotal + $tax - $discount;
            $paid = $data['paid'];
            $change = max(0, $paid - $total);

            $currentShift = Shift::where('user_id', Auth::id())->whereNull('closed_at')->first();
            $shiftId = $currentShift ? $currentShift->id : null;

            \Log::info('Creating sale', [
                'invoice_number' => $invoiceNumber,
                'subtotal' => $subtotal,
                'total' => $total,
                'shift_id' => $shiftId
            ]);

            $sale = Sale::create([
                'invoice_number' => $invoiceNumber,
                'customer_id' => $data['customer_id'] ?? null,
                'user_id' => Auth::id(),
                'shift_id' => $shiftId,
                'discount_id' => null,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total,
                'paid' => $paid,
                'change' => $change,
                'payment_method' => $data['payment_method'],
                'type' => 'cash',
                'status' => 'completed',
                'notes' => ''
            ]);

            \Log::info('Sale created', ['sale_id' => $sale->id]);

            foreach ($data['items'] as $itemData) {
                $itemTotal = $itemData['quantity'] * $itemData['price'];
                $sale->items()->create([
                    'product_id' => $itemData['id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['price'],
                    'discount' => 0,
                    'total' => $itemTotal
                ]);

                $product = Product::find($itemData['id']);
                $product->decrement('quantity', $itemData['quantity']);
            }

            if ($currentShift) {
                if ($data['payment_method'] == 'cash') {
                    $currentShift->increment('cash_sales', $total);
                } elseif ($data['payment_method'] == 'card') {
                    $currentShift->increment('card_sales', $total);
                } elseif ($data['payment_method'] == 'mobile') {
                    $currentShift->increment('mobile_sales', $total);
                }
            }

            $this->createAccountingEntries($sale);

            \Log::info('Sale completed successfully', ['sale_id' => $sale->id]);

            return response()->json(['sale' => $sale, 'change' => $change, 'sale_id' => $sale->id]);
        } catch (\Exception $e) {
            \Log::error('Error completing sale', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    protected function createAccountingEntries(Sale $sale) {
        try {
            $cashAccount = \App\Models\Account::where('name', 'Cash')->first();
            $salesAccount = \App\Models\Account::where('name', 'Sales')->first();
            $inventoryAccount = \App\Models\Account::where('name', 'Inventory')->first();
            $cogsAccount = \App\Models\Account::where('name', 'Cost of Goods Sold')->first();

            $journalNumber = 'JE-CASHIER-' . date('Ymd') . '-' . str_pad(\App\Models\JournalEntry::count() + 1, 4, '0', STR_PAD_LEFT);

            $journalEntry = \App\Models\JournalEntry::create([
                'journal_number' => $journalNumber,
                'entry_number' => $journalNumber,
                'entry_date' => now(),
                'description' => 'Sale: ' . $sale->invoice_number,
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
                'is_manual' => false,
            ]);

            AccountingEntry::create([
                'journal_entry_id' => $journalEntry->id,
                'reference_number' => $sale->invoice_number,
                'reference_type' => Sale::class,
                'account' => 'Cash',
                'account_id' => $cashAccount?->id,
                'type' => 'debit',
                'amount' => $sale->paid,
                'description' => 'Sale payment received'
            ]);

            AccountingEntry::create([
                'journal_entry_id' => $journalEntry->id,
                'reference_number' => $sale->invoice_number,
                'reference_type' => Sale::class,
                'account' => 'Sales',
                'account_id' => $salesAccount?->id,
                'type' => 'credit',
                'amount' => $sale->total,
                'description' => 'Sale completed'
            ]);

            AccountingEntry::create([
                'journal_entry_id' => $journalEntry->id,
                'reference_number' => $sale->invoice_number,
                'reference_type' => Sale::class,
                'account' => 'Inventory',
                'account_id' => $inventoryAccount?->id,
                'type' => 'credit',
                'amount' => $sale->subtotal,
                'description' => 'Inventory sold'
            ]);

            AccountingEntry::create([
                'journal_entry_id' => $journalEntry->id,
                'reference_number' => $sale->invoice_number,
                'reference_type' => Sale::class,
                'account' => 'Cost of Goods Sold',
                'account_id' => $cogsAccount?->id,
                'type' => 'debit',
                'amount' => $sale->subtotal,
                'description' => 'COGS for sale'
            ]);
        } catch (\Exception $e) {
            // Ignore accounting entry creation errors so sales don't fail
            \Log::error('Failed to create accounting entries in cashier: ' . $e->getMessage());
        }
    }

    public function initiateOnlinePayment(Request $request, FeedtanEcommercePaymentService $paymentService)
    {
        try {
            $data = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.id' => 'required|exists:products,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'discount' => 'nullable|numeric',
                'customer_id' => 'nullable|exists:customers,id',
                'phone_number' => 'required|string',
            ]);

            // Normalize phone number
            $phoneNumber = $this->normalizePhoneNumber($data['phone_number']);
            if (!$phoneNumber) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid phone number. Please use format: 255712345678.'
                ], 400);
            }

            // Check stock availability
            foreach ($data['items'] as $item) {
                $product = Product::find($item['id']);
                if (!$product) {
                    return response()->json(['success' => false, 'error' => 'Product not found'], 400);
                }
                if ($product->quantity < $item['quantity']) {
                    return response()->json(['success' => false, 'error' => "Insufficient stock for {$product->name}. Available: {$product->quantity}"], 400);
                }
            }

            // Get customer details
            $customer = null;
            $customerName = 'Walk-in Customer';
            $customerEmail = null;
            $customerAddress = null;
            if ($data['customer_id']) {
                $customer = Customer::find($data['customer_id']);
                $customerName = $customer->name;
                $customerEmail = $customer->email;
                $customerAddress = $customer->address;
            }

            // Calculate totals
            $orderNumber = 'ORD-' . date('YmdHis') . '-' . Str::random(4);
            $trackingToken = Str::uuid();
            $deliveryCode = str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
            $subtotal = collect($data['items'])->sum(fn($item) => $item['price'] * $item['quantity']);
            $discount = $data['discount'] ?? 0;
            $total = $subtotal - $discount;

            // Create online order
            $onlineOrder = OnlineOrder::create([
                'order_number' => $orderNumber,
                'tracking_token' => $trackingToken,
                'delivery_code' => $deliveryCode,
                'customer_id' => $data['customer_id'],
                'customer_name' => $customerName,
                'customer_phone' => $phoneNumber,
                'customer_email' => $customerEmail,
                'delivery_address' => $customerAddress,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => 'online',
                'subtotal' => $subtotal,
                'discount' => $discount,
                'delivery_fee' => 0,
                'total' => $total,
                'user_id' => Auth::id(),
            ]);

            // Create order items
            foreach ($data['items'] as $itemData) {
                $itemTotal = $itemData['quantity'] * $itemData['price'];
                $onlineOrder->items()->create([
                    'product_id' => $itemData['id'],
                    'quantity' => $itemData['quantity'],
                    'price' => $itemData['price'],
                    'total' => $itemTotal,
                ]);
            }

            // Eager load items with product relationship
            $onlineOrder->load('items.product');

            // Initiate payment
            try {
                $paymentPayload = $this->buildPaymentPayload($onlineOrder, $phoneNumber);
                $paymentResponse = $paymentService->initiatePayment($paymentPayload);

                if (isset($paymentResponse['success']) && $paymentResponse['success']) {
                    $this->syncOrderPaymentState($onlineOrder, $paymentResponse['data'] ?? [], 'Payment initiated via cashier dashboard');
                    $trackingUrl = route('shop.tracking.show', ltrim($onlineOrder->short_customer_reference, '#'));
                    $pdfUrl = route('shop.tracking.pdf', ltrim($onlineOrder->short_customer_reference, '#'));
                    return response()->json([
                        'success' => true,
                        'message' => 'Payment initiated successfully! Please check your phone to complete the payment.',
                        'order' => $onlineOrder,
                        'tracking_url' => $trackingUrl,
                        'pdf_url' => $pdfUrl,
                        'order_number' => $onlineOrder->order_number,
                    ]);
                } else {
                    return response()->json([
                        'success' => false,
                        'message' => $paymentResponse['message'] ?? 'Failed to initiate payment. Please try again.',
                    ], 400);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to initiate online payment via cashier: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
                // Even if payment fails, we still have the order in the system
                $trackingUrl = route('shop.tracking.show', ltrim($onlineOrder->short_customer_reference, '#'));
                return response()->json([
                    'success' => false,
                    'message' => 'Order created but payment initiation failed: ' . $e->getMessage() . '. Please complete payment manually.',
                    'order' => $onlineOrder,
                    'tracking_url' => $trackingUrl,
                    'order_number' => $onlineOrder->order_number,
                ], 400);
            }
        } catch (\Exception $e) {
            \Log::error('Error in initiate online payment: ' . $e->getMessage() . ' Trace: ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: ' . $e->getMessage()
            ], 500);
        }
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

    private function ensureGatewayOrderReference(OnlineOrder $order): string
    {
        $currentReference = strtoupper((string) $order->payment_order_reference);
        // If current reference is valid (not empty, alphanumeric, ≤20 chars), use it
        if ($currentReference !== '' && preg_match('/^[A-Z0-9]+$/', $currentReference) && strlen($currentReference) <= 20) {
            return $currentReference;
        }

        // Generate a short reference: ORD + order ID (up to 10 digits) + 6 random chars
        $generatedReference = 'ORD' . $order->id . strtoupper(\Illuminate\Support\Str::random(6));
        // Ensure it's ≤20 characters
        $generatedReference = substr($generatedReference, 0, 20);

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
            // Generate a short reference: ORD + order ID (up to 10 digits) + 6 random chars
            $generatedReference = 'ORD' . $order->id . strtoupper(\Illuminate\Support\Str::random(6));
            // Ensure it's ≤20 characters
            $generatedReference = substr($generatedReference, 0, 20);
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

            \App\Models\OnlineOrderStatusHistory::create([
                'online_order_id' => $order->id,
                'status' => $order->status,
                'payment_status' => $resolvedPaymentStatus,
                'notes' => trim($historyNotePrefix . ($notes ? ' | ' . implode(' | ', $notes) : '')),
            ]);
        }
    }
}
