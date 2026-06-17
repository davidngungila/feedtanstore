@extends('layouts.app')

@section('page-title', 'Expansion Readiness')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class='text-xl font-bold text-primary-900'>Expansion Readiness</h2>
            <div class="flex items-center gap-3">
                <input type="date" name="start_date" value="{{ $startDate }}" class="form-input input-field px-4 py-2" id="start-date-filter">
                <input type="date" name="end_date" value="{{ $endDate }}" class="form-input input-field px-4 py-2" id="end-date-filter">
                <button onclick="filterReport()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">
                    Filter
                </button>
                <a href="{{ route('reports.advanced.expansion-readiness.pdf', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">
                    Export PDF
                </a>
            </div>
        </div>

        <!-- Readiness Score -->
        <div class="mb-6 bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl p-6 border border-blue-100">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-gray-800 mb-2">Overall Expansion Readiness</h3>
                    <p class="text-sm text-gray-600">Based on sales growth, inventory turnover, customer retention, and cash flow</p>
                </div>
                @php
                    $score = 0;
                    $score += $salesGrowth['growth'] > 5 ? 25 : ($salesGrowth['growth'] > 0 ? 15 : 5);
                    $score += $inventoryTurnover > 3 ? 25 : ($inventoryTurnover > 1 ? 15 : 5);
                    $score += $customerRetention > 50 ? 25 : ($customerRetention > 20 ? 15 : 5);
                    $score += $cashFlowHealth['net'] > 0 ? 25 : ($cashFlowHealth['net'] > -1000 ? 15 : 5);
                @endphp
                <div class="text-center">
                    <div class="text-4xl font-bold text-blue-600">{{ $score }}/100</div>
                    <div class="text-sm text-gray-600">{{ $score >= 80 ? 'Excellent' : ($score >= 60 ? 'Good' : ($score >= 40 ? 'Fair' : 'Needs Improvement')) }}</div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-green-200 flex items-center justify-center">
                        <i class="fas fa-arrow-trend-up text-green-700"></i>
                    </div>
                </div>
                <p class="text-sm text-green-700 mb-1">Sales Growth</p>
                <h3 class="text-2xl font-bold text-green-900">{{ number_format($salesGrowth['growth'], 2) }}%</h3>
                <p class="text-xs text-green-600 mt-1">First Half: TZS {{ number_format($salesGrowth['first_half'], 0) }}</p>
                <p class="text-xs text-green-600">Second Half: TZS {{ number_format($salesGrowth['second_half'], 0) }}</p>
            </div>
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-orange-200 flex items-center justify-center">
                        <i class="fas fa-sync-alt text-orange-700"></i>
                    </div>
                </div>
                <p class="text-sm text-orange-700 mb-1">Inventory Turnover</p>
                <h3 class="text-2xl font-bold text-orange-900">{{ number_format($inventoryTurnover, 2) }}</h3>
                <p class="text-xs text-orange-600 mt-1">Times per period</p>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-purple-200 flex items-center justify-center">
                        <i class="fas fa-users text-purple-700"></i>
                    </div>
                </div>
                <p class="text-sm text-purple-700 mb-1">Customer Retention</p>
                <h3 class="text-2xl font-bold text-purple-900">{{ number_format($customerRetention, 2) }}%</h3>
                <p class="text-xs text-purple-600 mt-1">Repeat customers</p>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-200 flex items-center justify-center">
                        <i class="fas fa-wallet text-blue-700"></i>
                    </div>
                </div>
                <p class="text-sm text-blue-700 mb-1">Net Cash Flow</p>
                <h3 class="text-2xl font-bold text-blue-900 {{ $cashFlowHealth['net'] >= 0 ? 'text-green-900' : 'text-red-900' }}">TZS {{ number_format($cashFlowHealth['net'], 2) }}</h3>
                <p class="text-xs text-blue-600 mt-1">Sales: TZS {{ number_format($cashFlowHealth['sales'], 0) }} | Expenses: TZS {{ number_format($cashFlowHealth['expenses'], 0) }}</p>
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