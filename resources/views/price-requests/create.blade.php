@extends('layouts.app')

@section('page-title', 'Request Price Change')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <a href="{{ route('price-requests.index') }}" class="text-primary-600 hover:text-primary-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Price Requests
        </a>
    </div>

    <div class="max-w-5xl mx-auto">
        <div class="card rounded-2xl p-6 mb-6">
            <h1 class="text-2xl font-bold text-primary-900 mb-1">Request Price Change</h1>
            <p class="text-gray-600">Propose a new selling price for a product. An administrator will review and approve it.</p>

            @if($errors->any())
                <div class="mt-4 p-3 bg-red-100 border border-red-400 text-red-800 rounded-lg">
                    <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            {{-- Form --}}
            <div class="lg:col-span-2 card rounded-2xl p-6 h-fit">
                <form action="{{ route('price-requests.store') }}" method="POST" id="requestForm">
                    @csrf
                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                        <select name="product_id" id="product_select" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" {{ old('product_id') == $product->id ? 'selected' : '' }}>{{ $product->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-2 gap-4 mb-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Current Active</label>
                            <div id="current_price_display" class="px-3 py-2 bg-gray-100 border border-gray-200 rounded-lg font-semibold text-gray-700">—</div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Proposed (TZS) *</label>
                            <input type="number" name="proposed_price" id="proposed_price" step="0.01" min="0" value="{{ old('proposed_price') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>

                    <div class="mb-5">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Change</label>
                        <div id="change_display" class="px-4 py-2 bg-primary-50 border border-primary-200 rounded-lg font-semibold text-primary-900">—</div>
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

            {{-- Product details + price history --}}
            <div class="lg:col-span-3 space-y-6">
                <div class="card rounded-2xl p-6">
                    <h3 class="font-semibold text-primary-900 mb-4"><i class="fas fa-circle-info mr-2 text-primary-500"></i>Product Details</h3>
                    <div id="product_details" class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
                        <p class="col-span-full text-gray-400 text-center py-6">Select a product to see its full details and price list.</p>
                    </div>
                </div>

                <div class="card rounded-2xl p-6">
                    <h3 class="font-semibold text-primary-900 mb-1"><i class="fas fa-list mr-2 text-primary-500"></i>Price List</h3>
                    <p class="text-xs text-gray-500 mb-4">All recorded prices for this product — only one is active at a time.</p>
                    <div id="price_list" class="overflow-x-auto">
                        <p class="text-gray-400 text-center text-sm py-6">No product selected.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
@php
    $productsJson = $products->map(function ($p) {
        return [
            'id' => $p->id,
            'name' => $p->name,
            'sku' => $p->sku,
            'barcode' => $p->barcode,
            'category' => $p->category?->name,
            'stock' => $p->quantity,
            'unit' => $p->unit?->short_name ?? 'pcs',
            'cost_price' => $p->cost_price,
            'selling_price' => $p->selling_price,
            'prices' => $p->prices->map(function ($pr) {
                return [
                    'price' => $pr->price,
                    'label' => $pr->label,
                    'is_active' => (bool) $pr->is_active,
                    'activated_at' => $pr->activated_at ? $pr->activated_at->format('M d, Y H:i') : null,
                    'creator' => $pr->creator?->name,
                ];
            })->values(),
        ];
    })->values();
@endphp
const products = @json($productsJson);

const fmt = n => Number(n).toFixed(2);

function renderDetails(p) {
    document.getElementById('current_price_display').textContent = p ? fmt(p.selling_price) : '—';
    const d = document.getElementById('product_details');
    if (!p) {
        d.innerHTML = '<p class="col-span-full text-gray-400 text-center py-6">Select a product to see its full details and price list.</p>';
        return;
    }
    const margin = p.cost_price > 0 ? (((p.selling_price - p.cost_price) / p.cost_price) * 100).toFixed(1) : null;
    const profit = (p.selling_price - p.cost_price).toFixed(2);
    d.innerHTML = `
        <div><p class="text-xs text-gray-500 uppercase">Category</p><p class="font-semibold">${p.category ?? '—'}</p></div>
        <div><p class="text-xs text-gray-500 uppercase">Stock</p><p class="font-semibold">${p.stock} ${p.unit}</p></div>
        <div><p class="text-xs text-gray-500 uppercase">Cost Price</p><p class="font-semibold">TZS ${fmt(p.cost_price)}</p></div>
        <div><p class="text-xs text-gray-500 uppercase">SKU</p><p class="font-semibold">${p.sku ?? '—'}</p></div>
        <div><p class="text-xs text-gray-500 uppercase">Barcode</p><p class="font-semibold">${p.barcode ?? '—'}</p></div>
        <div><p class="text-xs text-gray-500 uppercase">Profit / Margin at current</p><p class="font-semibold">TZS ${profit}${margin !== null ? ' (' + margin + '%)' : ''}</p></div>
    `;
}

function renderPriceList(p) {
    const el = document.getElementById('price_list');
    if (!p || p.prices.length === 0) {
        el.innerHTML = '<p class="text-gray-400 text-center text-sm py-6">No prices recorded yet.</p>';
        return;
    }
    let rows = '';
    p.prices.forEach(function (pr) {
        rows += `
        <tr class="${pr.is_active ? 'bg-green-50/60' : ''} border-b border-gray-100">
            <td class="py-2 pr-4 font-bold ${pr.is_active ? 'text-green-700' : 'text-gray-800'}">TZS ${fmt(pr.price)}</td>
            <td class="py-2 pr-4 text-gray-600">${pr.label ?? '—'}</td>
            <td class="py-2 pr-4"><span class="px-2 py-0.5 text-xs font-semibold rounded-full ${pr.is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500'}">${pr.is_active ? 'Active' : 'Inactive'}</span></td>
            <td class="py-2 pr-4 text-xs text-gray-500">${pr.activated_at ?? '—'}</td>
            <td class="py-2 text-xs text-gray-500">${pr.creator ?? '—'}</td>
        </tr>`;
    });
    el.innerHTML = `
        <table class="w-full text-sm">
            <thead><tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-200">
                <th class="py-2 pr-4">Price</th><th class="py-2 pr-4">Label</th><th class="py-2 pr-4">Status</th><th class="py-2 pr-4">Activated</th><th class="py-2">Added By</th>
            </tr></thead>
            <tbody>${rows}</tbody>
        </table>`;
}

const productSelect = document.getElementById('product_select');
const proposedInput = document.getElementById('proposed_price');
const changeDisplay = document.getElementById('change_display');

function updatePreview() {
    const p = products.find(x => x.id == productSelect.value);
    renderDetails(p);
    renderPriceList(p);

    if (p && proposedInput.value) {
        const diff = parseFloat(proposedInput.value) - parseFloat(p.selling_price);
        const pct = p.selling_price > 0 ? ((diff / p.selling_price) * 100).toFixed(1) : '0.0';
        changeDisplay.textContent = (diff >= 0 ? '+' : '') + diff.toFixed(2) + ' TZS (' + (diff >= 0 ? '+' : '') + pct + '%)';
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
