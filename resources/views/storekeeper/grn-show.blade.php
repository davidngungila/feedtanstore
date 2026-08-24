@extends('layouts.app')

@section('page-title', 'GRN Details')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <a href="{{ route('storekeeper.grn') }}" class="text-primary-600 hover:text-primary-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to GRNs
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
            {{ session('success') }}
        </div>
    @endif

    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-primary-900">{{ $grn->grn_number }}</h1>
                <p class="text-gray-600 mt-1">Received {{ \Carbon\Carbon::parse($grn->received_date)->format('M d, Y H:i') }}</p>
            </div>
            <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-700">
                {{ ucfirst($grn->status) }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <p class="text-sm text-gray-600">Supplier</p>
                <p class="font-semibold">{{ $grn->supplier->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Purchase Order</p>
                @if($grn->purchaseOrder)
                    <p class="font-mono font-semibold">{{ $grn->purchaseOrder->po_number }}</p>
                @else
                    <p class="text-gray-400 italic">Direct receipt</p>
                @endif
            </div>
            <div>
                <p class="text-sm text-gray-600">Total Value</p>
                <p class="font-semibold">{{ number_format($grn->total, 2) }}</p>
            </div>
        </div>

        @if($grn->notes)
        <div class="mb-6">
            <p class="text-sm text-gray-600">Notes</p>
            <p class="text-gray-900">{{ $grn->notes }}</p>
        </div>
        @endif

        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Received Items</h3>
        <div class="overflow-x-auto rounded-xl border border-gray-200">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Product</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Quantity</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Unit Price</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Total</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Batch Number</th>
                        <th class="px-4 py-3 text-left font-semibold text-gray-600">Expiry Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach($grn->items as $item)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $item->product->name ?? 'N/A' }}</td>
                            <td class="px-4 py-3">{{ $item->quantity }} {{ $item->product->unit->short_name ?? 'pcs' }}</td>
                            <td class="px-4 py-3">{{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-4 py-3 font-medium">{{ number_format($item->total, 2) }}</td>
                            <td class="px-4 py-3">{{ $item->product->batch_number ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $item->expiry_date ? \Carbon\Carbon::parse($item->expiry_date)->format('M d, Y') : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
