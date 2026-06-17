@extends('layouts.app')

@section('page-title', 'Gross Profit')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class="text-xl font-bold text-primary-900">Gross Profit</h2>
            <div class="flex items-center gap-3">
                <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}" class="form-input input-field px-4 py-2" id="start-date-filter">
                <span class="text-gray-500">to</span>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}" class="form-input input-field px-4 py-2" id="end-date-filter">
                <button onclick="filterReport()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">
                    Filter
                </button>
                <a href="{{ route('reports.profit.gross.download', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">
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
                <p class="text-sm text-blue-700 mb-1">Total Cost</p>
                <h3 class="text-2xl font-bold text-blue-900">TZS {{ number_format($totalCost, 2) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-green-200 flex items-center justify-center">
                        <i class="fas fa-chart-line text-green-700"></i>
                    </div>
                </div>
                <p class="text-sm text-green-700 mb-1">Gross Profit</p>
                <h3 class="text-2xl font-bold text-green-900">TZS {{ number_format($totalProfit, 2) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-purple-200 flex items-center justify-center">
                        <i class="fas fa-boxes text-purple-700"></i>
                    </div>
                </div>
                <p class="text-sm text-purple-700 mb-1">Products Sold</p>
                <h3 class="text-2xl font-bold text-purple-900">{{ $products->count() }}</h3>
            </div>
        </div>

        <!-- Products Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Product</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Category</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Brand</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Qty Sold</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Total Sales</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Total Cost</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Gross Profit</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($products as $product)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $product->name }}</td>
                        <td class="px-4 py-3">{{ $product->category ? $product->category->name : 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $product->brand ? $product->brand->name : 'N/A' }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($product->total_qty) }}</td>
                        <td class="px-4 py-3 text-right">TZS {{ number_format($product->total_sales, 2) }}</td>
                        <td class="px-4 py-3 text-right">TZS {{ number_format($product->total_cost, 2) }}</td>
                        <td class="px-4 py-3 text-right font-semibold text-green-700">TZS {{ number_format($product->total_sales - $product->total_cost, 2) }}</td>
                    </tr>
                    @endforeach
                    @if($products->isEmpty())
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
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
