@extends('layouts.app')

@section('page-title', 'Price Management')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-primary-900">Price Management</h1>
            <p class="text-gray-600">Manage prices for all products used in sales — a product can hold multiple prices, but only one is active at a time.</p>
        </div>
        <form method="GET" action="{{ route('price-requests.prices') }}" class="flex gap-2">
            <input type="text" name="search" value="{{ $search }}" placeholder="Search name, SKU or barcode..." class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg"><i class="fas fa-search"></i></button>
        </form>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-800 rounded-lg">{{ session('error') }}</div>
    @endif
    @if($errors->any())
        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-800 rounded-lg">
            <ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    @forelse($products as $product)
    <div class="card rounded-2xl p-5 mb-4">
        <div class="flex flex-wrap items-start justify-between gap-3 mb-4 pb-3 border-b border-gray-100">
            <div>
                <h3 class="font-bold text-primary-900">{{ $product->name }}</h3>
                <p class="text-xs text-gray-500 mt-0.5">
                    @if($product->sku)SKU: {{ $product->sku }} · @endif
                    @if($product->barcode)Barcode: {{ $product->barcode }} · @endif
                    Stock: {{ $product->quantity ?? 0 }} {{ $product->unit->short_name ?? 'pcs' }}
                </p>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-xs text-gray-500 uppercase tracking-wide">Selling in POS at</p>
                    <p class="text-xl font-bold text-primary-900">TZS {{ number_format((float) $product->selling_price, 2) }}</p>
                </div>
                <button type="button" onclick="toggleAddPrice({{ $product->id }})" id="add-price-btn-{{ $product->id }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition-colors whitespace-nowrap">
                    <i class="fas fa-plus mr-2"></i>Add New Price
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Price list --}}
            <div class="lg:col-span-2 overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-xs font-semibold text-gray-500 uppercase tracking-wider border-b border-gray-200">
                            <th class="py-2 pr-4">Price</th>
                            <th class="py-2 pr-4">Label</th>
                            <th class="py-2 pr-4">Status</th>
                            <th class="py-2 pr-4">Activated</th>
                            <th class="py-2 pr-4">Added By</th>
                            <th class="py-2 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($product->prices as $price)
                        <tr class="{{ $price->is_active ? 'bg-green-50/60' : '' }}">
                            <td class="py-2.5 pr-4 font-bold {{ $price->is_active ? 'text-green-700' : 'text-gray-800' }}">
                                TZS {{ number_format((float) $price->price, 2) }}
                            </td>
                            <td class="py-2.5 pr-4 text-gray-600">{{ $price->label ?? '—' }}</td>
                            <td class="py-2.5 pr-4">
                                <span class="px-2 py-0.5 text-xs font-semibold rounded-full {{ $price->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                    {{ $price->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="py-2.5 pr-4 text-xs text-gray-500">{{ $price->activated_at ? $price->activated_at->format('M d, Y H:i') : '—' }}</td>
                            <td class="py-2.5 pr-4 text-xs text-gray-500">{{ $price->creator->name ?? '—' }}</td>
                            <td class="py-2.5 text-right whitespace-nowrap">
                                @if($price->is_active)
                                    <form action="{{ route('price-requests.prices.deactivate', $price) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <button type="submit" class="text-xs px-3 py-1.5 rounded-lg bg-yellow-100 hover:bg-yellow-200 text-yellow-800 font-medium">Deactivate</button>
                                    </form>
                                @else
                                    <button type="button" data-action="{{ route('price-requests.prices.activate', $price) }}" data-product="{{ $product->name }}" data-price="{{ number_format((float) $price->price, 2) }}" onclick="openActivateModal(this)" class="text-xs px-3 py-1.5 rounded-lg bg-green-600 hover:bg-green-700 text-white font-medium">Activate</button>
                                    <form action="{{ route('price-requests.prices.destroy', $price) }}" method="POST" class="inline" onsubmit="return confirm('Delete this price entry?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-xs px-3 py-1.5 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 font-medium ml-1">Delete</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="py-4 text-center text-gray-400 text-sm">No prices recorded yet — add the first price on the right.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Add price (hidden until "Add New Price" is clicked) --}}
            <div id="add-price-{{ $product->id }}" class="hidden bg-gray-50 rounded-xl p-4">
                <div class="flex items-center justify-between mb-3">
                    <h4 class="font-semibold text-primary-900 text-sm uppercase tracking-wide">Add New Price</h4>
                    <button type="button" onclick="toggleAddPrice({{ $product->id }})" class="text-gray-400 hover:text-gray-600 transition-colors">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <form action="{{ route('price-requests.prices.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <input type="hidden" name="product_id" value="{{ $product->id }}">
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Price (TZS) *</label>
                        <input type="number" name="price" step="0.01" min="0" required class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Label</label>
                        <input type="text" name="label" maxlength="100" placeholder="e.g., Retail, Wholesale, Promo" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">Notes</label>
                        <input type="text" name="notes" maxlength="500" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <label class="flex items-center gap-2 text-sm text-gray-700">
                        <input type="checkbox" name="activate_now" value="1" checked class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        Activate immediately
                    </label>
                    <button type="submit" class="w-full py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium">Add Price</button>
                    <p class="text-xs text-gray-400">Activating a price switches sales to it instantly and deactivates the others.</p>
                </form>
            </div>
        </div>
    </div>
    @empty
    <div class="card rounded-2xl p-12 text-center text-gray-500">
        <i class="fas fa-tags text-4xl mb-3 block text-gray-300"></i>
        No products found.
    </div>
    @endforelse

    <div class="mt-4">
        {{ $products->links() }}
    </div>
