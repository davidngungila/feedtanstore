@extends('layouts.app')

@section('page-title', 'Delivery Management')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="flex items-center justify-between mb-6 flex-wrap gap-3">
        <h1 class="text-xl font-bold" :class="darkMode?'text-white':'text-primary-900'">Delivery Management</h1>
        <div class="flex gap-3">
            <a href="{{ route('online.delivery.map') }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                <i class="fas fa-map mr-2"></i>Delivery Map
            </a>
            <a href="{{ route('online.customer.locations') }}" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                <i class="fas fa-users mr-2"></i>Customer Locations
            </a>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Ready Orders -->
        <div class="card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-primary-900 mb-4">
                <i class="fas fa-box mr-2 text-cyan-600"></i>Ready for Delivery
            </h3>
            @forelse($readyOrders as $order)
            <div class="border rounded-lg p-4 mb-3">
                <h4 class="font-semibold text-primary-900 mb-1">{{ $order->order_number }}</h4>
                <p class="text-sm text-gray-700 mb-1">{{ $order->customer_name }} - {{ $order->customer_phone }}</p>
                <p class="text-sm text-gray-500 mb-2">{{ $order->delivery_address }}</p>
                <p class="text-sm font-semibold mb-3">TZS {{ number_format($order->total, 2) }}</p>
                <form action="{{ route('online.orders.assign-rider', $order) }}" method="POST" class="flex gap-2">
                    @csrf
                    <select name="delivery_rider_id" class="flex-1 px-3 py-1 border rounded text-sm">
                        <option value="">Select Rider</option>
                        @foreach($riders as $rider)
                            @if($rider->is_active)
                                <option value="{{ $rider->id }}">{{ $rider->name }}</option>
                            @endif
                        @endforeach
                    </select>
                    <button type="submit" class="px-3 py-1 bg-primary-600 hover:bg-primary-700 text-white text-sm rounded transition-colors">Assign</button>
                </form>
            </div>
            @empty
            <p class="text-gray-500 text-sm">No orders ready for delivery.</p>
            @endforelse
        </div>

        <!-- Out for Delivery -->
        <div class="card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-primary-900 mb-4">
                <i class="fas fa-truck mr-2 text-orange-600"></i>Out for Delivery
            </h3>
            @forelse($outForDelivery as $order)
            <div class="border rounded-lg p-4 mb-3">
                <h4 class="font-semibold text-primary-900 mb-1">{{ $order->order_number }}</h4>
                <p class="text-sm text-gray-700 mb-1">{{ $order->customer_name }} - {{ $order->customer_phone }}</p>
                <p class="text-sm text-gray-500 mb-2">{{ $order->delivery_address }}</p>
                <p class="text-sm font-semibold mb-2">
                    Rider: {{ $order->rider ? $order->rider->name : 'N/A' }}
                </p>
                <div class="text-sm">TZS {{ number_format($order->total, 2) }}</div>
            </div>
            @empty
            <p class="text-gray-500 text-sm">No orders out for delivery.</p>
            @endforelse
        </div>
    </div>
@endsection