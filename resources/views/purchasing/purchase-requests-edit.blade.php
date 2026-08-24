@extends('layouts.app')

@section('page-title', 'Edit Purchase Request')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <a href="{{ route('purchasing.purchase-requests.show', $purchaseOrderRequest) }}" class="text-primary-600 hover:text-primary-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Request Details
        </a>
    </div>

    <div class="card rounded-2xl p-6 max-w-3xl mx-auto">
        <div class="mb-6">
            <h1 class="text-2xl font-bold text-primary-900">Edit Purchase Request</h1>
            <p class="text-gray-600 mt-1">{{ $purchaseOrderRequest->request_number }} — you can adjust details before approval</p>
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

        <form action="{{ route('purchasing.purchase-requests.update', $purchaseOrderRequest) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="mb-6 p-4 bg-gray-50 rounded-xl grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Product (read-only)</p>
                    <p class="font-semibold">{{ $purchaseOrderRequest->product->name ?? 'N/A' }}</p>
                    @if($purchaseOrderRequest->product?->sku)
                        <p class="text-xs text-gray-500">SKU: {{ $purchaseOrderRequest->product->sku }}</p>
                    @endif
                </div>
                <div>
                    <p class="text-sm text-gray-600">Current Stock</p>
                    <p class="font-semibold">{{ $purchaseOrderRequest->product->quantity ?? 0 }} {{ $purchaseOrderRequest->product->unit->short_name ?? 'pcs' }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Requested Quantity *</label>
                    <input type="number" name="requested_quantity" id="qty_input" step="0.01" min="1" value="{{ old('requested_quantity', $purchaseOrderRequest->requested_quantity) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Requested Supplier</label>
                    <select name="supplier_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">No supplier selected</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchaseOrderRequest->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-500 mt-1">You will confirm the final supplier when approving.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estimated Unit Price</label>
                    <input type="number" name="estimated_unit_price" id="price_input" step="0.01" min="0" value="{{ old('estimated_unit_price', $purchaseOrderRequest->estimated_cost !== null && $purchaseOrderRequest->requested_quantity > 0 ? round($purchaseOrderRequest->estimated_cost / $purchaseOrderRequest->requested_quantity, 2) : null) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Estimated Total Cost</label>
                    <div id="total_display" class="px-4 py-2 bg-primary-50 border border-primary-200 rounded-lg font-semibold text-primary-900">0.00</div>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason *</label>
                <textarea name="reason" rows="3" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('reason', $purchaseOrderRequest->reason) }}</textarea>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('purchasing.purchase-requests.show', $purchaseOrderRequest) }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-save mr-2"></i>Save Changes
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const qtyInput = document.getElementById('qty_input');
const priceInput = document.getElementById('price_input');
const totalDisplay = document.getElementById('total_display');

function updateTotal() {
    const qty = parseFloat(qtyInput.value) || 0;
    const price = parseFloat(priceInput.value) || 0;
    totalDisplay.textContent = (qty * price).toFixed(2);
}
qtyInput.addEventListener('input', updateTotal);
priceInput.addEventListener('input', updateTotal);
updateTotal();
</script>
@endsection
