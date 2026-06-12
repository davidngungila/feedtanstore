@extends('layouts.app')

@section('page-title', $product->name)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">{{ $product->name }}</h2>
            <div class="flex gap-3">
                <a href="{{ route('inventory.products.edit', $product) }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Edit
                </a>
                <a href="{{ route('inventory.products') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Back to Products
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">SKU</p>
                <p class="font-medium">{{ $product->sku ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Barcode</p>
                <p class="font-medium">{{ $product->barcode ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Category</p>
                <p class="font-medium">{{ $product->category->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Brand</p>
                <p class="font-medium">{{ $product->brand->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Unit</p>
                <p class="font-medium">{{ $product->unit->name ?? '-' }} ({{ $product->unit->short_name ?? '-' }})</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Quantity in Stock</p>
                <p class="font-medium {{ $product->quantity <= $product->reorder_level ? 'text-red-600' : '' }}">
                    {{ $product->quantity }} {{ $product->unit->short_name ?? '' }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Cost Price</p>
                <p class="font-medium">TZS {{ number_format($product->cost_price, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Selling Price</p>
                <p class="font-medium">TZS {{ number_format($product->selling_price, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Reorder Level</p>
                <p class="font-medium">{{ $product->reorder_level }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Expiry Date</p>
                <p class="font-medium">{{ $product->expiry_date ? date('M d, Y', strtotime($product->expiry_date)) : '-' }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500 mb-1">Description</p>
                <p>{{ $product->description ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-primary-900 mb-6">Purchase History (GRNs)</h3>
        @if($product->grnItems->count() > 0)
            <div class="overflow-x-auto">
                <table class="data-table w-full">
                    <thead>
                        <tr>
                            <th class="text-left">GRN Number</th>
                            <th class="text-left">Supplier</th>
                            <th class="text-left">Date Received</th>
                            <th class="text-left">Quantity</th>
                            <th class="text-left">Unit Price</th>
                            <th class="text-left">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($product->grnItems as $item)
                            <tr>
                                <td class="font-medium">{{ $item->goodsReceivedNote->grn_number }}</td>
                                <td>{{ $item->goodsReceivedNote->supplier->name ?? '-' }}</td>
                                <td>{{ $item->goodsReceivedNote->received_date ? date('M d, Y', strtotime($item->goodsReceivedNote->received_date)) : '-' }}</td>
                                <td>{{ $item->quantity }}</td>
                                <td>TZS {{ number_format($item->unit_price, 2) }}</td>
                                <td>TZS {{ number_format($item->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-center py-8">No purchase history available for this product.</p>
        @endif
    </div>
</div>
@endsection
