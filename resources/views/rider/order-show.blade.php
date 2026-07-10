@extends('layouts.app')

@section('page-title', 'Order Details')

@section('content')
<div class="animate-[fadeIn_0.4s_ease] space-y-6">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="{{ route('rider.dashboard') }}" class="text-primary-600 hover:text-primary-800 mb-2 inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
                <h2 class="text-xl font-bold text-primary-900">Order {{ $order->short_customer_reference }}</h2>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('errors'))
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-800 rounded-lg">
                {{ $errors->first() }}
            </div>
        @endif

        <!-- Order Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Customer Information</h3>
                <div class="space-y-2">
                    <div><span class="font-medium text-gray-600">Name:</span> {{ $order->customer_name }}</div>
                    <div><span class="font-medium text-gray-600">Phone:</span> <a href="tel:{{ $order->customer_phone }}" class="text-primary-600 hover:underline">{{ $order->customer_phone }}</a></div>
                    @if($order->customer_email)
                    <div><span class="font-medium text-gray-600">Email:</span> <a href="mailto:{{ $order->customer_email }}" class="text-primary-600 hover:underline">{{ $order->customer_email }}</a></div>
                    @endif
                </div>
            </div>
            <div>
                <h3 class="text-lg font-semibold text-gray-900 mb-3">Delivery Information</h3>
                <div class="space-y-2">
                    <div><span class="font-medium text-gray-600">Address:</span> {{ $order->delivery_address }}</div>
                    @if($order->delivery_latitude && $order->delivery_longitude)
                    <div><span class="font-medium text-gray-600">Location:</span> {{ number_format($order->delivery_latitude, 6) }}, {{ number_format($order->delivery_longitude, 6) }}</div>
                    @endif
                    <div><span class="font-medium text-gray-600">Status:</span>
                        <div class="flex flex-wrap gap-2 mt-1">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                @if($order->status === 'confirmed') bg-blue-100 text-blue-800
                                @elseif($order->status === 'ready') bg-cyan-100 text-cyan-800
                                @elseif($order->status === 'out_for_delivery') bg-orange-100 text-orange-800
                                @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                @endif">
                                {{ ucwords(str_replace('_', ' ', $order->status)) }}
                            </span>
                            @if($order->rider_acceptance_status === 'pending')
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                    Pending Acceptance
                                </span>
                            @elseif($order->rider_acceptance_status === 'accepted')
                                <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                    Accepted
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Items -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Order Items</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-700">Product</th>
                            <th class="px-4 py-3 text-left text-gray-700">Quantity</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($order->items as $item)
                        <tr>
                            <td class="px-4 py-3">{{ $item->product ? $item->product->name : 'Product' }}</td>
                            <td class="px-4 py-3">{{ $item->quantity }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Delivery Fee -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Delivery Fee</h3>
            <div class="p-4 bg-gray-50 rounded-lg">
                <div class="text-2xl font-bold text-gray-900">TZS {{ number_format($order->delivery_fee, 2) }}</div>
            </div>
        </div>

        <!-- Accept/Reject Order -->
        @if($order->rider_acceptance_status === 'pending')
        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Accept or Reject Order</h3>
            <div class="flex flex-wrap gap-4">
                <form action="{{ route('rider.orders.accept', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors font-semibold">
                        <i class="fas fa-check mr-2"></i> Accept Order
                    </button>
                </form>
                <form action="{{ route('rider.orders.reject', $order) }}" method="POST">
                    @csrf
                    <button type="submit" class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors font-semibold">
                        <i class="fas fa-times mr-2"></i> Reject Order
                    </button>
                </form>
            </div>
        </div>
        @endif

        <!-- Status Update -->
        @if($order->status !== 'delivered' && $order->rider_acceptance_status === 'accepted')
        <div class="border-t pt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-3">Update Status</h3>
            <form action="{{ route('rider.orders.update-status', $order) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="flex flex-wrap gap-4 items-end">
                    @if(in_array($order->status, ['confirmed', 'ready', 'out_for_delivery']))
                        @if(!in_array($order->status, ['out_for_delivery']))
                        <input type="hidden" name="status" value="out_for_delivery">
                        <button type="submit" class="px-6 py-3 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition-colors font-semibold">
                            <i class="fas fa-shipping-fast mr-2"></i> Start Delivery
                        </button>
                        @endif
                    @endif

                    @if($order->status === 'out_for_delivery')
                    <input type="hidden" name="status" value="delivered">
                    <div class="flex-1 max-w-xs">
                        <label for="delivery_code_input" class="block text-sm font-medium text-gray-700 mb-1">Enter Delivery Code</label>
                        <input 
                            type="text" 
                            id="delivery_code_input" 
                            name="delivery_code_input" 
                            placeholder="4-digit code"
                            maxlength="4"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            required
                        >
                    </div>
                    <button type="submit" class="px-6 py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors font-semibold">
                        <i class="fas fa-check-circle mr-2"></i> Mark as Delivered
                    </button>
                    @endif
                </div>
            </form>
        </div>
        @endif

    </div>
</div>
@endsection
