@extends('layouts.app')

@section('page-title', 'Receive Stock - {{ $purchaseOrder->po_number }}')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <a href="{{ route('storekeeper.stock-receiving') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Stock Receiving
            </a>
            <h1 class="text-2xl font-bold text-primary-900 mt-2">Receive Stock</h1>
            <p class="text-gray-600">PO: {{ $purchaseOrder->po_number }} - {{ $purchaseOrder->supplier->name }}</p>
        </div>
    </div>

    @if($errors->any())
        <div class="mb-6 p-3 bg-red-100 border border-red-400 text-red-800 rounded-lg">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('storekeeper.stock-receiving.store', $purchaseOrder) }}" method="POST" id="receive-form">
        @csrf
        <input type="hidden" name="purchase_order_id" value="{{ $purchaseOrder->id }}">

        <!-- Purchase Order Info -->
        <div class="card rounded-2xl p-6 mb-6 bg-blue-50 border border-blue-200">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-sm text-gray-600">PO Number</p>
                    <p class="font-semibold text-gray-900">{{ $purchaseOrder->po_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Supplier</p>
                    <p class="font-semibold text-gray-900">{{ $purchaseOrder->supplier->name }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Order Date</p>
                    <p class="font-semibold text-gray-900">{{ $purchaseOrder->created_at->format('M d, Y') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Status</p>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                        @if($purchaseOrder->status == 'received') bg-green-100 text-green-700
                        @elseif($purchaseOrder->status == 'partial') bg-yellow-100 text-yellow-700
                        @else bg-blue-100 text-blue-700
                        @endif">
                        {{ ucfirst($purchaseOrder->status) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Items to Receive -->
        <div class="card rounded-2xl overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h2 class="text-lg font-bold text-primary-900">Items to Receive</h2>
                <p class="text-sm text-gray-600 mt-1">Enter the quantity received for each item. Set to 0 if not received.</p>
            </div>
            <div class="p-6">
                @foreach($purchaseOrder->items as $item)
                    @php
                        $previouslyReceived = \App\Models\GrnItem::where('product_id', $item->product_id)
                            ->whereHas('goodsReceivedNote', function ($q) use ($purchaseOrder) {
                                $q->where('purchase_order_id', $purchaseOrder->id);
                            })->sum('quantity');
                        $remaining = $item->quantity - $previouslyReceived;
                    @endphp
                    <div class="product_item mb-6 p-4 border border-gray-200 rounded-lg {{ $remaining <= 0 ? 'bg-green-50' : '' }}">
                        <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-4">
                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                                <input type="hidden" name="received_items[{{ $item->id }}][product_id]" value="{{ $item->product_id }}">
                                <div class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed">
                                    {{ $item->product->name }} ({{ $item->product->sku ?? 'N/A' }})
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Ordered</label>
                                <input type="number" value="{{ $item->quantity }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed" readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Previously Received</label>
                                <input type="number" value="{{ $previouslyReceived }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed" readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Remaining</label>
                                <input type="number" value="{{ $remaining }}" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed {{ $remaining <= 0 ? 'text-green-600 font-medium' : 'text-red-600 font-medium' }}" readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Receiving Now *</label>
                                <input type="number"
                                       name="received_items[{{ $item->id }}][quantity]"
                                       value="{{ $remaining > 0 ? $remaining : 0 }}"
                                       min="0"
                                       max="{{ $remaining }}"
                                       {{ $remaining <= 0 ? 'disabled' : 'required' }}
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-center {{ $remaining <= 0 ? 'bg-gray-100' : '' }}">
                                @if($remaining <= 0)
                                    <input type="hidden" name="received_items[{{ $item->id }}][quantity]" value="0">
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price *</label>
                                @if($remaining > 0)
                                    <input type="number" step="0.01" min="0" name="received_items[{{ $item->id }}][unit_price]" value="{{ $item->unit_price }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-center" placeholder="Price per unit">
                                @else
                                    <input type="number" step="0.01" value="{{ $item->unit_price }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed text-center" readonly>
                                @endif
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                <input type="text"
                                       name="received_items[{{ $item->id }}][notes]"
                                       placeholder="Optional notes..."
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm"
                                       {{ $remaining <= 0 ? 'disabled' : '' }}>
                            </div>
                            <div class="flex items-end">
                                @if($remaining <= 0)
                                <span class="px-4 py-2 bg-green-100 border border-green-300 rounded-lg text-green-800 font-medium text-sm">
                                    <i class="fas fa-check-circle mr-1"></i>Fully Received
                                </span>
                                @endif
                            </div>
                        </div>
                        @if($remaining > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Batch Number</label>
                                <input type="text"
                                       name="received_items[{{ $item->id }}][batch_number]"
                                       maxlength="100"
                                       value="{{ $item->product->batch_number ?? '' }}"
                                       placeholder="Auto-generated if left blank"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">
                                    Expiry Date
                                    @if($item->product->category?->requires_expiry_date)
                                        <span class="text-red-500">*</span>
                                    @endif
                                </label>
                                <input type="date"
                                       name="received_items[{{ $item->id }}][expiry_date]"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm">
                            </div>
                        </div>
                        @endif
                    </div>
                @endforeach
            </div>

            @if($purchaseOrder->status == 'partial')
            <div class="p-4 bg-yellow-50 border-t border-gray-200">
                <p class="text-sm text-yellow-800">
                    <i class="fas fa-info-circle mr-2"></i>
                    This purchase order is partially received. You can receive the remaining quantities above.
                </p>
            </div>
            @endif
        </div>

        <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-gray-200">
            <a href="{{ route('storekeeper.stock-receiving') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <button type="submit" id="submitBtn" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                <i class="fas fa-check mr-2"></i>Confirm Receive Stock
            </button>
        </div>
    </form>
</div>

<script>
    document.getElementById('receive-form').addEventListener('submit', function(e) {
        const submitBtn = document.getElementById('submitBtn');
        if (submitBtn.disabled) {
            e.preventDefault();
            return false;
        }
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
        submitBtn.classList.remove('bg-primary-600', 'hover:bg-primary-700');
        submitBtn.classList.add('bg-gray-400', 'cursor-not-allowed');
    });
</script>
@endsection