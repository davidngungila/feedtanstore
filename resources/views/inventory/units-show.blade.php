@extends('layouts.app')

@section('page-title', $unit->name)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">{{ $unit->name }}</h2>
            <div class="flex gap-3">
                <a href="{{ route('inventory.units.edit', $unit) }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Edit
                </a>
                <a href="{{ route('inventory.units') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Back to Units
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">Name</p>
                <p class="font-medium">{{ $unit->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Short Name</p>
                <p class="font-medium">{{ $unit->short_name }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500 mb-1">Description</p>
                <p>{{ $unit->description ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Status</p>
                <span class="badge {{ $unit->is_active ? 'badge-green' : 'badge-gray' }}">
                    {{ $unit->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>
    </div>

    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-primary-900 mb-6">Products Using This Unit</h3>
        @if($unit->products->count() > 0)
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
                        @foreach($unit->products as $product)
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
            <p class="text-gray-600 text-center py-8">No products are using this unit yet.</p>
        @endif
    </div>
</div>
@endsection
