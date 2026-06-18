@extends('layouts.app')

@section('page-title', 'Financial Reports')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-primary-900 mb-2">Today</h3>
            <div class="mb-2">
                <p class="text-sm text-gray-600">Income</p>
                <p class="text-2xl font-bold text-green-700">TZS {{ number_format($dailyIncome, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Expenses</p>
                <p class="text-2xl font-bold text-red-700">TZS {{ number_format($dailyExpenses, 2) }}</p>
            </div>
        </div>
        
        <div class="card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-primary-900 mb-2">This Month</h3>
            <div class="mb-2">
                <p class="text-sm text-gray-600">Income</p>
                <p class="text-2xl font-bold text-green-700">TZS {{ number_format($monthlyIncome, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Expenses</p>
                <p class="text-2xl font-bold text-red-700">TZS {{ number_format($monthlyExpenses, 2) }}</p>
            </div>
        </div>
        
        <div class="card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-primary-900 mb-2">This Year</h3>
            <div class="mb-2">
                <p class="text-sm text-gray-600">Income</p>
                <p class="text-2xl font-bold text-green-700">TZS {{ number_format($yearlyIncome, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Expenses</p>
                <p class="text-2xl font-bold text-red-700">TZS {{ number_format($yearlyExpenses, 2) }}</p>
            </div>
        </div>
    </div>
    
    <!-- Quick Links -->
    <div class="card rounded-2xl p-6 mb-6">
        <h2 class="text-xl font-bold text-primary-900 mb-4">Quick Links</h2>
        <div class="flex flex-wrap gap-4">
            <a href="{{ route('finance.balance-sheet') }}" class="px-6 py-3 bg-gradient-to-r from-yellow-500 to-yellow-600 text-white rounded-lg font-semibold hover:from-yellow-600 hover:to-yellow-700 transition-all shadow-md">
                <i class="fas fa-balance-scale mr-2"></i> View Balance Sheet
            </a>
            <a href="{{ route('finance.income-statement') }}" class="px-6 py-3 bg-gradient-to-r from-indigo-500 to-indigo-600 text-white rounded-lg font-semibold hover:from-indigo-600 hover:to-indigo-700 transition-all shadow-md">
                <i class="fas fa-chart-pie mr-2"></i> View Income Statement
            </a>
        </div>
    </div>
    
    <!-- Recent Transactions -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-primary-900 mb-4">Recent Sales</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-600">Invoice</th>
                            <th class="px-4 py-3 text-left text-gray-600">Amount</th>
                            <th class="px-4 py-3 text-left text-gray-600">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($recentSales as $sale)
                            <tr>
                                <td class="px-4 py-3 font-semibold">{{ $sale->invoice_number }}</td>
                                <td class="px-4 py-3 font-bold">TZS {{ number_format($sale->total, 2) }}</td>
                                <td class="px-4 py-3">{{ $sale->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-gray-500">No recent sales!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-primary-900 mb-4">Recent Purchases</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-600">PO Number</th>
                            <th class="px-4 py-3 text-left text-gray-600">Amount</th>
                            <th class="px-4 py-3 text-left text-gray-600">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($recentPurchases as $po)
                            <tr>
                                <td class="px-4 py-3 font-semibold">{{ $po->po_number }}</td>
                                <td class="px-4 py-3 font-bold">TZS {{ number_format($po->total, 2) }}</td>
                                <td class="px-4 py-3">{{ $po->created_at->format('d/m/Y') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-8 text-center text-gray-500">No recent purchases!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection