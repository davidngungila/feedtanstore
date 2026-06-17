@extends('layouts.app')

@section('page-title', 'Loss Report')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class="text-xl font-bold text-primary-900">Loss Report</h2>
            <div class="flex items-center gap-3">
                <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}" class="form-input input-field px-4 py-2" id="start-date-filter">
                <span class="text-gray-500">to</span>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}" class="form-input input-field px-4 py-2" id="end-date-filter">
                <button onclick="filterReport()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">
                    Filter
                </button>
                <a href="{{ route('reports.profit.loss.download', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">
                    Export PDF
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-red-200 flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-red-700"></i>
                    </div>
                </div>
                <p class="text-sm text-red-700 mb-1">Total Loss</p>
                <h3 class="text-2xl font-bold text-red-900">TZS {{ number_format($totalLoss, 2) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-200 flex items-center justify-center">
                        <i class="fas fa-list text-blue-700"></i>
                    </div>
                </div>
                <p class="text-sm text-blue-700 mb-1">Adjustments</p>
                <h3 class="text-2xl font-bold text-blue-900">{{ $adjustments->count() }}</h3>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-purple-200 flex items-center justify-center">
                        <i class="fas fa-boxes text-purple-700"></i>
                    </div>
                </div>
                <p class="text-sm text-purple-700 mb-1">Products</p>
                <h3 class="text-2xl font-bold text-purple-900">{{ $adjustments->pluck('product_id')->unique()->count() }}</h3>
            </div>
            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-yellow-200 flex items-center justify-center">
                        <i class="fas fa-layer-group text-yellow-700"></i>
                    </div>
                </div>
                <p class="text-sm text-yellow-700 mb-1">Total Qty Lost</p>
                <h3 class="text-2xl font-bold text-yellow-900">{{ number_format($adjustments->sum('quantity')) }}</h3>
            </div>
        </div>

        <!-- Adjustments Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Date</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Product</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Quantity</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Cost Price</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Loss Amount</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Reason</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($adjustments as $adjustment)
                    <tr>
                        <td class="px-4 py-3">{{ $adjustment->created_at->format('Y-m-d H:i') }}</td>
                        <td class="px-4 py-3 font-medium">{{ $adjustment->product ? $adjustment->product->name : 'N/A' }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($adjustment->quantity) }}</td>
                        <td class="px-4 py-3 text-right">TZS {{ number_format($adjustment->product->cost_price ?? 0, 2) }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-red-700">TZS {{ number_format(($adjustment->product->cost_price ?? 0) * $adjustment->quantity, 2) }}</td>
                        <td class="px-4 py-3">{{ $adjustment->reason ?? 'N/A' }}</td>
                    </tr>
                    @endforeach
                    @if($adjustments->isEmpty())
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            No data found for this date range
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
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
