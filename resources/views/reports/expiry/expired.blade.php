@extends('layouts.app')

@section('page-title', 'Expired')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class='text-xl font-bold text-primary-900'>Expired</h2>
            <div class="flex items-center gap-3">
                <input type="date" name="date" value="{{ request('date', date('Y-m-d')) }}" class="form-input input-field px-4 py-2" id="date-filter">
                <button onclick="filterReport()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">
                    Filter
                </button>
                <button class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">
                    Export PDF
                </button>
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
                <h3 class="text-2xl font-bold text-primary-900">TZS 0.00</h3>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-200 flex items-center justify-center">
                        <i class="fas fa-receipt text-blue-700"></i>
                    </div>
                </div>
                <p class="text-sm text-blue-700 mb-1">Transactions</p>
                <h3 class="text-2xl font-bold text-blue-900">0</h3>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-purple-200 flex items-center justify-center">
                        <i class="fas fa-chart-bar text-purple-700"></i>
                    </div>
                </div>
                <p class="text-sm text-purple-700 mb-1">Average Sale</p>
                <h3 class="text-2xl font-bold text-purple-900">TZS 0.00</h3>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-green-200 flex items-center justify-center">
                        <i class="fas fa-shopping-cart text-green-700"></i>
                    </div>
                </div>
                <p class="text-sm text-green-700 mb-1">Items Sold</p>
                <h3 class="text-2xl font-bold text-green-900">0</h3>
            </div>
        </div>

        <!-- Payment Method Breakdown -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="border border-gray-100 rounded-xl p-5">
                <h4 class="font-semibold text-primary-900 mb-4">Payment Methods</h4>
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700">Cash</span>
                        <span class="font-semibold text-primary-900">TZS 0.00</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700">Card</span>
                        <span class="font-semibold text-primary-900">TZS 0.00</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700">Mobile Money</span>
                        <span class="font-semibold text-primary-900">TZS 0.00</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-gray-700">Credit</span>
                        <span class="font-semibold text-primary-900">TZS 0.00</span>
                    </div>
                </div>
            </div>

            <div class="border border-gray-100 rounded-xl p-5">
                <h4 class="font-semibold text-primary-900 mb-4">Sales Chart</h4>
                <div class="h-48 flex items-center justify-center text-gray-400">
                    <i class="fas fa-chart-line text-4xl"></i>
                </div>
            </div>
        </div>

        <!-- Transactions Table -->
        <div class="mt-6 border-t pt-6">
            <h4 class="font-semibold text-primary-900 mb-4">Transactions</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-700 font-medium">Invoice #</th>
                            <th class="px-4 py-3 text-left text-gray-700 font-medium">Time</th>
                            <th class="px-4 py-3 text-left text-gray-700 font-medium">Customer</th>
                            <th class="px-4 py-3 text-left text-gray-700 font-medium">Cashier</th>
                            <th class="px-4 py-3 text-left text-gray-700 font-medium">Payment</th>
                            <th class="px-4 py-3 text-right text-gray-700 font-medium">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                                No transactions found for this date
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function filterReport() {
    const date = document.getElementById('date-filter').value;
    window.location.href = `?date=${date}`;
}
</script>
@endsection
