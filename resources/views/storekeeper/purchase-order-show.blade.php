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
    <div class="card rounded-2xl overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-bold text-primary-900">Order Items</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Ordered</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Received</th>
                        <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Remaining</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($purchaseOrder->items as $item)
                        @php
                            $received = (int) ($receivedByProduct[$item->product_id] ?? 0);
                            $remaining = max(0, $item->quantity - $received);
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $item->product->name ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-500">{{ $item->product->sku ?? '' }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 text-center">{{ $item->quantity }} {{ $item->product->unit->short_name ?? '' }}</td>
                            <td class="px-6 py-4 text-sm font-semibold {{ $received >= $item->quantity ? 'text-green-600' : ($received > 0 ? 'text-yellow-600' : 'text-gray-400') }} text-center">
                                {{ $received }}
                            </td>
                            <td class="px-6 py-4 text-sm text-center">
                                @if($remaining <= 0)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700"><i class="fas fa-check-circle mr-1"></i>Fully Received</span>
                                @else
                                    <span class="font-medium text-red-600">{{ $remaining }}</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Goods Received Notes -->
    <div class="card rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-bold text-primary-900">Goods Received Notes</h2>
            <span class="text-sm text-gray-500">{{ $purchaseOrder->goodsReceivedNotes->count() }} note(s)</span>
        </div>
        @if($purchaseOrder->goodsReceivedNotes->count() > 0)
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">GRN Number</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Received Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Products</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($purchaseOrder->goodsReceivedNotes as $grn)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-medium text-primary-900">{{ $grn->grn_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $grn->received_date?->format('M d, Y') ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $grn->items->map(fn($gi) => $gi->product->name . ' (' . $gi->quantity . ')')->implode(', ') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900 text-right">{{ number_format($grn->total, 2) }}</td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $grn->status === 'received' || $grn->status === 'accepted' ? 'bg-green-100 text-green-700' :
                                       ($grn->status === 'pending' ? 'bg-yellow-100 text-yellow-700' : 'bg-red-100 text-red-700') }}">
                                    {{ ucfirst($grn->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <a href="{{ route('purchasing.grn.show', $grn) }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                                    View <i class="fas fa-arrow-right ml-1 text-xs"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="p-8 text-center">
                <i class="fas fa-box-open text-3xl text-gray-300 mb-3"></i>
                <p class="text-gray-500">No goods received yet for this purchase order.</p>
                @if(in_array($purchaseOrder->status, ['pending', 'partial', 'approved', 'sent']))
                    <a href="{{ route('storekeeper.stock-receiving.create', $purchaseOrder) }}" class="inline-block mt-3 text-primary-600 hover:text-primary-800 text-sm font-medium">
                        <i class="fas fa-truck-loading mr-1"></i>Receive Stock
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>
@endsection
