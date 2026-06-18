<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Tracking - {{ $order->order_number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Manrope', sans-serif; }
        .status-pending { background-color: #fef3c7; color: #92400e; }
        .status-confirmed { background-color: #dbeafe; color: #1e40af; }
        .status-preparing { background-color: #fce7f3; color: #9d174d; }
        .status-ready { background-color: #ddd6fe; color: #5b21b6; }
        .status-out_for_delivery { background-color: #d1fae5; color: #065f46; }
        .status-delivered { background-color: #d1fae5; color: #065f46; }
        .status-cancelled { background-color: #fee2e2; color: #991b1b; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Top Header -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 text-white py-2">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <div class="flex items-center gap-4 text-sm">
                <span class="flex items-center gap-2">
                    <i class="fas fa-phone"></i>
                    +255 700 000 000
                </span>
            </div>
            <div class="flex items-center gap-4 text-sm">
                <a href="#" class="hover:text-green-200 transition">
                    <i class="fab fa-whatsapp text-lg"></i>
                </a>
                <a href="#" class="hover:text-green-200 transition">
                    <i class="fab fa-facebook text-lg"></i>
                </a>
                <a href="#" class="hover:text-green-200 transition">
                    <i class="fab fa-instagram text-lg"></i>
                </a>
                <a href="#" class="hover:text-green-200 transition">
                    <i class="fab fa-twitter text-lg"></i>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Main Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <a href="{{ route('shop.index') }}" class="flex items-center gap-3">
                <img src="{{ asset('feedtanstorelogo.png') }}" alt="Feedtan Store" class="h-12">
            </a>
            <a href="{{ route('shop.index') }}" class="text-gray-600 hover:text-gray-900 font-medium flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                <span class="hidden sm:inline">Back to Shop</span>
            </a>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Order Info -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-bold text-gray-900">Order Tracking</h1>
                <span class="badge status-{{ $order->status }} px-4 py-2 rounded-full text-sm font-semibold">
                    {{ ucwords(str_replace('_', ' ', $order->status)) }}
                </span>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-500">Order Number</p>
                    <p class="font-semibold text-gray-900">{{ $order->order_number }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Order Date</p>
                    <p class="font-semibold text-gray-900">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Customer</p>
                    <p class="font-semibold text-gray-900">{{ $order->customer_name }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Phone</p>
                    <p class="font-semibold text-gray-900">{{ $order->customer_phone }}</p>
                </div>
                <div>
                    <p class="text-gray-500">Payment Method</p>
                    <p class="font-semibold text-gray-900">
                        @if($order->payment_method === 'cash')
                            Cash
                        @elseif($order->payment_method === 'online')
                            Online Payment
                        @elseif($order->payment_method === 'bank')
                            Bank Transfer
                        @else
                            N/A
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-gray-500">Payment Status</p>
                    <p class="font-semibold text-gray-900">
                        <span class="{{ $order->payment_status === 'paid' ? 'text-green-600' : ($order->payment_status === 'failed' ? 'text-red-600' : 'text-yellow-600') }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                        @if($order->clickpesa_status)
                            <span class="text-gray-400 text-xs ml-2">({{ $order->clickpesa_status }})</span>
                        @endif
                    </p>
                </div>
                @if($order->payment_transaction_id)
                <div>
                    <p class="text-gray-500">Transaction ID</p>
                    <p class="font-semibold text-gray-900 text-xs">{{ $order->payment_transaction_id }}</p>
                </div>
                @endif
            </div>
            @if(in_array($order->payment_status, ['pending']))
                <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <p class="text-yellow-800 font-medium mb-2">
                        <i class="fas fa-info-circle mr-2"></i>
                        Checking payment status...
                    </p>
                    <p class="text-sm text-yellow-700" id="paymentStatusText">Please wait while we confirm your payment.</p>
                </div>
            @endif
        </div>

        <!-- Delivery Address -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-3">Delivery Address</h2>
            <p class="text-gray-700 mb-4">{{ $order->delivery_address }}</p>
            
            @if($order->delivery_latitude && $order->delivery_longitude)
                <div class="p-4 bg-gray-50 border border-gray-200 rounded-lg flex items-center gap-3">
                    <i class="fas fa-map-marker-alt text-green-600 text-xl"></i>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800">Delivery Coordinates</p>
                        <p class="text-xs text-gray-500 mt-1">{{ number_format($order->delivery_latitude, 6) }}, {{ number_format($order->delivery_longitude, 6) }}</p>
                    </div>
                    <a href="https://www.google.com/maps?q={{ $order->delivery_latitude }},{{ $order->delivery_longitude }}" target="_blank" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-medium hover:bg-blue-700 transition flex items-center gap-2">
                        <i class="fas fa-external-link-alt text-xs"></i> View Map
                    </a>
                </div>
            @endif
        </div>

        <!-- Status Timeline -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Status</h2>
            <div class="space-y-4">
                @php
                    $statuses = [
                        'pending' => 'Order Placed',
                        'confirmed' => 'Order Confirmed',
                        'preparing' => 'Preparing Your Order',
                        'ready' => 'Ready for Pickup',
                        'out_for_delivery' => 'Out for Delivery',
                        'delivered' => 'Delivered'
                    ];
                    $statusIndex = array_search($order->status, array_keys($statuses));
                @endphp
                @foreach($statuses as $status => $label)
                    @php
                        $currentIndex = array_search($status, array_keys($statuses));
                        $isCompleted = $currentIndex <= $statusIndex;
                        $isActive = $status === $order->status;
                    @endphp
                    <div class="flex items-center gap-4">
                        <div class="flex flex-col items-center">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $isCompleted ? 'bg-green-600' : 'bg-gray-300' }}">
                                <i class="fas fa-check text-white text-xs"></i>
                            </div>
                            @if(!$loop->last)
                                <div class="w-0.5 h-10 {{ $isCompleted ? 'bg-green-600' : 'bg-gray-300' }}"></div>
                            @endif
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold {{ $isActive ? 'text-green-600' : 'text-gray-600' }}">{{ $label }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Order Items -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Items</h2>
            <div class="divide-y divide-gray-100">
                @foreach($order->items as $item)
                    <div class="flex items-center justify-between py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-box text-gray-400"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">{{ $item->product->name ?? 'Product' }}</p>
                                <p class="text-sm text-gray-500">{{ $item->quantity }} x TZS {{ number_format($item->price, 2) }}</p>
                            </div>
                        </div>
                        <p class="font-semibold text-gray-900">TZS {{ number_format($item->total, 2) }}</p>
                    </div>
                @endforeach
            </div>
            <div class="border-t border-gray-100 pt-4 mt-4">
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-500">Subtotal</span>
                    <span class="text-gray-900">TZS {{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-gray-500">Delivery Fee</span>
                    <span class="text-gray-900">TZS {{ number_format($order->delivery_fee, 2) }}</span>
                </div>
                <div class="flex justify-between text-lg font-bold">
                    <span class="text-gray-900">Total</span>
                    <span class="text-green-700">TZS {{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Continue Shopping -->
        <div class="text-center">
            <a href="{{ route('shop.index') }}" class="inline-flex items-center gap-2 bg-green-600 text-white px-8 py-3 rounded-lg font-semibold hover:bg-green-700 transition">
                <i class="fas fa-arrow-left"></i> Continue Shopping
            </a>
        </div>
    </main>

    <footer class="bg-gray-900 text-white py-6 mt-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-gray-400">&copy; 2024 Feedtan Store. All rights reserved.</p>
        </div>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const orderNumber = '{{ $order->order_number }}';
            const paymentStatusText = document.getElementById('paymentStatusText');
            
            if (paymentStatusText) {
                let pollCount = 0;
                const maxPolls = 60; // Poll for 5 minutes max
                
                const checkPaymentStatus = async function() {
                    try {
                        const response = await fetch(`/api/shop/orders/${orderNumber}/payment-status`);
                        const data = await response.json();
                        
                        if (data.success) {
                            const clickpesaStatus = data.data.status;
                            paymentStatusText.textContent = `Payment status: ${clickpesaStatus}`;
                            
                            if (['SUCCESS', 'SETTLED', 'FAILED', 'DECLINED', 'CANCELLED'].includes(clickpesaStatus)) {
                                // Reload page to show updated status
                                setTimeout(() => {
                                    window.location.reload();
                                }, 2000);
                                return;
                            }
                        }
                        
                        pollCount++;
                        if (pollCount < maxPolls) {
                            setTimeout(checkPaymentStatus, 5000); // Check every 5 seconds
                        }
                    } catch (err) {
                        console.error('Error checking payment status:', err);
                        pollCount++;
                        if (pollCount < maxPolls) {
                            setTimeout(checkPaymentStatus, 5000);
                        }
                    }
                };
                
                checkPaymentStatus();
            }
        });
    </script>
</body>
</html>
