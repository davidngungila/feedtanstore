@extends('layouts.app')

@section('page-title', 'Edit Purchase Request')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <a href="{{ route('purchasing.purchase-requests.show', $purchaseOrderRequest) }}" class="text-primary-600 hover:text-primary-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Request Details
        </a>
    </div>

    <div class="card rounded-2xl p-6 max-w-5xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-primary-900">Edit Purchase Request</h1>
            <p class="text-gray-600 mt-1">{{ $baseNumber }} — {{ $orderItems->count() }} product(s) in this submission. You can edit every detail before approval.</p>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-800 rounded-lg">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('purchasing.purchase-requests.update', $purchaseOrderRequest) }}" method="POST" id="editForm">
            @csrf
            @method('PUT')

            @foreach($orderItems as $index => $item)
                @php
                    $unitPrice = $item->estimated_cost !== null && $item->requested_quantity > 0
                        ? round($item->estimated_cost / $item->requested_quantity, 2)
                        : null;
                @endphp
                <div class="border border-gray-200 rounded-xl p-5 mb-4 item-card" data-item-row>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-semibold text-primary-900">
                            <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-primary-600 text-white text-sm mr-2">{{ $index + 1 }}</span>
                            {{ $item->product->name ?? 'N/A' }}
                            @if($item->product?->sku)
                                <span class="text-xs text-gray-500 font-normal ml-2">SKU: {{ $item->product->sku }}</span>
                            @endif
                        </h3>
                        <p class="text-xs text-gray-500">{{ $item->request_number }} · Stock: {{ $item->product->quantity ?? 0 }} {{ $item->product->unit->short_name ?? 'pcs' }}</p>
                    </div>

                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Requested Quantity *</label>
                            <input type="number" name="items[{{ $index }}][requested_quantity]" step="0.01" min="1" value="{{ old("items.{$index}.requested_quantity", $item->requested_quantity) }}" required data-item-qty class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Estimated Unit Price</label>
                            <input type="number" name="items[{{ $index }}][estimated_unit_price]" step="0.01" min="0" value="{{ old("items.{$index}.estimated_unit_price", $unitPrice) }}" data-item-price class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Line Total</label>
                            <div class="px-4 py-2 bg-primary-50 border border-primary-200 rounded-lg font-semibold text-primary-900" data-item-total>0.00</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                        <select name="items[{{ $index }}][supplier_id]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">No supplier selected</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ old("items.{$index}.supplier_id", $item->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reason *</label>
                        <textarea name="items[{{ $index }}][reason]" rows="2" required maxlength="2000" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old("items.{$index}.reason", $item->reason) }}</textarea>
                    </div>

                    @if($orderItems->count() > 1)
                        <label class="mt-4 flex items-center gap-2 text-sm text-red-600 cursor-pointer">
                            <input type="checkbox" name="remove_ids[]" value="{{ $item->id }}" class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            Remove this product from the order
                        </label>
                    @endif
                </div>
            @endforeach

            <div class="flex flex-wrap items-center justify-between gap-4 mt-6 p-4 bg-gray-50 rounded-xl">
                <div>
                    <p class="text-sm text-gray-600">Total Estimated Cost ({{ $orderItems->count() }} products)</p>
                    <p id="grand_total" class="text-2xl font-bold text-primary-900">0.00 TZS</p>
                </div>
                <div class="flex gap-3">
                    <a href="{{ route('purchasing.purchase-requests.show', $purchaseOrderRequest) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors" onclick="return confirmSave();">
                        <i class="fas fa-save mr-2"></i>Save All Changes
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function computeRow(card) {
    const qty = parseFloat(card.querySelector('[data-item-qty]').value) || 0;
    const price = parseFloat(card.querySelector('[data-item-price]').value) || 0;
    card.querySelector('[data-item-total]').textContent = (qty * price).toFixed(2);
    return qty * price;
}

function updateAllTotals() {
    let grand = 0;
    document.querySelectorAll('[data-item-row]').forEach(function (card) {
        const removed = card.querySelector('input[name="remove_ids[]"]')?.checked;
        if (!removed) grand += computeRow(card);
    });
    document.getElementById('grand_total').textContent = grand.toFixed(2) + ' TZS';
}

function confirmSave() {
    const removedCount = document.querySelectorAll('input[name="remove_ids[]"]:checked').length;
    return !removedCount || confirm('Remove ' + removedCount + ' product(s) from this purchase order? This cannot be undone.');
}

document.querySelectorAll('[data-item-row]').forEach(function (card) {
    card.querySelectorAll('[data-item-qty], [data-item-price]').forEach(function (el) {
        el.addEventListener('input', updateAllTotals);
    });
    const removeBox = card.querySelector('input[name="remove_ids[]"]');
    if (removeBox) removeBox.addEventListener('change', updateAllTotals);
});
updateAllTotals();
</script>
@endsection
