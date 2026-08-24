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
                <a href="{{ route('price-requests.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
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
            <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded-lg font-medium transition-colors" onclick="return confirm('Update this product price immediately?')">
                <i class="fas fa-bolt mr-2"></i>Set Price
            </button>
        </form>
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
@endsection
