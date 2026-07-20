@extends('layouts.app')

@section('page-title', 'Stock Adjustment Details')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Stock Adjustment Details</h2>
            <a href="{{ route('inventory.adjustments') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Adjustments
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="text-sm text-gray-600 mb-1">Reference Number</div>
                <div class="font-semibold text-lg">{{ $adjustment->reference_number }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Adjustment Date</div>
                <div class="font-semibold">{{ $adjustment->adjustment_date }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Type</div>
                <div class="font-semibold">
                    <span class="badge {{ $adjustment->type === 'addition' ? 'badge-green' : 'badge-red' }}">
                        {{ $adjustment->type === 'addition' ? 'Addition' : 'Subtraction' }}
                    </span>
                </div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Reason</div>
                <div class="font-semibold">{{ $adjustment->reason }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Quantity Before</div>
                <div class="font-semibold text-lg">{{ $adjustment->quantity_before }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Quantity Change</div>
                <div class="font-semibold text-lg {{ $adjustment->type === 'addition' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $adjustment->quantity_change > 0 ? '+' : '' }}{{ $adjustment->quantity_change }}
                </div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Quantity After</div>
                <div class="font-semibold text-lg">{{ $adjustment->quantity_after }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Created At</div>
                <div class="font-semibold">{{ $adjustment->created_at->format('d/m/Y H:i:s') }}</div>
            </div>
        </div>

        @if($adjustment->notes)
        <div class="mt-6">
            <div class="text-sm text-gray-600 mb-1">Notes</div>
            <div class="font-semibold bg-gray-50 p-3 rounded-lg">{{ $adjustment->notes }}</div>
        </div>
        @endif
    </div>

    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-primary-900 mb-4">Product Information</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="text-sm text-gray-600 mb-1">Product Name</div>
                <div class="font-semibold">{{ $adjustment->product->name ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">SKU</div>
                <div class="font-semibold">{{ $adjustment->product->sku ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Barcode</div>
                <div class="font-semibold">{{ $adjustment->product->barcode ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Category</div>
                <div class="font-semibold">{{ $adjustment->product->category->name ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Brand</div>
                <div class="font-semibold">{{ $adjustment->product->brand->name ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Unit</div>
                <div class="font-semibold">{{ $adjustment->product->unit->name ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Current Stock</div>
                <div class="font-semibold">{{ $adjustment->product->quantity }} {{ $adjustment->product->unit->short_name ?? '' }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Reorder Level</div>
                <div class="font-semibold">{{ $adjustment->product->reorder_level }} {{ $adjustment->product->unit->short_name ?? '' }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
