@extends('layouts.app')

@section('page-title', 'Stock Out')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class='text-xl font-bold text-primary-900'>Stock Out Report</h2>
            <form method="GET" action="{{ route('reports.inventory.stock-out') }}" class="flex flex-wrap items-center gap-2">
                <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}" class="form-input input-field px-3 py-2">
                <span class="text-gray-500">to</span>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}" class="form-input input-field px-3 py-2">
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">Filter</button>
                <a href="{{ route('reports.inventory.stock-out.download', ['start_date' => request('start_date', $startDate), 'end_date' => request('end_date', $endDate)]) }}" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">Export PDF</a>
            </form>
        </div>

        <!-- Stock Adjustments (subtractions) -->
        <h3 class="font-semibold text-primary-900 mb-3">Adjustments (Reductions)</h3>
        @if($adjustments->isEmpty())
        <p class="text-gray-500 text-sm mb-6">No stock reductions in this period.</p>
        @else
        <div class="overflow-x-auto mb-8">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Date</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Product</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Qty Reduced</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Reason</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($adjustments as $adj)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $adj->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-4 py-3 font-medium">{{ $adj->product->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-right text-red-700 font-semibold">-{{ number_format($adj->quantity_change) }}</td>
                        <td class="px-4 py-3">{{ $adj->reason ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        <!-- Customer Returns -->
        <h3 class="font-semibold text-primary-900 mb-3">Customer Returns</h3>
        @if($returns->isEmpty())
        <p class="text-gray-500 text-sm">No returns recorded in this period.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Date</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Return #</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Items</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Total Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($returns as $return)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $return->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3 font-medium">{{ $return->return_number ?? '#' . $return->id }}</td>
                        <td class="px-4 py-3">
                            @foreach($return->items as $item)
                                {{ $item->saleItem?->product?->name ?? 'Item' }} ({{ number_format($item->quantity) }})@if(!$loop->last), @endif
                            @endforeach
                        </td>
                        <td class="px-4 py-3 text-right font-semibold">TZS {{ number_format($return->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
