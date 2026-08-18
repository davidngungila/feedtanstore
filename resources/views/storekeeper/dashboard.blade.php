@extends('layouts.app')

@section('page-title', 'Storekeeper Dashboard')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-primary-900">Storekeeper Dashboard</h1>
        <p class="text-gray-600">Manage store operations and inventory</p>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Total Products</p>
                    <p class="text-3xl font-bold text-primary-900">{{ number_format($totalProducts) }}</p>
                </div>
                <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-box text-primary-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Low Stock</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ number_format($lowStockProducts) }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Out of Stock</p>
                    <p class="text-3xl font-bold text-red-600">{{ number_format($outOfStockProducts) }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>

        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Stock Value</p>
                    <p class="text-3xl font-bold text-green-600">TZS {{ number_format($totalStockValue, 0) }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Low Stock Items -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-primary-900">Low Stock Items</h2>
                <a href="{{ route('storekeeper.stock') }}" class="text-primary-600 hover:text-primary-700 text-sm">View All</a>
            </div>
            <div class="space-y-3">
                @if($lowStockItems->count() > 0)
                    @foreach($lowStockItems as $item)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $item->name }}</p>
                            <p class="text-sm text-gray-600">{{ $item->category->name ?? 'No Category' }}</p>
                        </div>
                        <div class="text-right">
                            <p class="font-bold {{ $item->quantity <= 0 ? 'text-red-600' : 'text-yellow-600' }}">
                                {{ $item->quantity }} {{ $item->unit?->short_name ?? 'pcs' }}
                            </p>
                        </div>
                    </div>
                    @endforeach
                @else
                    <p class="text-gray-500 text-center py-4">No low stock items</p>
                @endif
            </div>
        </div>

        <!-- Recent Purchase Orders -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-bold text-primary-900">Recent Purchase Orders</h2>
                <a href="{{ route('storekeeper.purchase-orders') }}" class="text-primary-600 hover:text-primary-700 text-sm">View All</a>
            </div>
            <div class="space-y-3">
                @if($recentPurchaseOrders->count() > 0)
                    @foreach($recentPurchaseOrders as $order)
                    <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $order->order_number }}</p>
                            <p class="text-sm text-gray-600">{{ $order->supplier->name ?? 'No Supplier' }}</p>
                        </div>
                        <div class="text-right">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $order->status == 'received' ? 'bg-green-100 text-green-700' : 
                                   ($order->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                   'bg-gray-100 text-gray-700') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>
                    @endforeach
                @else
                    <p class="text-gray-500 text-center py-4">No recent purchase orders</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mt-8">
        <h2 class="text-lg font-bold text-primary-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <a href="{{ route('storekeeper.products') }}" class="card rounded-xl p-4 hover:shadow-lg transition-shadow">
                <div class="text-center">
                    <i class="fas fa-boxes text-3xl text-primary-600 mb-2"></i>
                    <p class="font-semibold text-gray-800">Products</p>
                </div>
            </a>
            <a href="{{ route('storekeeper.stock') }}" class="card rounded-xl p-4 hover:shadow-lg transition-shadow">
                <div class="text-center">
                    <i class="fas fa-warehouse text-3xl text-yellow-600 mb-2"></i>
                    <p class="font-semibold text-gray-800">Stock</p>
                </div>
            </a>
            <a href="{{ route('storekeeper.purchase-orders') }}" class="card rounded-xl p-4 hover:shadow-lg transition-shadow">
                <div class="text-center">
                    <i class="fas fa-shopping-cart text-3xl text-green-600 mb-2"></i>
                    <p class="font-semibold text-gray-800">Purchase Orders</p>
                </div>
            </a>
        </div>
    </div>
</div>
@endsection
