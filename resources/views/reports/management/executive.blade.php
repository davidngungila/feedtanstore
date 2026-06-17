@extends('layouts.app')

@section('page-title', 'Executive Dashboard')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class="text-xl font-bold text-primary-900">Executive Dashboard</h2>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-primary-200 flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-primary-700"></i>
                    </div>
                </div>
                <p class="text-sm text-primary-700 mb-1">Today's Sales</p>
                <h3 class="text-2xl font-bold text-primary-900">TZS {{ number_format($todaySales, 2) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-200 flex items-center justify-center">
                        <i class="fas fa-receipt text-blue-700"></i>
                    </div>
                </div>
                <p class="text-sm text-blue-700 mb-1">Today's Transactions</p>
                <h3 class="text-2xl font-bold text-blue-900">{{ $todayTransactions }}</h3>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-purple-200 flex items-center justify-center">
                        <i class="fas fa-chart-line text-purple-700"></i>
                    </div>
                </div>
                <p class="text-sm text-purple-700 mb-1">This Month's Sales</p>
                <h3 class="text-2xl font-bold text-purple-900">TZS {{ number_format($monthSales, 2) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-green-200 flex items-center justify-center">
                        <i class="fas fa-store text-green-700"></i>
                    </div>
                </div>
                <p class="text-sm text-green-700 mb-1">Total Inventory Value</p>
                <h3 class="text-2xl font-bold text-green-900">TZS {{ number_format($totalStockValue, 2) }}</h3>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Low Stock & Out of Stock -->
            <div class="border border-gray-100 rounded-xl p-5">
                <h4 class="font-semibold text-primary-900 mb-4">Stock Status</h4>
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700">Low Stock Items</span>
                        <span class="font-semibold text-yellow-700">{{ $lowStockCount }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700">Out of Stock Items</span>
                        <span class="font-semibold text-red-700">{{ $outOfStockCount }}</span>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="border border-gray-100 rounded-xl p-5">
                <h4 class="font-semibold text-primary-900 mb-4">Top Products (This Month)</h4>
                <div class="space-y-3">
                    @foreach($topProducts as $product)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700">{{ $product->name }}</span>
                        <span class="font-semibold text-primary-900">{{ $product->total_qty }} sold</span>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Recent Sales -->
        <div class="border border-gray-100 rounded-xl p-5">
            <h4 class="font-semibold text-primary-900 mb-4">Recent Sales</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-700 font-medium">Invoice #</th>
                            <th class="px-4 py-3 text-left text-gray-700 font-medium">Date</th>
                            <th class="px-4 py-3 text-left text-gray-700 font-medium">Customer</th>
                            <th class="px-4 py-3 text-left text-gray-700 font-medium">Cashier</th>
                            <th class="px-4 py-3 text-right text-gray-700 font-medium">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($recentSales as $sale)
                        <tr>
                            <td class="px-4 py-3 font-medium">#{{ $sale->id }}</td>
                            <td class="px-4 py-3">{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3">{{ $sale->customer ? $sale->customer->name : 'Walk-in' }}</td>
                            <td class="px-4 py-3">{{ $sale->user ? $sale->user->name : 'N/A' }}</td>
                            <td class="px-4 py-3 text-right">TZS {{ number_format($sale->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection