@extends('layouts.app')

@section('page-title', 'Order Details')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <a href="{{ route('marketing-officer.orders') }}" class="text-primary-600 hover:text-primary-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Orders
        </a>
    </div>

    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-primary-900">{{ $order->order_number }}</h1>
                <p class="text-gray-600">Order #{{ $order->id }}</p>
            </div>
            <div>
                @if($order->status === 'pending')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                @elseif($order->status === 'confirmed')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">Confirmed</span>
                @elseif($order->status === 'out_for_delivery')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-purple-100 text-purple-800">Out for Delivery</span>
                @elseif($order->status === 'delivered')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Delivered</span>
                @elseif($order->status === 'cancelled')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">Cancelled</span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-600">Customer Name</p>
                <p class="font-semibold">{{ $order->customer_name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Customer Phone</p>
                <p class="font-semibold">{{ $order->customer_phone }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Delivery Address</p>
                <p class="font-semibold">{{ $order->delivery_address }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Payment Method</p>
                <p class="font-semibold">{{ ucfirst($order->payment_method) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Payment Status</p>
                <p class="font-semibold">{{ ucfirst($order->payment_status) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Order Date</p>
                <p class="font-semibold">{{ $order->created_at->format('M d, Y H:i') }}</p>
            </div>
            @if($order->rider)
            <div>
                <p class="text-sm text-gray-600">Assigned Rider</p>
                <p class="font-semibold">{{ $order->rider->name }}</p>
            </div>
            @endif
        </div>

        @if($order->notes)
        <div class="mt-6">
            <p class="text-sm text-gray-600">Notes</p>
            <p class="text-gray-900">{{ $order->notes }}</p>
        </div>
        @endif
    </div>

    <div class="card rounded-2xl overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-bold text-primary-900">Order Items</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Unit Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($order->items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $item->product_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->quantity }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">TZS {{ number_format($item->unit_price, 0) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">TZS {{ number_format($item->total, 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50">
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right font-semibold text-gray-900">Subtotal</td>
                        <td class="px-6 py-3 text-sm font-semibold text-gray-900">TZS {{ number_format($order->subtotal, 0) }}</td>
                    </tr>
                    @if($order->discount > 0)
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right font-semibold text-gray-900">Discount</td>
                        <td class="px-6 py-3 text-sm font-semibold text-green-600">TZS {{ number_format($order->discount, 0) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right font-semibold text-gray-900">Delivery Fee</td>
                        <td class="px-6 py-3 text-sm font-semibold text-gray-900">TZS {{ number_format($order->delivery_fee, 0) }}</td>
                    </tr>
                    <tr>
                        <td colspan="3" class="px-6 py-3 text-right font-bold text-gray-900">Total</td>
                        <td class="px-6 py-3 text-sm font-bold text-primary-600">TZS {{ number_format($order->total, 0) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <div class="card rounded-2xl p-6">
        <h2 class="text-lg font-bold text-primary-900 mb-4">Update Order Status</h2>
        <form action="{{ route('marketing-officer.update-order-status', $order->id) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="flex gap-4">
                <select name="status" required class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="out_for_delivery" {{ $order->status === 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Update Status
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
