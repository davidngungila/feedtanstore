@extends('layouts.app')

@section('page-title', 'Inventory Adjustments')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class='text-xl font-bold text-primary-900'>Inventory Adjustments</h2>
            <form method="GET" action="{{ route('reports.security.inventory-adjustments') }}" class="flex flex-wrap items-center gap-2">
                <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}" class="form-input input-field px-3 py-2">
                <span class="text-gray-500">to</span>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}" class="form-input input-field px-3 py-2">
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">Filter</button>
                <a href="{{ route('reports.security.inventory-adjustments.download', ['start_date' => request('start_date', $startDate), 'end_date' => request('end_date', $endDate)]) }}" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">Export PDF</a>
            </form>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-xl p-5">
                <p class="text-sm text-primary-700 mb-1">Total Adjustments</p>
                <h3 class="text-2xl font-bold text-primary-900">{{ $adjustments->count() }}</h3>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5">
                <p class="text-sm text-green-700 mb-1">Additions</p>
                <h3 class="text-2xl font-bold text-green-900">{{ number_format($adjustments->where('type', 'addition')->sum('quantity_change')) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-5">
                <p class="text-sm text-red-700 mb-1">Subtractions</p>
                <h3 class="text-2xl font-bold text-red-900">{{ number_format($adjustments->where('type', 'subtraction')->sum('quantity_change')) }}</h3>
            </div>
        </div>

        @if($adjustments->isEmpty())
        <div class="text-center py-10 text-gray-500">
            <i class="fas fa-sliders text-4xl mb-3"></i>
            <p>No stock adjustments recorded in this period.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Date</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Product</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Type</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Qty Change</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Reason</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($adjustments as $adjustment)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">{{ $adjustment->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-4 py-3 font-medium">{{ $adjustment->product->name ?? 'N/A' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $adjustment->type === 'addition' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                {{ ucfirst($adjustment->type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold {{ $adjustment->type === 'addition' ? 'text-green-700' : 'text-red-700' }}">
                            {{ $adjustment->type === 'addition' ? '+' : '-' }}{{ number_format($adjustment->quantity_change) }}
                        </td>
                        <td class="px-4 py-3 text-gray-600">{{ $adjustment->reason ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
