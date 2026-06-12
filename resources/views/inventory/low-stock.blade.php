@extends('layouts.app')

@section('page-title', 'Low Stock Alert')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Low Stock Alert</h2>
        </div>
        @if(count($products) > 0)
            <div class="overflow-x-auto">
                <table class="data-table w-full">
                    <thead>
                        <tr>
                            <th class="text-left">Name</th>
                            <th class="text-left">SKU</th>
                            <th class="text-left">Current Stock</th>
                            <th class="text-left">Reorder Level</th>
                            <th class="text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        <tr>
                            <td class="font-medium text-primary-900">{{ $product->name }}</td>
                            <td class="text-gray-600">{{ $product->sku ?? '-' }}</td>
                            <td class="font-semibold text-red-600">
                                {{ $product->quantity }} {{ $product->unit->short_name ?? '' }}
                            </td>
                            <td class="text-gray-600">{{ $product->reorder_level }}</td>
                            <td>
                                <span class="badge badge-red">Low Stock</span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
                <h3 class="text-lg font-semibold text-primary-900 mb-2">All products have sufficient stock!</h3>
                <p class="text-gray-600">No products are currently below reorder level.</p>
            </div>
        @endif
    </div>
</div>
@endsection