@extends('layouts.app')

@section('page-title', 'Request Price Change')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <a href="{{ route('price-requests.index') }}" class="text-primary-600 hover:text-primary-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Price Requests
        </a>
    </div>

    <div class="card rounded-2xl p-6 max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold text-primary-900 mb-1">Request Price Change</h1>
        <p class="text-gray-600 mb-6">Propose a new selling price for a product. An administrator will review and approve it.</p>

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-800 rounded-lg">
                <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <form action="{{ route('price-requests.store') }}" method="POST">
            @csrf
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                <select name="product_id" id="product_select" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Select Product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Current Price</label>
                    <div id="current_price_display" class="px-4 py-2 bg-gray-100 border border-gray-200 rounded-lg font-semibold text-gray-700">—</div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Proposed New Price (TZS) *</label>
                    <input type="number" name="proposed_price" id="proposed_price" step="0.01" min="0" value="{{ old('proposed_price') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Change</label>
                    <div id="change_display" class="px-4 py-2 bg-primary-50 border border-primary-200 rounded-lg font-semibold text-primary-900">—</div>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Change *</label>
                <textarea name="reason" rows="4" required maxlength="1000" placeholder="e.g., Supplier prices increased, competitor pricing analysis, promotion ended..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('reason') }}</textarea>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('price-requests.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-paper-plane mr-2"></i>Submit Request
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const productSelect = document.getElementById('product_select');
const currentDisplay = document.getElementById('current_price_display');
const proposedInput = document.getElementById('proposed_price');
const changeDisplay = document.getElementById('change_display');

function updatePreview() {
    const option = productSelect.options[productSelect.selectedIndex];
    const current = parseFloat(option.dataset.price) || 0;
    currentDisplay.textContent = productSelect.value ? current.toFixed(2) : '—';

    const proposed = parseFloat(proposedInput.value) || 0;
    if (productSelect.value && proposedInput.value) {
        const diff = proposed - current;
        const pct = current > 0 ? ((diff / current) * 100).toFixed(1) : '0.0';
        changeDisplay.textContent = (diff >= 0 ? '+' : '') + diff.toFixed(2) + ' (' + (diff >= 0 ? '+' : '') + pct + '%)';
        changeDisplay.className = 'px-4 py-2 rounded-lg font-semibold ' +
            (diff >= 0 ? 'bg-green-50 border border-green-200 text-green-800' : 'bg-orange-50 border border-orange-200 text-orange-800');
    } else {
        changeDisplay.textContent = '—';
        changeDisplay.className = 'px-4 py-2 bg-primary-50 border border-primary-200 rounded-lg font-semibold text-primary-900';
    }
}
productSelect.addEventListener('change', updatePreview);
proposedInput.addEventListener('input', updatePreview);
updatePreview();
</script>
@endsection
