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
                    @if($order->status === 'Pending') bg-yellow-100 text-yellow-800
                    @elseif($order->status === 'Confirmed') bg-blue-100 text-blue-800
                    @elseif($order->status === 'Preparing') bg-purple-100 text-purple-800
                    @elseif($order->status === 'Ready') bg-cyan-100 text-cyan-800
                    @elseif($order->status === 'Out for Delivery') bg-orange-100 text-orange-800
                    @elseif($order->status === 'Delivered') bg-green-100 text-green-800
                    @else bg-red-100 text-red-800 @endif">
                    {{ $order->status }}
                </span>
            </div>
            <div>
                <span class="text-sm text-gray-600">Payment Status:</span>
                <span class="ml-2 px-3 py-1 rounded-full text-xs font-semibold 
                    @if($order->payment_status === 'Paid') bg-green-100 text-green-800
                    @elseif($order->payment_status === 'Pending') bg-yellow-100 text-yellow-800
                    @else bg-red-100 text-red-800 @endif">
                    {{ $order->payment_status }}
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
                    <option value="Pending" {{ $order->status === 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Confirmed" {{ $order->status === 'Confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="Preparing" {{ $order->status === 'Preparing' ? 'selected' : '' }}>Preparing</option>
                    <option value="Ready" {{ $order->status === 'Ready' ? 'selected' : '' }}>Ready</option>
                    <option value="Out for Delivery" {{ $order->status === 'Out for Delivery' ? 'selected' : '' }}>Out for Delivery</option>
                    <option value="Delivered" {{ $order->status === 'Delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="Cancelled" {{ $order->status === 'Cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <select name="payment_status" class="px-3 py-1 border border-gray-300 rounded focus:ring-2 focus:ring-primary-500">
                    <option value="Pending" {{ $order->payment_status === 'Pending' ? 'selected' : '' }}>Payment: Pending</option>
                    <option value="Paid" {{ $order->payment_status === 'Paid' ? 'selected' : '' }}>Payment: Paid</option>
                    <option value="Failed" {{ $order->payment_status === 'Failed' ? 'selected' : '' }}>Payment: Failed</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded transition-colors">
                    Update Status
                </button>
            </form>

            <!-- Assign Rider -->
            @if($order->status !== 'Delivered' && $order->status !== 'Cancelled')
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