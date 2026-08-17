@extends('layouts.app')

@section('page-title', 'Purchase Order Details')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <a href="{{ route('storekeeper.purchase-orders') }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Purchase Orders
        </a>
    </div>

    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-primary-900">{{ $purchaseOrder->po_number }}</h1>
                <p class="text-gray-600">Purchase Order Details</p>
            </div>
            <span class="px-3 py-1 text-sm font-semibold rounded-full 
                {{ $purchaseOrder->status == 'received' ? 'bg-green-100 text-green-700' : 
                   ($purchaseOrder->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                   ($purchaseOrder->status == 'cancelled' ? 'bg-red-100 text-red-700' : 
                   'bg-gray-100 text-gray-700')) }}">
                {{ ucfirst($purchaseOrder->status) }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">Supplier</p>
                <p class="font-semibold">{{ $purchaseOrder->supplier->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Order Date</p>
                <p class="font-semibold">{{ $purchaseOrder->order_date ? $purchaseOrder->order_date->format('M d, Y') : '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Expected Date</p>
                <p class="font-semibold">{{ $purchaseOrder->expected_date ? $purchaseOrder->expected_date->format('M d, Y') : '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Created By</p>
                <p class="font-semibold">{{ $purchaseOrder->createdBy->name ?? 'N/A' }}</p>
            </div>
            @if($purchaseOrder->approved_by)
            <div>
                <p class="text-sm text-gray-500 mb-1">Approved By</p>
                <p class="font-semibold">{{ $purchaseOrder->approvedBy->name ?? 'N/A' }}</p>
            </div>
            @endif
            @if($purchaseOrder->approved_at)
            <div>
                <p class="text-sm text-gray-500 mb-1">Approved At</p>
                <p class="font-semibold">{{ $purchaseOrder->approved_at->format('M d, Y H:i') }}</p>
            </div>
            @endif
        </div>

        @if($purchaseOrder->notes)
        <div class="mt-6">
            <p class="text-sm text-gray-500 mb-1">Notes</p>
            <p class="text-gray-600">{{ $purchaseOrder->notes }}</p>
        </div>
        @endif
    </div>

    <!-- Order Items -->
    <div class="card rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-bold text-primary-900">Order Items</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Quantity</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($purchaseOrder->items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $item->product->name ?? 'N/A' }}</p>
                            <p class="text-sm text-gray-500">{{ $item->product->sku ?? '' }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->quantity }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
