@extends('layouts.app')

@section('page-title', $grn->grn_number)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">{{ $grn->grn_number }}</h2>
            <div class="flex gap-3">
                <a href="{{ route('purchasing.grn.download', $grn) }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-download mr-2"></i>Download PDF
                </a>
                <a href="{{ route('purchasing.grn.edit', $grn) }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Edit
                </a>
                <a href="{{ route('purchasing.grn') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Back to GRNs
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">Supplier</p>
                <p class="font-medium">{{ $grn->supplier->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Purchase Order</p>
                <p class="font-medium">
                    @if($grn->purchaseOrder)
                        <a href="{{ route('purchasing.orders.show', $grn->purchaseOrder) }}" class="text-primary-600 hover:underline">{{ $grn->purchaseOrder->po_number }}</a>
                    @else
                        N/A
                    @endif
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Received Date</p>
                <p class="font-medium">{{ $grn->received_date ? date('M d, Y', strtotime($grn->received_date)) : '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Total Amount</p>
                <p class="font-bold text-lg">TZS {{ number_format($grn->total, 2) }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500 mb-1">Notes</p>
                <p>{{ $grn->notes ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-primary-900 mb-6">Received Products</h3>
        @if($grn->items->count() > 0)
            <div class="overflow-x-auto">
                <table class="data-table w-full">
                    <thead>
                        <tr>
                            <th class="text-left">Product</th>
                            <th class="text-left">Quantity</th>
                            <th class="text-left">Unit Price</th>
                            <th class="text-left">Total</th>
                            <th class="text-left">Expiry Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($grn->items as $item)
                        <tr>
                            <td class="font-medium text-primary-900">
                                <a href="{{ route('inventory.products.show', $item->product) }}" class="hover:underline">{{ $item->product->name ?? 'N/A' }}</a>
                            </td>
                            <td class="text-gray-600">{{ $item->quantity }}</td>
                            <td class="text-gray-600">TZS {{ number_format($item->unit_price, 2) }}</td>
                            <td class="text-gray-600">TZS {{ number_format($item->total, 2) }}</td>
                            <td class="text-gray-600">{{ $item->expiry_date ? date('M d, Y', strtotime($item->expiry_date)) : '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-600 text-center py-8">No products in this GRN.</p>
        @endif
    </div>
</div>
@endsection
