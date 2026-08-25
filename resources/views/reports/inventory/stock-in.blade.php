@extends('layouts.app')

@section('page-title', 'Stock In')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class='text-xl font-bold text-primary-900'>Stock In Report</h2>
            <form method="GET" action="{{ route('reports.inventory.stock-in') }}" class="flex flex-wrap items-center gap-2">
                <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}" class="form-input input-field px-3 py-2">
                <span class="text-gray-500">to</span>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}" class="form-input input-field px-3 py-2">
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">Filter</button>
                <a href="{{ route('reports.inventory.stock-in.download', ['start_date' => request('start_date', $startDate), 'end_date' => request('end_date', $endDate)]) }}" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">Export PDF</a>
            </form>
        </div>

        @if($grns->isEmpty())
        <div class="text-center py-10 text-gray-500">
            <i class="fas fa-truck-ramp-box text-4xl mb-3"></i>
            <p>No goods received in this period.</p>
        </div>
        @else
        <div class="space-y-4">
            @foreach($grns as $grn)
            <div class="border border-gray-100 rounded-xl overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 flex flex-wrap items-center justify-between gap-2">
                    <div>
                        <span class="font-bold text-primary-900">{{ $grn->grn_number ?? 'GRN #' . $grn->id }}</span>
                        <span class="text-gray-500 text-sm ml-2">{{ $grn->supplier->name ?? 'N/A' }}</span>
                    </div>
                    <span class="text-sm text-gray-600">{{ $grn->created_at->format('M d, Y H:i') }}</span>
                </div>
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="px-4 py-2 text-left font-medium text-gray-500">Product</th>
                            <th class="px-4 py-2 text-right font-medium text-gray-500">Qty</th>
                            <th class="px-4 py-2 text-right font-medium text-gray-500">Unit Price</th>
                            <th class="px-4 py-2 text-right font-medium text-gray-500">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        @foreach($grn->items as $item)
                        <tr>
                            <td class="px-4 py-2">{{ $item->product->name ?? 'Product' }}</td>
                            <td class="px-4 py-2 text-right">{{ number_format($item->quantity) }}</td>
                            <td class="px-4 py-2 text-right">TZS {{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-4 py-2 text-right font-medium">TZS {{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
