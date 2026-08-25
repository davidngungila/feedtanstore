@extends('layouts.app')

@section('page-title', 'Stock Transfers')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class='text-xl font-bold text-primary-900'>Stock Transfers</h2>
            <form method="GET" action="{{ route('reports.inventory.transfers') }}" class="flex flex-wrap items-center gap-2">
                <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}" class="form-input input-field px-3 py-2">
                <span class="text-gray-500">to</span>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}" class="form-input input-field px-3 py-2">
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">Filter</button>
                <a href="{{ route('reports.inventory.transfers.download', ['start_date' => request('start_date', $startDate), 'end_date' => request('end_date', $endDate)]) }}" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">Export PDF</a>
            </form>
        </div>

        @if($transfers->isEmpty())
        <div class="text-center py-10 text-gray-500">
            <i class="fas fa-truck-fast text-4xl mb-3"></i>
            <p>No transfers recorded in this period.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Date</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Transfer #</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Product</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">From</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">To</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Qty</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($transfers as $transfer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3">{{ $transfer->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3 font-medium">{{ $transfer->transfer_number ?? '#' . $transfer->id }}</td>
                        <td class="px-4 py-3">{{ $transfer->product->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $transfer->fromLocation->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $transfer->toLocation->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-right font-semibold">{{ number_format($transfer->quantity) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $transfer->status === 'completed' ? 'bg-green-100 text-green-700' : ($transfer->status === 'canceled' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ ucfirst($transfer->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
