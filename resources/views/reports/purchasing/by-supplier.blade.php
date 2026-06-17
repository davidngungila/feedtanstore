@extends('layouts.app')

@section('page-title', 'Purchase by Supplier')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class="text-xl font-bold text-primary-900">Purchase by Supplier</h2>
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
                <a href="{{ route('reports.purchasing.by-supplier.download', ['start_date' => request('start_date'), 'end_date' => request('end_date')]) }}" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">
                    Export PDF
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-green-200 flex items-center justify-center">
                        <i class="fas fa-users text-green-700"></i>
                    </div>
                </div>
                <p class="text-sm text-green-700 mb-1">Number of Suppliers</p>
                <h3 class="text-2xl font-bold text-green-900">{{ $suppliers->count() }}</h3>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-200 flex items-center justify-center">
                        <i class="fas fa-dollar-sign text-blue-700"></i>
                    </div>
                </div>
                <p class="text-sm text-blue-700 mb-1">Total Purchases</p>
                <h3 class="text-2xl font-bold text-blue-900">TZS {{ number_format($suppliers->sum('total_purchases'), 2) }}</h3>
            </div>
        </div>

        <!-- Suppliers Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Supplier Name</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Email</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Phone</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Total Purchases</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($suppliers as $supplier)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $supplier->name }}</td>
                        <td class="px-4 py-3">{{ $supplier->email }}</td>
                        <td class="px-4 py-3">{{ $supplier->phone }}</td>
                        <td class="px-4 py-3 text-right font-semibold">TZS {{ number_format($supplier->total_purchases, 2) }}</td>
                    </tr>
                    @endforeach
                    @if($suppliers->isEmpty())
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                            No data found for this period
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
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    window.location.href = `?start_date=${startDate}&end_date=${endDate}`;
}
</script>
@endsection
