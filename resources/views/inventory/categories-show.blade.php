@extends('layouts.app')

@section('page-title', $category->name)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">{{ $category->name }}</h2>
            <div class="flex gap-3">
                <a href="{{ route('inventory.categories.edit', $category) }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Edit
                </a>
                <a href="{{ route('inventory.categories') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Back to Categories
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">Name</p>
                <p class="font-medium">{{ $category->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Status</p>
                <span class="badge {{ $category->is_active ? 'badge-green' : 'badge-gray' }}">
                    {{ $category->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500 mb-1">Description</p>
                <p>{{ $category->description ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-primary-900 mb-6">Products in This Category</h3>
        @if($category->products->count() > 0)
            <div class="overflow-x-auto">
                <table class="data-table w-full">
                    <thead>
                        <tr>
                            <th class="text-left">Product Name</th>
                            <th class="text-left">SKU</th>
                            <th class="text-left">Quantity</th>
                            <th class="text-left">Cost Price</th>
                            <th class="text-left">Selling Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($category->products as $product)
                        <tr>
                            <td class="font-medium text-primary-900">
                                <a href="{{ route('inventory.products.show', $product) }}" class="hover:underline">{{ $product->name }}</a>
                            </td>
                            <td class="text-gray-600">{{ $product->sku ?? '-' }}</td>
                            <td class="text-gray-600">{{ $product->quantity }}</td>
                            <td class="text-gray-600">TZS {{ number_format($product->cost_price, 2) }}</td>
                            <td class="text-gray-600">TZS {{ number_format($product->selling_price, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-600 text-center py-8">No products are in this category yet.</p>
        @endif
    </div>
</div>
@endsection
