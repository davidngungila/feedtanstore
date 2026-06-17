@extends('layouts.app')

@section('page-title', 'Branch Comparison')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class='text-xl font-bold text-primary-900'>Branch Comparison</h2>
            <div class="flex items-center gap-3">
                <input type="date" name="start_date" value="{{ $startDate }}" class="form-input input-field px-4 py-2" id="start-date-filter">
                <input type="date" name="end_date" value="{{ $endDate }}" class="form-input input-field px-4 py-2" id="end-date-filter">
                <button onclick="filterReport()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">
                    Filter
                </button>
                <a href="{{ route('reports.advanced.branch-comparison.download', ['start_date' => $startDate, 'end_date' => $endDate]) }}" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">
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
                <h3 class="text-2xl font-bold text-primary-900">TZS {{ number_format($totalSales, 2) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-200 flex items-center justify-center">
                        <i class="fas fa-receipt text-blue-700"></i>
                    </div>
                </div>
                <p class="text-sm text-blue-700 mb-1">Transactions</p>
                <h3 class="text-2xl font-bold text-blue-900">{{ number_format($totalTransactions) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-purple-200 flex items-center justify-center">
                        <i class="fas fa-chart-bar text-purple-700"></i>
                    </div>
                </div>
                <p class="text-sm text-purple-700 mb-1">Average Sale</p>
                <h3 class="text-2xl font-bold text-purple-900">TZS {{ number_format($totalTransactions > 0 ? $totalSales / $totalTransactions : 0, 2) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-green-200 flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-green-700"></i>
                    </div>
                </div>
                <p class="text-sm text-green-700 mb-1">Items Sold</p>
                <h3 class="text-2xl font-bold text-green-900">{{ number_format($totalItems) }}</h3>
            </div>
        </div>

        <!-- Payment Method Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <div class="border border-gray-100 rounded-xl p-5">
                <h4 class="font-semibold text-primary-900 mb-4">Payment Methods</h4>
                <div class="space-y-3">
                    @foreach($paymentMethods as $method)
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700">{{ ucfirst($method->payment_method) }}</span>
                        <span class="font-semibold text-primary-900">TZS {{ number_format($method->total, 2) }} ({{ $method->count }} txns)</span>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="border border-gray-100 rounded-xl p-5">
                <h4 class="font-semibold text-primary-900 mb-4">Daily Performance</h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-2 text-left text-gray-700 font-medium">Date</th>
                                <th class="px-4 py-2 text-right text-gray-700 font-medium">Sales</th>
                                <th class="px-4 py-2 text-right text-gray-700 font-medium">Txns</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($dailySales as $day)
                            <tr>
                                <td class="px-4 py-2">{{ $day['date'] }}</td>
                                <td class="px-4 py-2 text-right">TZS {{ number_format($day['sales'], 2) }}</td>
                                <td class="px-4 py-2 text-right">{{ number_format($day['transactions']) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
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