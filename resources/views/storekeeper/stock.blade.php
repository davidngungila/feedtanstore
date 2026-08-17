@extends('layouts.app')

@section('page-title', 'Stock Management')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-primary-900">Stock Management</h1>
        <p class="text-gray-600">Monitor and manage inventory levels</p>
    </div>

    <!-- Stock Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Low Stock Items</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $products->count() }}</p>
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
                    <p class="text-3xl font-bold text-red-600">{{ $products->where('quantity', '<=', 0)->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Critical Stock</p>
                    <p class="text-3xl font-bold text-orange-600">{{ $products->where('quantity', '>', 0)->where('quantity', '<=', 5)->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-exclamation text-orange-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Total Products</p>
                    <p class="text-3xl font-bold text-primary-600">{{ \App\Models\Product::count() }}</p>
                </div>
                <div class="w-12 h-12 bg-primary-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-boxes text-primary-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Low Stock Products Table -->
    <div class="card rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-bold text-primary-900">Low Stock Products</h2>
            <p class="text-sm text-gray-600">Products with quantity ≤ 10</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Current Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Reorder Level</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900">{{ $product->name }}</p>
                            <p class="text-sm text-gray-500">{{ $product->sku }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $product->category->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $product->quantity <= 0 ? 'bg-red-100 text-red-700' : 
                                   ($product->quantity <= 5 ? 'bg-orange-100 text-orange-700' : 
                                   'bg-yellow-100 text-yellow-700') }}">
                                {{ $product->quantity }} {{ $product->unit?->short_name ?? 'pcs' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $product->reorder_level ?? 'N/A' }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $product->quantity <= 0 ? 'bg-red-100 text-red-700' : 
                                   ($product->quantity <= $product->reorder_level ? 'bg-orange-100 text-orange-700' : 
                                   'bg-yellow-100 text-yellow-700') }}">
                                {{ $product->quantity <= 0 ? 'Out of Stock' : 
                                   ($product->quantity <= $product->reorder_level ? 'Below Reorder Level' : 
                                   'Low Stock') }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('stock-requests.create') }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">Reorder</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $products->links() }}
        </div>
    </div>
</div>
@endsection
