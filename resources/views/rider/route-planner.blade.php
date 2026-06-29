@extends('layouts.app')

@section('page-title', 'Route Planner')

@section('content')
<div class="animate-[fadeIn_0.4s_ease] space-y-6">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="{{ route('rider.dashboard') }}" class="text-primary-600 hover:text-primary-800 mb-2 inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
                <h2 class="text-xl font-bold text-primary-900">Route Planner</h2>
            </div>
        </div>

        <div class="h-96 bg-gray-100 rounded-lg mb-6 flex items-center justify-center">
            <div class="text-center text-gray-500">
                <i class="fas fa-map-marked-alt text-5xl mb-4"></i>
                <p>Map integration goes here</p>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-primary-900 mb-4">Pending Deliveries</h3>
            <div class="space-y-3">
                @forelse($assignedOrders as $order)
                <div class="p-4 border rounded-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-semibold">{{ $order->short_customer_reference }}</div>
                            <div class="text-sm text-gray-600">{{ $order->customer_name }}</div>
                            <div class="text-sm text-gray-500">{{ $order->delivery_address }}</div>
                        </div>
                        <a href="{{ route('rider.orders.show', $order) }}" class="text-primary-600 hover:text-primary-800">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
                @empty
                <div class="text-center text-gray-500 py-8">
                    No pending deliveries.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection
