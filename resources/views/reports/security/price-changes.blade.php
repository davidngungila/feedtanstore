@extends('layouts.app')

@section('page-title', 'Price Changes')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class='text-xl font-bold text-primary-900'>Price Change History</h2>
            <form method="GET" action="{{ route('reports.security.price-changes') }}" class="flex flex-wrap items-center gap-2">
                <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}" class="form-input input-field px-3 py-2">
                <span class="text-gray-500">to</span>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}" class="form-input input-field px-3 py-2">
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">Filter</button>
                <a href="{{ route('reports.security.price-changes.download', ['start_date' => request('start_date', $startDate), 'end_date' => request('end_date', $endDate)]) }}" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">Export PDF</a>
            </form>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-xl p-5">
                <p class="text-sm text-primary-700 mb-1">Total Requests</p>
                <h3 class="text-2xl font-bold text-primary-900">{{ $changes->count() }}</h3>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5">
                <p class="text-sm text-green-700 mb-1">Approved</p>
                <h3 class="text-2xl font-bold text-green-900">{{ $changes->where('status', 'approved')->count() }}</h3>
            </div>
            <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-5">
                <p class="text-sm text-red-700 mb-1">Rejected</p>
                <h3 class="text-2xl font-bold text-red-900">{{ $changes->where('status', 'rejected')->count() }}</h3>
            </div>
            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-5">
                <p class="text-sm text-yellow-700 mb-1">Pending</p>
                <h3 class="text-2xl font-bold text-yellow-900">{{ $changes->where('status', 'pending')->count() }}</h3>
            </div>
        </div>

        @if($changes->isEmpty())
        <div class="text-center py-10 text-gray-500">
            <i class="fas fa-money-bill-transfer text-4xl mb-3"></i>
            <p>No price changes recorded in this period.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Date</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Product</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Old Price</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">New Price</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Change %</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Requested By</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Reviewed By</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($changes as $change)
                    @php
                        $changePct = $change->current_price > 0
                            ? round((($change->proposed_price - $change->current_price) / $change->current_price) * 100, 1)
                            : 0;
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">{{ $change->created_at->format('M d, Y') }}</td>
                        <td class="px-4 py-3 font-medium">{{ $change->product->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3 text-right">TZS {{ number_format($change->current_price, 2) }}</td>
                        <td class="px-4 py-3 text-right font-semibold">TZS {{ number_format($change->proposed_price, 2) }}</td>
                        <td class="px-4 py-3 text-right {{ $changePct >= 0 ? 'text-red-600' : 'text-green-600' }} font-semibold">
                            {{ $changePct >= 0 ? '+' : '' }}{{ $changePct }}%
                        </td>
                        <td class="px-4 py-3">{{ $change->requester->name ?? '-' }}</td>
                        <td class="px-4 py-3">{{ $change->reviewer->name ?? '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $change->status === 'approved' ? 'bg-green-100 text-green-700' : ($change->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                {{ ucfirst($change->status) }}
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
