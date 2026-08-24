@extends('layouts.app')

@section('page-title', $stockTransfer->transfer_number)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">{{ $stockTransfer->transfer_number }}</h2>
            <div class="flex gap-3">
                @if(in_array(Auth::user()->role, ['admin', 'manager']) && in_array($stockTransfer->status, ['pending', 'rejected']))
                    <a href="/inventory/transfers/{{ $stockTransfer->id }}/edit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                        Edit
                    </a>
                @endif
                <a href="/inventory/transfers" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Back to Transfers
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @if($stockTransfer->items && $stockTransfer->items->count() > 0)
                <div class="md:col-span-2">
                    <p class="text-sm text-gray-500 mb-1">Products</p>
                    <div class="space-y-2">
                        @foreach($stockTransfer->items as $item)
                        <div class="flex justify-between items-center bg-gray-50 p-2 rounded">
                            <span class="font-medium">{{ $item->product->name ?? 'N/A' }}</span>
                            <span class="text-gray-600">{{ $item->quantity }} {{ $item->product->unit?->short_name ?? 'pcs' }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>
            @elseif($stockTransfer->product_id)
                <div>
                    <p class="text-sm text-gray-500 mb-1">Product</p>
                    <p class="font-medium">{{ $stockTransfer->product->name ?? 'N/A' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500 mb-1">Quantity</p>
                    <p class="font-medium">{{ $stockTransfer->quantity ?? 'N/A' }}</p>
                </div>
            @else
                <div class="md:col-span-2">
                    <p class="text-sm text-gray-500 mb-1">Products</p>
                    <p class="text-gray-600">No product information available for this transfer</p>
                </div>
            @endif
            <div>
                <p class="text-sm text-gray-500 mb-1">From Location</p>
                <p class="font-medium">{{ $stockTransfer->fromLocation->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">To Location</p>
                <p class="font-medium">{{ $stockTransfer->toLocation->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Transfer Date</p>
                <p class="font-medium">{{ $stockTransfer->created_at ? $stockTransfer->created_at->format('M d, Y') : '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Status</p>
                @if($stockTransfer->status === 'approved')
                    <span class="badge badge-blue">Approved</span>
                @elseif($stockTransfer->status === 'completed')
                    <span class="badge badge-green">Completed</span>
                @elseif($stockTransfer->status === 'rejected')
                    <span class="badge badge-red">Rejected</span>
                @else
                    <span class="badge badge-yellow">Pending</span>
                @endif
            </div>
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500 mb-1">Notes</p>
                <p>{{ $stockTransfer->notes ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
