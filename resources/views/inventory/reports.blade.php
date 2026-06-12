@extends('layouts.app')

@section('page-title', 'Inventory Reports')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <h2 class="text-xl font-bold text-primary-900 mb-6">Inventory Reports</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            <div class="bg-primary-50 rounded-xl p-4 border border-primary-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Products</p>
                        <p class="text-2xl font-bold text-primary-900">{{ $totalProducts }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-primary-200 flex items-center justify-center">
                        <i class="fas fa-box text-primary-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-green-50 rounded-xl p-4 border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Inventory Value (Cost)</p>
                        <p class="text-2xl font-bold text-green-900">TZS {{ number_format($totalValue, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-green-200 flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-green-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-blue-50 rounded-xl p-4 border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Total Potential Revenue</p>
                        <p class="text-2xl font-bold text-blue-900">TZS {{ number_format($totalSellValue, 2) }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-blue-200 flex items-center justify-center">
                        <i class="fas fa-chart-line text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-yellow-50 rounded-xl p-4 border border-yellow-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Low Stock Products</p>
                        <p class="text-2xl font-bold text-yellow-900">{{ $lowStockCount }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-yellow-200 flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="bg-red-50 rounded-xl p-4 border border-red-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Out of Stock Products</p>
                        <p class="text-2xl font-bold text-red-900">{{ $outOfStockCount }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-full bg-red-200 flex items-center justify-center">
                        <i class="fas fa-minus-circle text-red-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection