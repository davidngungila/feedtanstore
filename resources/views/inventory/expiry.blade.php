@extends('layouts.app')

@section('page-title', 'Expiry Management')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Expiry Management</h2>
        </div>
        @if(count($products) > 0)
            <div class="overflow-x-auto">
                <table class="data-table w-full">
                    <thead>
                        <tr>
                            <th class="text-left">Name</th>
                            <th class="text-left">SKU</th>
                            <th class="text-left">Quantity</th>
                            <th class="text-left">Expiry Date</th>
                            <th class="text-left">Status</th>
                            <th class="text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                        @php
                            $expiryDate = \Carbon\Carbon::parse($product->expiry_date);
                            $today = \Carbon\Carbon::today();
                            $daysUntilExpiry = $today->diffInDays($expiryDate, false);
                        @endphp
                        <tr>
                            <td class="font-medium text-primary-900">{{ $product->name }}</td>
                            <td class="text-gray-600">{{ $product->sku ?? '-' }}</td>
                            <td class="text-gray-600">{{ $product->quantity }}</td>
                            <td class="text-gray-600">{{ $product->expiry_date }}</td>
                            <td>
                                @if($daysUntilExpiry < 0)
                                    <span class="badge badge-red">Expired</span>
                                @elseif($daysUntilExpiry <= 30)
                                    <span class="badge badge-yellow">Expiring Soon</span>
                                @else
                                    <span class="badge badge-green">Good</span>
                                @endif
                            </td>
                            <td class="flex items-center gap-2">
                                <a href="{{ route('inventory.products.show', $product) }}" class="text-primary-600 hover:text-primary-800 p-1" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('inventory.products.edit', $product) }}" class="text-primary-600 hover:text-primary-800 p-1" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
                <h3 class="text-lg font-semibold text-primary-900 mb-2">No products with expiry dates!</h3>
                <p class="text-gray-600">All products are either non-perishable or don't have expiry dates set.</p>
            </div>
        @endif
    </div>
</div>
@endsection