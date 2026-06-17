@extends('layouts.app')

@section('page-title', 'Current Stock')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class="text-xl font-bold text-primary-900">Current Stock</h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('reports.inventory.current-stock.download') }}" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">
                    Export PDF
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-primary-200 flex items-center justify-center">
                        <i class="fas fa-boxes text-primary-700"></i>
                    </div>
                </div>
                <p class="text-sm text-primary-700 mb-1">Total Products</p>
                <h3 class="text-2xl font-bold text-primary-900">{{ $products->count() }}</h3>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-200 flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-blue-700"></i>
                    </div>
                </div>
                <p class="text-sm text-blue-700 mb-1">Total Stock Value</p>
                <h3 class="text-2xl font-bold text-blue-900">TZS {{ number_format($totalStockValue, 2) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-purple-200 flex items-center justify-center">
                        <i class="fas fa-tags text-purple-700"></i>
                    </div>
                </div>
                <p class="text-sm text-purple-700 mb-1">Low Stock Items</p>
                <h3 class="text-2xl font-bold text-purple-900">{{ $products->filter(fn($p) => $p->quantity <= $p->reorder_level)->count() }}</h3>
            </div>
        </div>

        <!-- Products Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Product</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Category</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Brand</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Quantity</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Cost Price</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Selling Price</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Stock Value</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($products as $product)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $product->name }}</td>
                        <td class="px-4 py-3">{{ $product->category ? $product->category->name : 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $product->brand ? $product->brand->name : 'N/A' }}</td>
                        <td class="px-4 py-3 text-right font-semibold">{{ number_format($product->quantity) }}</td>
                        <td class="px-4 py-3 text-right">TZS {{ number_format($product->cost_price, 2) }}</td>
                        <td class="px-4 py-3 text-right">TZS {{ number_format($product->selling_price, 2) }}</td>
                        <td class="px-4 py-3 text-right font-semibold">TZS {{ number_format($product->quantity * $product->cost_price, 2) }}</td>
                        <td class="px-4 py-3">
                            @if($product->quantity == 0)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">Out of Stock</span>
                            @elseif($product->quantity <= $product->reorder_level)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">Low Stock</span>
                            @else
                                <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">In Stock</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
