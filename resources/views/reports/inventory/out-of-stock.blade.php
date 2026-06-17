@extends('layouts.app')

@section('page-title', 'Out of Stock')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class="text-xl font-bold text-primary-900">Out of Stock Products</h2>
            <div class="flex items-center gap-3">
                <button class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">
                    Export PDF
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-red-200 flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-700"></i>
                    </div>
                </div>
                <p class="text-sm text-red-700 mb-1">Out of Stock Items</p>
                <h3 class="text-2xl font-bold text-red-900">{{ $products->count() }}</h3>
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
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Unit Cost</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Unit Price</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($products as $product)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $product->name }}</td>
                        <td class="px-4 py-3">{{ $product->category ? $product->category->name : 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $product->brand ? $product->brand->name : 'N/A' }}</td>
                        <td class="px-4 py-3 text-right">TZS {{ number_format($product->cost_price, 2) }}</td>
                        <td class="px-4 py-3 text-right">TZS {{ number_format($product->selling_price, 2) }}</td>
                    </tr>
                    @endforeach
                    @if($products->isEmpty())
                    <tr>
                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                            No out of stock products found
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
