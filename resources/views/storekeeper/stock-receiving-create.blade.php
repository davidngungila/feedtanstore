@extends('layouts.app')

@section('page-title', 'Receive Stock - {{ $purchaseOrder->order_number }}')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <a href="{{ route('storekeeper.stock-receiving') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Stock Receiving
            </a>
            <h1 class="text-2xl font-bold text-primary-900 mt-2">Receive Stock</h1>
            <p class="text-gray-600">PO: {{ $purchaseOrder->order_number }} - {{ $purchaseOrder->supplier->name }}</p>
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
                    <p class="font-semibold text-gray-900">{{ $purchaseOrder->order_number }}</p>
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
                <p class="text-sm text-gray-600 mt-1">Enter the quantity received for each item. Leave as 0 if not received.</p>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Product</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Ordered</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Previously Received</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Remaining</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Unit Price</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Received Now</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Notes</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($purchaseOrder->items as $item)
                            @php
                                $previouslyReceived = \App\Models\GrnItem::whereHas('goodsReceivedNote', function ($q) use ($purchaseOrder, $item) {
                                    $q->where('purchase_order_id', $purchaseOrder->id)
                                        ->where('product_id', $item->product_id);
                                })->sum('quantity_received');
                                $remaining = $item->quantity - $previouslyReceived;
                            @endphp
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <p class="font-semibold text-gray-900">{{ $item->product->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $item->product->sku ?? '' }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $item->quantity }} {{ $item->product->unit->short_name ?? 'pcs' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">{{ $previouslyReceived }} {{ $item->product->unit->short_name ?? 'pcs' }}</td>
                                <td class="px-6 py-4 text-sm font-medium {{ $remaining <= 0 ? 'text-green-600' : 'text-red-600' }}">{{ $remaining }} {{ $item->product->unit->short_name ?? 'pcs' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-600">TZS {{ number_format($item->unit_price, 0) }}</td>
                                <td class="px-6 py-4">
                                    <input type="number"
                                           name="received_items[{{ $item->id }}][quantity]"
                                           value="{{ $remaining > 0 ? $remaining : 0 }}"
                                           min="0"
                                           max="{{ $remaining > 0 ? $remaining : 0 }}"
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-center"
                                           @if($remaining <= 0) disabled @endif>
                                    @if($remaining <= 0)
                                        <input type="hidden" name="received_items[{{ $item->id }}][quantity]" value="0">
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <input type="text"
                                           name="received_items[{{ $item->id }}][notes]"
                                           placeholder="Optional notes..."
                                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 text-sm"
                                           @if($remaining <= 0) disabled @endif>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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

        <div class="mt-6 flex justify-end gap-3">
            <a href="{{ route('storekeeper.stock-receiving') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                Cancel
            </a>
            <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                <i class="fas fa-check mr-2"></i>Confirm Receive Stock
            </button>
        </div>
    </form>
</div>
@endsection