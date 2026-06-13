@extends('layouts.app')

@section('page-title', 'Order #' . $order->order_number)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Order #{{ $order->order_number }}</h2>
            <a href="{{ route('online.orders') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Orders
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div>
                <span class="text-sm text-gray-600">Status:</span>
                <span class="ml-2 px-3 py-1 rounded-full text-xs font-semibold 
                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                    @elseif($order->status === 'preparing') bg-purple-100 text-purple-800
                    @elseif($order->status === 'ready') bg-cyan-100 text-cyan-800
                    @elseif($order->status === 'out_for_delivery') bg-orange-100 text-orange-800
                    @elseif($order->status === 'delivered') bg-green-100 text-green-800
                    @else bg-red-100 text-red-800 @endif">
                    {{ ucwords(str_replace('_', ' ', $order->status)) }}
                </span>
            </div>
            <div>
                <span class="text-sm text-gray-600">Payment Status:</span>
                <span class="ml-2 px-3 py-1 rounded-full text-xs font-semibold 
                    @if($order->payment_status === 'paid') bg-green-100 text-green-800
                    @elseif($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                    @else bg-red-100 text-red-800 @endif">
                    {{ ucwords($order->payment_status) }}
                </span>
            </div>
            <div>
                <span class="text-sm text-gray-600">Date:</span>
                <span class="ml-2">{{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
            <div>
                <h4 class="font-semibold text-primary-900 mb-2">Customer</h4>
                <p class="mb-1"><strong>Name:</strong> {{ $order->customer_name }}</p>
                <p class="mb-1"><strong>Phone:</strong> {{ $order->customer_phone }}</p>
                @if($order->customer_email)
                    <p class="mb-1"><strong>Email:</strong> {{ $order->customer_email }}</p>
                @endif
            </div>
            <div>
                <h4 class="font-semibold text-primary-900 mb-2">Delivery</h4>
                <p class="mb-1"><strong>Address:</strong> {{ $order->delivery_address }}</p>
                @if($order->rider)
                    <p class="mb-1"><strong>Rider:</strong> {{ $order->rider->name }} ({{ $order->rider->phone }})</p>
                @endif
                <p class="mb-1"><strong>Payment Method:</strong> {{ $order->payment_method ?? 'N/A' }}</p>
            </div>
        </div>

        <!-- Order Items -->
        <h3 class="text-lg font-semibold text-primary-900 mb-3">Order Items</h3>
        <div class="overflow-x-auto mb-6">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-gray-700">Product</th>
                        <th class="px-4 py-2 text-left text-gray-700">Price</th>
                        <th class="px-4 py-2 text-left text-gray-700">Quantity</th>
                        <th class="px-4 py-2 text-left text-gray-700">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($order->items as $item)
                    <tr>
                        <td class="px-4 py-2">{{ $item->product->name }}</td>
                        <td class="px-4 py-2">TZS {{ number_format($item->price, 2) }}</td>
                        <td class="px-4 py-2">{{ $item->quantity }}</td>
                        <td class="px-4 py-2">TZS {{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Order Summary -->
        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-right">
                <div class="md:col-span-2">
                    <span class="text-gray-600">Subtotal:</span>
                </div>
                <div>
                    <span class="font-semibold text-primary-900">TZS {{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="md:col-span-2">
                    <span class="text-gray-600">Delivery Fee:</span>
                </div>
                <div>
                    <span class="font-semibold text-primary-900">TZS {{ number_format($order->delivery_fee, 2) }}</span>
                </div>
                <div class="md:col-span-2">
                    <span class="text-lg font-semibold text-primary-900">Total:</span>
                </div>
                <div>
                    <span class="text-xl font-bold text-primary-900">TZS {{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        @if($order->notes)
            <div class="p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded mb-6">
                <h4 class="font-semibold text-yellow-800 mb-1">Notes</h4>
                <p class="text-sm text-yellow-700">{{ $order->notes }}</p>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-3 justify-end">
            <!-- Update Status -->
            <form action="{{ route('online.orders.status', $order) }}" method="POST" class="flex gap-2 items-center">
                @csrf
                @method('PUT')
                <select name="status" class="px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-primary-500">
                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                    <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Ready</option>
                    <option value="out_for_delivery" {{ $order->status === 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <select name="payment_status" class="px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-primary-500">
                    <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Payment: Pending</option>
                    <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Payment: Paid</option>
                    <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Payment: Failed</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded transition-colors">
                    Update Status
                </button>
            </form>

            <!-- Assign Rider -->
            @if($order->status !== 'delivered' && $order->status !== 'cancelled')
            <form action="{{ route('online.orders.assign-rider', $order) }}" method="POST" class="flex gap-2 items-center">
                @csrf
                <select name="delivery_rider_id" class="px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-primary-500">
                    <option value="">Assign Rider</option>
                    @foreach(\App\Models\DeliveryRider::where('is_active', true)->get() as $rider)
                        <option value="{{ $rider->id }}" {{ $order->delivery_rider_id == $rider->id ? 'selected' : '' }}>{{ $rider->name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded transition-colors">
                    Assign
                </button>
            </form>
            @endif
        </div>
    </div>
</div>
@endsection