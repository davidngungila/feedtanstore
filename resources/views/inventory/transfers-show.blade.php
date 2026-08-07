@extends('layouts.app')

@section('page-title', $stockTransfer->transfer_number)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">{{ $stockTransfer->transfer_number }}</h2>
            <div class="flex gap-3">
                <a href="{{ route('inventory.transfers.edit', $stockTransfer->id) }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Edit
                </a>
                <a href="{{ route('inventory.transfers') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Back to Transfers
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">Product</p>
                <p class="font-medium">{{ $stockTransfer->product->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Quantity</p>
                <p class="font-medium">{{ $stockTransfer->quantity }}</p>
            </div>
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
                <span class="badge badge-green">Completed</span>
            </div>
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500 mb-1">Notes</p>
                <p>{{ $stockTransfer->notes ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
