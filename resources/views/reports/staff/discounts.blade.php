@extends('layouts.app')

@section('page-title', 'Discount Report')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class="text-xl font-bold text-primary-900">Discount Report</h2>
            <div class="flex items-center gap-3">
                <input type="date" name="start_date" value="{{ $startDate }}" class="form-input input-field px-4 py-2" id="start-date-filter">
                <input type="date" name="end_date" value="{{ $endDate }}" class="form-input input-field px-4 py-2" id="end-date-filter">
                <button onclick="filterReport()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">
                    Filter
                </button>
                <a href="{{ route('reports.staff.discounts.download', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">
                    Export PDF
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-primary-200 flex items-center justify-center">
                        <i class="fas fa-percent text-primary-700"></i>
                    </div>
                </div>
                <p class="text-sm text-primary-700 mb-1">Total Discounts</p>
                <h3 class="text-2xl font-bold text-primary-900">TZS {{ number_format($totalDiscounts, 2) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-200 flex items-center justify-center">
                        <i class="fas fa-receipt text-blue-700"></i>
                    </div>
                </div>
                <p class="text-sm text-blue-700 mb-1">Discount Transactions</p>
                <h3 class="text-2xl font-bold text-blue-900">{{ number_format($sales->count()) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-purple-200 flex items-center justify-center">
                        <i class="fas fa-chart-bar text-purple-700"></i>
                    </div>
                </div>
                <p class="text-sm text-purple-700 mb-1">Avg. Discount</p>
                <h3 class="text-2xl font-bold text-purple-900">TZS {{ number_format($sales->count() > 0 ? $totalDiscounts / $sales->count() : 0, 2) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-green-200 flex items-center justify-center">
                        <i class="fas fa-tags text-green-700"></i>
                    </div>
                </div>
                <p class="text-sm text-green-700 mb-1">Largest Discount</p>
                <h3 class="text-2xl font-bold text-green-900">TZS {{ number_format($sales->max('discount'), 2) }}</h3>
            </div>
        </div>

        <!-- Discounts Table -->
        <div class="border border-gray-100 rounded-xl p-5">
            <h4 class="font-semibold text-primary-900 mb-4">Discount Transactions</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-700 font-medium">Date</th>
                            <th class="px-4 py-3 text-left text-gray-700 font-medium">Invoice #</th>
                            <th class="px-4 py-3 text-left text-gray-700 font-medium">Cashier</th>
                            <th class="px-4 py-3 text-right text-gray-700 font-medium">Discount</th>
                            <th class="px-4 py-3 text-right text-gray-700 font-medium">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($sales as $sale)
                        <tr>
                            <td class="px-4 py-3">{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3 font-medium">{{ $sale->invoice_number }}</td>
                            <td class="px-4 py-3">{{ $sale->user ? $sale->user->name : 'N/A' }}</td>
                            <td class="px-4 py-3 text-right text-orange-600 font-semibold">TZS {{ number_format($sale->discount, 2) }}</td>
                            <td class="px-4 py-3 text-right">TZS {{ number_format($sale->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function filterReport() {
    const startDate = document.getElementById('start-date-filter').value;
    const endDate = document.getElementById('end-date-filter').value;
    window.location.href = `?start_date=${startDate}&end_date=${endDate}`;
}
</script>
@endsection