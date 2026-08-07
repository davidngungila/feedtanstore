@extends('layouts.app')

@section('page-title', 'Marketing Officer Dashboard')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-primary-900">Marketing Officer Dashboard</h1>
        <p class="text-gray-600">Manage online orders and delivery operations</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Total Orders</p>
                    <p class="text-3xl font-bold text-primary-900">{{ number_format($totalOrders) }}</p>
                </div>
                <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-shopping-bag text-primary-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Pending</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ number_format($pendingOrders) }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Out for Delivery</p>
                    <p class="text-3xl font-bold text-blue-600">{{ number_format($outForDeliveryOrders) }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-motorcycle text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Total Revenue</p>
                    <p class="text-3xl font-bold text-green-600">TZS {{ number_format($totalRevenue, 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-chart-line text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Order Status Overview -->
    <div class="card rounded-2xl p-6 mb-6">
        <h2 class="text-lg font-bold text-primary-900 mb-4">Order Status Overview</h2>
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
            <div class="text-center p-4 bg-gray-50 rounded-lg">
                <p class="text-2xl font-bold text-gray-600">{{ $ordersByStatus['pending'] ?? 0 }}</p>
                <p class="text-sm text-gray-600">Pending</p>
            </div>
            <div class="text-center p-4 bg-blue-50 rounded-lg">
                <p class="text-2xl font-bold text-blue-600">{{ $ordersByStatus['confirmed'] ?? 0 }}</p>
                <p class="text-sm text-blue-600">Confirmed</p>
            </div>
            <div class="text-center p-4 bg-orange-50 rounded-lg">
                <p class="text-2xl font-bold text-orange-600">{{ $ordersByStatus['preparing'] ?? 0 }}</p>
                <p class="text-sm text-orange-600">Preparing</p>
            </div>
            <div class="text-center p-4 bg-purple-50 rounded-lg">
                <p class="text-2xl font-bold text-purple-600">{{ $ordersByStatus['ready'] ?? 0 }}</p>
                <p class="text-sm text-purple-600">Ready</p>
            </div>
            <div class="text-center p-4 bg-indigo-50 rounded-lg">
                <p class="text-2xl font-bold text-indigo-600">{{ $ordersByStatus['out_for_delivery'] ?? 0 }}</p>
                <p class="text-sm text-indigo-600">Out for Delivery</p>
            </div>
            <div class="text-center p-4 bg-green-50 rounded-lg">
                <p class="text-2xl font-bold text-green-600">{{ $ordersByStatus['delivered'] ?? 0 }}</p>
                <p class="text-sm text-green-600">Delivered</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Orders -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-primary-900">Recent Orders</h2>
                <a href="{{ route('marketing-officer.orders') }}" class="text-primary-600 hover:text-primary-700 text-sm">View All</a>
            </div>
            <div class="space-y-3">
                @if($recentOrders->count() > 0)
                    @foreach($recentOrders as $order)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $order->order_number }}</p>
                            <p class="text-sm text-gray-600">{{ $order->customer_name }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $order->status == 'delivered' ? 'bg-green-100 text-green-700' : 
                                   $order->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                   $order->status == 'cancelled' ? 'bg-red-100 text-red-700' : 
                                   'bg-blue-100 text-blue-700' }}">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                @else
                    <p class="text-gray-500 text-center py-4">No recent orders</p>
                @endif
            </div>
        </div>

        <!-- Rider Status -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-primary-900">Rider Status</h2>
                <a href="{{ route('marketing-officer.riders') }}" class="text-primary-600 hover:text-primary-700 text-sm">View All</a>
            </div>
            <div class="space-y-4">
                <div class="flex items-center justify-between p-4 bg-green-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-200 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-check text-green-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Available Riders</p>
                            <p class="text-sm text-gray-600">Ready for deliveries</p>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-green-600">{{ $availableRiders }}</p>
                </div>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-users text-gray-600"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-800">Total Riders</p>
                            <p class="text-sm text-gray-600">All registered riders</p>
                        </div>
                    </div>
                    <p class="text-2xl font-bold text-gray-600">{{ $totalRiders }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8">
        <h2 class="text-lg font-bold text-primary-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('marketing-officer.orders') }}" class="card rounded-xl p-4 hover:shadow-lg transition-shadow">
                <div class="text-center">
                    <i class="fas fa-list-alt text-3xl text-primary-600 mb-2"></i>
                    <p class="font-semibold text-gray-800">All Orders</p>
                </div>
            </a>
            <a href="{{ route('marketing-officer.customers') }}" class="card rounded-xl p-4 hover:shadow-lg transition-shadow">
                <div class="text-center">
                    <i class="fas fa-users text-3xl text-blue-600 mb-2"></i>
                    <p class="font-semibold text-gray-800">Customers</p>
                </div>
            </a>
            <a href="{{ route('marketing-officer.riders') }}" class="card rounded-xl p-4 hover:shadow-lg transition-shadow">
                <div class="text-center">
                    <i class="fas fa-motorcycle text-3xl text-green-600 mb-2"></i>
                    <p class="font-semibold text-gray-800">Riders</p>
                </div>
            </a>
            <a href="{{ route('delivery-management.index') }}" class="card rounded-xl p-4 hover:shadow-lg transition-shadow">
                <div class="text-center">
                    <i class="fas fa-map-marked-alt text-3xl text-purple-600 mb-2"></i>
                    <p class="font-semibold text-gray-800">Delivery Map</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
