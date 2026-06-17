@extends('layouts.app')

@section('page-title', 'Sales by Date')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class="text-xl font-bold text-primary-900">Sales by Date</h2>
            <div class="flex items-center gap-3">
                <form id="filter-form" class="flex items-center gap-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">From</label>
                        <input type="date" name="start_date" value="{{ $startDate }}" class="form-input input-field px-4 py-2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">To</label>
                        <input type="date" name="end_date" value="{{ $endDate }}" class="form-input input-field px-4 py-2">
                    </div>
                    <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors mt-6">
                        Filter
                    </button>
                </form>
                <a href="{{ route('reports.sales.by-date.download', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors mt-6">
                    Export PDF
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-primary-200 flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-primary-700"></i>
                    </div>
                </div>
                <p class="text-sm text-primary-700 mb-1">Total Sales</p>
                <h3 class="text-2xl font-bold text-primary-900">TZS {{ number_format($sales->sum('total'), 2) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-200 flex items-center justify-center">
                        <i class="fas fa-receipt text-blue-700"></i>
                    </div>
                </div>
                <p class="text-sm text-blue-700 mb-1">Total Transactions</p>
                <h3 class="text-2xl font-bold text-blue-900">{{ $sales->sum('count') }}</h3>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-green-200 flex items-center justify-center">
                        <i class="fas fa-calendar text-green-700"></i>
                    </div>
                </div>
                <p class="text-sm text-green-700 mb-1">Days Active</p>
                <h3 class="text-2xl font-bold text-green-900">{{ $sales->count() }}</h3>
            </div>
        </div>

        <!-- Sales Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Date</th>
                        <th class="px-4 py-3 text-center text-gray-700 font-medium">Transactions</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Total Sales</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($sales as $sale)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $sale->date }}</td>
                        <td class="px-4 py-3 text-center">{{ $sale->count }}</td>
                        <td class="px-4 py-3 text-right font-semibold">TZS {{ number_format($sale->total, 2) }}</td>
                    </tr>
                    @endforeach
                    @if($sales->isEmpty())
                    <tr>
                        <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                            No sales data found for the selected period
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
