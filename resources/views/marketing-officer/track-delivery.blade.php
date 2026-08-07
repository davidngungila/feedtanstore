@extends('layouts.app')

@section('page-title', 'Track Delivery')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <a href="{{ route('marketing-officer.order-details', $order->id) }}" class="text-primary-600 hover:text-primary-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Order Details
        </a>
    </div>

    <div class="card rounded-2xl p-6 mb-6">
        <h1 class="text-2xl font-bold text-primary-900 mb-6">Track Delivery - {{ $order->order_number }}</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-sm text-gray-600">Customer</p>
                <p class="font-semibold">{{ $order->customer_name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Delivery Address</p>
                <p class="font-semibold">{{ $order->delivery_address }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Assigned Rider</p>
                <p class="font-semibold">{{ $order->rider->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Rider Phone</p>
                <p class="font-semibold">{{ $order->rider->phone }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Vehicle Type</p>
                <p class="font-semibold">{{ $order->rider->vehicle_type }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Vehicle Plate</p>
                <p class="font-semibold">{{ $order->rider->vehicle_plate }}</p>
            </div>
        </div>
    </div>

    <div class="card rounded-2xl p-6 mb-6">
        <h2 class="text-lg font-bold text-primary-900 mb-4">Delivery Status</h2>
        
        <div class="space-y-4">
            @if($order->status === 'pending')
            <div class="flex items-center gap-4 p-4 bg-yellow-50 rounded-lg">
                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div>
                    <p class="font-semibold text-yellow-800">Pending</p>
                    <p class="text-sm text-yellow-600">Order is waiting to be processed</p>
                </div>
            </div>
            @endif

            @if($order->status === 'confirmed')
            <div class="flex items-center gap-4 p-4 bg-blue-50 rounded-lg">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-blue-600"></i>
                </div>
                <div>
                    <p class="font-semibold text-blue-800">Confirmed</p>
                    <p class="text-sm text-blue-600">Order confirmed and rider assigned</p>
                </div>
            </div>
            @endif

            @if($order->status === 'out_for_delivery')
            <div class="flex items-center gap-4 p-4 bg-purple-50 rounded-lg">
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-motorcycle text-purple-600"></i>
                </div>
                <div>
                    <p class="font-semibold text-purple-800">Out for Delivery</p>
                    <p class="text-sm text-purple-600">Rider is on the way to customer</p>
                </div>
            </div>
            @endif

            @if($order->status === 'delivered')
            <div class="flex items-center gap-4 p-4 bg-green-50 rounded-lg">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div>
                    <p class="font-semibold text-green-800">Delivered</p>
                    <p class="text-sm text-green-600">Order successfully delivered</p>
                </div>
            </div>
            @endif

            @if($order->status === 'cancelled')
            <div class="flex items-center gap-4 p-4 bg-red-50 rounded-lg">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-times text-red-600"></i>
                </div>
                <div>
                    <p class="font-semibold text-red-800">Cancelled</p>
                    <p class="text-sm text-red-600">Order was cancelled</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="card rounded-2xl p-6 mb-6">
        <h2 class="text-lg font-bold text-primary-900 mb-4">Rider Location</h2>
        
        @if($order->rider && $order->rider->latestLocation)
        <div class="bg-gray-50 rounded-lg p-4">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Last Updated</p>
                    <p class="font-semibold">{{ $order->rider->latestLocation->created_at->format('M d, Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Latitude</p>
                    <p class="font-semibold">{{ $order->rider->latestLocation->latitude }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Longitude</p>
                    <p class="font-semibold">{{ $order->rider->latestLocation->longitude }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Accuracy</p>
                    <p class="font-semibold">{{ $order->rider->latestLocation->accuracy }}m</p>
                </div>
            </div>
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-map-marker-alt text-4xl mb-4 text-gray-300"></i>
            <p class="font-medium">No location data available</p>
            <p class="text-sm">Rider location will appear here when available</p>
        </div>
        @endif
    </div>

    <div class="card rounded-2xl p-6">
        <h2 class="text-lg font-bold text-primary-900 mb-4">Status History</h2>
        
        @if($order->statusHistory->count() > 0)
        <div class="space-y-3">
            @foreach($order->statusHistory as $history)
            <div class="flex items-start gap-4 p-3 border border-gray-200 rounded-lg">
                <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-history text-primary-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-gray-900">{{ ucfirst($history->status) }}</p>
                        <p class="text-xs text-gray-500">{{ $history->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    @if($history->notes)
                    <p class="text-sm text-gray-600 mt-1">{{ $history->notes }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-history text-4xl mb-4 text-gray-300"></i>
            <p class="font-medium">No status history available</p>
            <p class="text-sm">Status changes will appear here</p>
        </div>
        @endif
    </div>
</div>
@endsection
