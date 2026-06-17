@extends('layouts.app')

@section('page-title', 'Purchase vs Sales')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class="text-xl font-bold text-primary-900">Purchase vs Sales</h2>
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <label for="start_date" class="text-sm font-medium text-gray-700">From:</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date', today()->startOfMonth()->toDateString()) }}" class="form-input input-field px-4 py-2">
                </div>
                <div class="flex items-center gap-2">
                    <label for="end_date" class="text-sm font-medium text-gray-700">To:</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date', today()->toDateString()) }}" class="form-input input-field px-4 py-2">
                </div>
                <button onclick="filterReport()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">
                    Filter
                </button>
                <a href="{{ route('reports.purchasing.vs-sales.download', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">
                    Export PDF
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-200 flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-blue-700"></i>
                    </div>
                </div>
                <p class="text-sm text-blue-700 mb-1">Total Purchases</p>
                <h3 class="text-2xl font-bold text-blue-900">TZS {{ number_format($purchases, 2) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-yellow-200 flex items-center justify-center">
                        <i class="fas fa-cash-register text-yellow-700"></i>
                    </div>
                </div>
                <p class="text-sm text-yellow-700 mb-1">Total Sales</p>
                <h3 class="text-2xl font-bold text-yellow-900">TZS {{ number_format($sales, 2) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-green-200 flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-green-700"></i>
                    </div>
                </div>
                <p class="text-sm text-green-700 mb-1">Profit (Sales - Purchases)</p>
                <h3 class="text-2xl font-bold text-green-900">TZS {{ number_format($sales - $purchases, 2) }}</h3>
            </div>
        </div>
    </div>
</div>

<script>
function filterReport() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    window.location.href = `?start_date=${startDate}&end_date=${endDate}`;
}
</script>
@endsection
