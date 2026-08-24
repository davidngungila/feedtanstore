@extends('layouts.app')

@section('page-title', 'Product Price Requests')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <div>
            @if($isAdmin)
                <h1 class="text-2xl font-bold text-primary-900">Product Price Requests</h1>
                <p class="text-gray-600">Review price change requests from marketing officers, or manage all product prices directly</p>
            @else
                <h1 class="text-2xl font-bold text-primary-900">Product Price Requests</h1>
                <p class="text-gray-600">Request a new price for a product — an administrator will review it</p>
            @endif
        </div>
        <div class="flex gap-3">
            @if($isAdmin)
                <a href="{{ route('price-requests.prices') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-list mr-2"></i>Manage All Prices
                </a>
            @else
                @if(Auth::user()->role === 'marketing_officer')
                    <a href="{{ route('price-requests.prices') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-list mr-2"></i>Price Management
                    </a>
                @endif
                <a href="{{ route('price-requests.create') }}" class="bg-primary-100 hover:bg-primary-200 text-primary-800 px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-tag mr-2"></i>Request Price Change
                </a>
            @endif
        </div>
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

    {{-- Stats --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="card rounded-2xl p-6 flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm mb-1">Pending</p>
                <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
            </div>
            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center"><i class="fas fa-clock text-yellow-600 text-xl"></i></div>
        </div>
        <div class="card rounded-2xl p-6 flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm mb-1">Approved</p>
                <p class="text-3xl font-bold text-green-600">{{ $stats['approved'] }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center"><i class="fas fa-check text-green-600 text-xl"></i></div>
        </div>
        <div class="card rounded-2xl p-6 flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm mb-1">Rejected</p>
                <p class="text-3xl font-bold text-red-600">{{ $stats['rejected'] }}</p>
            </div>
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center"><i class="fas fa-times text-red-600 text-xl"></i></div>
        </div>
    </div>

    {{-- Admin: set price directly --}}
    @if($isAdmin)
    <div class="card rounded-2xl p-6 mb-6">
        <h3 class="text-lg font-semibold text-primary-900 mb-1">Set Product Price Directly</h3>
        <p class="text-sm text-gray-500 mb-4">As admin you can assign a new price immediately — no approval needed.</p>
        <form action="{{ route('price-requests.set-price') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end" id="setPriceForm">
            @csrf
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                <select name="product_id" id="set_price_product" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">Select Product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}">{{ $product->name }} (current: {{ number_format($product->selling_price, 2) }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">New Price (TZS) *</label>
                <input type="number" name="new_price" step="0.01" min="0" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <button type="button" onclick="openSetPriceModal()" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-bolt mr-2"></i>Set Price
            </button>
        </form>
    </div>
    @endif

    {{-- Admin: pending price approvals from marketing officers --}}
    @if($isAdmin && $pendingPrices->count())
    <div class="card rounded-2xl p-6 mb-6 border-l-4 border-yellow-400">
        <h3 class="text-lg font-semibold text-primary-900 mb-1"><i class="fas fa-clock text-yellow-500 mr-2"></i>Pending Price Approvals</h3>
        <p class="text-sm text-gray-500 mb-4">New prices submitted by marketing officers — approve to make them the active selling price.</p>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Product</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">New Price</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Current</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Label</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Submitted By</th>
                        <th class="px-4 py-2.5 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="px-4 py-2.5 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($pendingPrices as $pendingPrice)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-900">{{ $pendingPrice->product->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 font-bold text-green-700">TZS {{ number_format((float) $pendingPrice->price, 2) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">TZS {{ number_format((float) ($pendingPrice->product->selling_price ?? 0), 2) }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $pendingPrice->label ?? '—' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-600">{{ $pendingPrice->creator->name ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs text-gray-500">{{ $pendingPrice->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-4 py-3 text-right whitespace-nowrap">
                            <button type="button" data-action="{{ route('price-requests.prices.activate', $pendingPrice) }}" data-product="{{ $pendingPrice->product->name }}" data-price="{{ number_format((float) $pendingPrice->price, 2) }}" onclick="openApprovePriceModal(this)" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium">
                                <i class="fas fa-check mr-1"></i>Approve
                            </button>
                            <form action="{{ route('price-requests.prices.destroy', $pendingPrice) }}" method="POST" class="inline" onsubmit="return confirm('Reject and remove this price entry?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="bg-red-100 hover:bg-red-200 text-red-700 px-3 py-1.5 rounded-lg text-xs font-medium ml-1">Reject</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- Requests table --}}
    <div class="card rounded-2xl overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="font-semibold text-primary-900">{{ $isAdmin ? 'Price Change Requests' : 'My Price Requests' }}</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Current Price</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Proposed Price</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Reason</th>
                        @if($isAdmin)<th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Requested By</th>@endif
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        @if($isAdmin)<th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>@endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse($requests as $request)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-900">{{ $request->product->name ?? 'N/A' }}</p>
                                <p class="text-xs text-gray-500">{{ $request->created_at->format('M d, Y H:i') }}</p>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ number_format($request->current_price, 2) }}</td>
                            <td class="px-6 py-4 text-sm font-bold {{ $request->proposed_price >= $request->current_price ? 'text-green-700' : 'text-orange-700' }}">
                                {{ number_format($request->proposed_price, 2) }}
                                @if($request->proposed_price >= $request->current_price)
                                    <i class="fas fa-arrow-up text-xs ml-1"></i>
                                @else
                                    <i class="fas fa-arrow-down text-xs ml-1"></i>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600 max-w-xs">
                                {{ \Illuminate\Support\Str::limit($request->reason, 80) }}
                                @if($request->status === 'rejected' && $request->rejection_reason)
                                    <p class="mt-1 text-xs text-red-600"><strong>Rejected:</strong> {{ $request->rejection_reason }}</p>
                                @endif
                            </td>
                            @if($isAdmin)
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $request->requester->name ?? '—' }}</td>
                            @endif
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 text-xs font-semibold rounded-full
                                    {{ $request->status === 'approved' ? 'bg-green-100 text-green-700' : ($request->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                    {{ ucfirst($request->status) }}
                                </span>
                                @if($request->reviewed_at)
                                    <p class="text-xs text-gray-400 mt-1">by {{ $request->reviewer->name ?? '—' }}</p>
                                @endif
                            </td>
                            @if($isAdmin)
                            <td class="px-6 py-4">
                                @if($request->status === 'pending')
                                    <div class="flex items-center gap-2">
                                        <form action="{{ route('price-requests.approve', $request) }}" method="POST">
                                            @csrf
                                            @method('PUT')
                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium" onclick="return confirm('Approve and apply the new price to this product?')">
                                                <i class="fas fa-check mr-1"></i>Approve &amp; Apply
                                            </button>
                                        </form>
                                        <details class="relative">
                                            <summary class="bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded-lg text-xs font-medium cursor-pointer list-none">Reject</summary>
                                            <form action="{{ route('price-requests.reject', $request) }}" method="POST" class="absolute right-0 z-10 mt-2 w-64 p-3 bg-white border border-gray-200 rounded-lg shadow-lg">
                                                @csrf
                                                @method('PUT')
                                                <label class="block text-xs font-medium text-gray-700 mb-1">Rejection reason *</label>
                                                <textarea name="rejection_reason" rows="2" required class="w-full px-2 py-1 border border-gray-300 rounded text-xs"></textarea>
                                                <button type="submit" class="mt-2 w-full bg-red-600 hover:bg-red-700 text-white py-1.5 rounded text-xs font-medium">Confirm Rejection</button>
                                            </form>
                                        </details>
                                    </div>
                                @else
                                    <span class="text-xs text-gray-400">Reviewed</span>
                                @endif
                            </td>
                            @endif
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ $isAdmin ? 7 : 5 }}" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-3 block text-gray-300"></i>
                                No price requests yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $requests->links() }}
        </div>
    </div>
</div>

@if($isAdmin)
{{-- Set price confirmation modal --}}
<div id="setPriceModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-bolt text-primary-600"></i>
                </div>
                <h3 class="text-lg font-bold text-primary-900">Update this product price immediately?</h3>
            </div>
            <p class="text-gray-700 mb-2">Product: <span id="setPriceProductName" class="font-bold text-primary-900"></span></p>
            <p class="text-sm text-gray-600 mb-1">Current: <span id="setPriceCurrent" class="font-semibold"></span> &rarr; New: <span id="setPriceNew" class="font-bold text-green-700"></span></p>
            <p class="text-xs text-gray-500 mb-5">Sales will switch to the new price immediately — no approval needed.</p>
            <div class="flex justify-end gap-3">
                <button type="button" id="setPriceCancel" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="button" onclick="document.getElementById('setPriceForm').submit()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">Yes, Update Price</button>
            </div>
        </div>
    </div>
</div>

<script>
const setPriceModal = document.getElementById('setPriceModal');
const approvePriceModal = document.getElementById('approvePriceModal');

function openSetPriceModal() {
    const select = document.getElementById('set_price_product');
    const newPriceInput = document.querySelector('#setPriceForm input[name="new_price"]');
    if (!select.value || !newPriceInput.value) {
        select.required && !select.value ? select.reportValidity() : newPriceInput.reportValidity();
        return;
    }
    const current = select.options[select.selectedIndex].dataset.price;
    document.getElementById('setPriceProductName').textContent = select.options[select.selectedIndex].textContent.split('(current')[0].trim();
    document.getElementById('setPriceCurrent').textContent = 'TZS ' + Number(current).toFixed(2);
    document.getElementById('setPriceNew').textContent = 'TZS ' + Number(newPriceInput.value).toFixed(2);
    setPriceModal.classList.remove('hidden');
}
document.getElementById('setPriceCancel').addEventListener('click', function () { setPriceModal.classList.add('hidden'); });
setPriceModal.addEventListener('click', function (e) { if (e.target === setPriceModal) setPriceModal.classList.add('hidden'); });
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') {
        if (!setPriceModal.classList.contains('hidden')) setPriceModal.classList.add('hidden');
        if (approvePriceModal && !approvePriceModal.classList.contains('hidden')) approvePriceModal.classList.add('hidden');
    }
});

function openApprovePriceModal(btn) {
    document.getElementById('approvePriceForm').action = btn.dataset.action;
    document.getElementById('approvePriceProductName').textContent = btn.dataset.product;
    document.getElementById('approvePriceNew').textContent = 'TZS ' + btn.dataset.price;
    approvePriceModal.classList.remove('hidden');
}
document.getElementById('approvePriceCancel').addEventListener('click', function () { approvePriceModal.classList.add('hidden'); });
if (approvePriceModal) {
    approvePriceModal.addEventListener('click', function (e) { if (e.target === approvePriceModal) approvePriceModal.classList.add('hidden'); });
}
</script>

{{-- Approve pending price confirmation modal --}}
<div id="approvePriceModal" class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50 p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">
        <div class="p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-check text-green-600"></i>
                </div>
                <h3 class="text-lg font-bold text-primary-900">Approve New Price</h3>
            </div>
            <p class="text-gray-700 mb-2">Approve the new price for <span id="approvePriceProductName" class="font-bold text-primary-900"></span>?</p>
            <p class="text-sm text-gray-600 mb-1">New selling price: <span id="approvePriceNew" class="font-bold text-green-700"></span></p>
            <p class="text-xs text-gray-500 mb-5">Sales will switch to this price immediately.</p>
            <form id="approvePriceForm" action="" method="POST" class="flex justify-end gap-3">
                @csrf
                @method('PUT')
                <button type="button" id="approvePriceCancel" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">Yes, Approve &amp; Activate</button>
            </form>
        </div>
    </div>
</div>
@endif
@endsection