</div>

<script>
function toggleAddPrice(productId) {
    const panel = document.getElementById('add-price-' + productId);
    const btn = document.getElementById('add-price-btn-' + productId);
    panel.classList.toggle('hidden');
    btn.innerHTML = panel.classList.contains('hidden')
        ? '<i class="fas fa-plus mr-2"></i>Add New Price'
        : '<i class="fas fa-minus mr-2"></i>Close';
    if (!panel.classList.contains('hidden')) {
        panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        panel.querySelector('input[name="price"]').focus();
    }
}

const activateModal = document.getElementById('activateModal');
const activateForm = document.getElementById('activateForm');

function openActivateModal(btn) {
    activateForm.action = btn.dataset.action;
    document.getElementById('activateProductName').textContent = btn.dataset.product;
    document.getElementById('activateNewPrice').textContent = 'TZS ' + btn.dataset.price;
    activateModal.classList.remove('hidden');
}
function closeActivateModal() {
    activateModal.classList.add('hidden');
    activateForm.action = '';
}
document.getElementById('activateCancel').addEventListener('click', closeActivateModal);
activateModal.addEventListener('click', function (e) { if (e.target === activateModal) closeActivateModal(); });
document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && !activateModal.classList.contains('hidden')) closeActivateModal(); });
</script>

{{-- Activate price confirmation modal --}}
<div id="activateModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-tag text-green-600"></i>
                </div>
                <h3 class="text-lg font-bold text-primary-900">Update Selling Price</h3>
            </div>
            <p class="text-gray-700 mb-2">Are you sure you want to update the price in <span id="activateProductName" class="font-bold text-primary-900"></span>?</p>
            <p class="text-sm text-gray-600 mb-1">New selling price: <span id="activateNewPrice" class="font-bold text-green-700"></span></p>
            <p class="text-xs text-gray-500 mb-5">Sales will switch to this price immediately and all other prices for this product become inactive.</p>
            <form id="activateForm" action="" method="POST" class="flex justify-end gap-3">
                @csrf
                @method('PUT')
                <button type="button" id="activateCancel" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">Yes, Update Price</button>
            </form>
        </div>
    </div>
</div>
@endsection
