@extends('layouts.app')

@section('page-title', 'Net Profit')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class="text-xl font-bold text-primary-900">Net Profit</h2>
            <div class="flex items-center gap-3">
                <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}" class="form-input input-field px-4 py-2" id="start-date-filter">
                <span class="text-gray-500">to</span>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}" class="form-input input-field px-4 py-2" id="end-date-filter">
                <button onclick="filterReport()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">
                    Filter
                </button>
                <a href="{{ route('reports.profit.net.download', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">
                    Export PDF
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-primary-200 flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-primary-700"></i>
                    </div>
                </div>
                <p class="text-sm text-primary-700 mb-1">Total Sales</p>
                <h3 class="text-2xl font-bold text-primary-900">TZS {{ number_format($sales, 2) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-200 flex items-center justify-center">
                        <i class="fas fa-receipt text-blue-700"></i>
                    </div>
                </div>
                <p class="text-sm text-blue-700 mb-1">Cost of Goods Sold</p>
                <h3 class="text-2xl font-bold text-blue-900">TZS {{ number_format($costOfGoodsSold, 2) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-purple-200 flex items-center justify-center">
                        <i class="fas fa-wallet text-purple-700"></i>
                    </div>
                </div>
                <p class="text-sm text-purple-700 mb-1">Total Expenses</p>
                <h3 class="text-2xl font-bold text-purple-900">TZS {{ number_format($expenses, 2) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-green-200 flex items-center justify-center">
                        <i class="fas fa-chart-line text-green-700"></i>
                    </div>
                </div>
                <p class="text-sm text-green-700 mb-1">Net Profit</p>
                <h3 class="text-2xl font-bold text-green-900">TZS {{ number_format($netProfit, 2) }}</h3>
            </div>
        </div>

        <!-- Profit Breakdown -->
        <div class="bg-gray-50 rounded-xl p-6 mb-6">
            <h3 class="text-lg font-semibold text-primary-900 mb-4">Profit Breakdown</h3>
            <div class="space-y-4">
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">Total Sales</span>
                    <span class="font-semibold text-lg">TZS {{ number_format($sales, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">Cost of Goods Sold</span>
                    <span class="font-semibold text-lg text-red-600">- TZS {{ number_format($costOfGoodsSold, 2) }}</span>
                </div>
                <div class="border-t border-gray-300 pt-4 flex justify-between items-center">
                    <span class="text-gray-700 font-semibold">Gross Profit</span>
                    <span class="font-semibold text-lg text-blue-700">TZS {{ number_format($grossProfit, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">Total Expenses</span>
                    <span class="font-semibold text-lg text-red-600">- TZS {{ number_format($expenses, 2) }}</span>
                </div>
                <div class="border-t border-gray-300 pt-4 flex justify-between items-center">
                    <span class="text-gray-700 font-semibold text-lg">Net Profit</span>
                    <span class="font-bold text-xl {{ $netProfit >= 0 ? 'text-green-700' : 'text-red-700' }}">TZS {{ number_format($netProfit, 2) }}</span>
                </div>
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
