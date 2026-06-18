@extends('layouts.app')

@section('page-title', 'Finance Dashboard')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="card rounded-2xl p-6 bg-gradient-to-br from-green-50 to-green-100 border border-green-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-600">Total Income</h3>
                <i class="fas fa-arrow-up text-green-600 text-2xl flex-shrink-0"></i>
            </div>
            <p class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-800 break-all">TZS {{ number_format($totalIncome, 2) }}</p>
        </div>
        
        <div class="card rounded-2xl p-6 bg-gradient-to-br from-red-50 to-red-100 border border-red-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-600">Total Expenses</h3>
                <i class="fas fa-arrow-down text-red-600 text-2xl flex-shrink-0"></i>
            </div>
            <p class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-800 break-all">TZS {{ number_format($totalExpenses, 2) }}</p>
        </div>
        
        <div class="card rounded-2xl p-6 bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-600">Cash on Hand</h3>
                <i class="fas fa-money-bill text-blue-600 text-2xl flex-shrink-0"></i>
            </div>
            <p class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-800 break-all">TZS {{ number_format($cashOnHand, 2) }}</p>
        </div>
        
        <div class="card rounded-2xl p-6 bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-600">Bank & Mobile Balance</h3>
                <i class="fas fa-university text-purple-600 text-2xl flex-shrink-0"></i>
            </div>
            <p class="text-xl md:text-2xl lg:text-3xl font-bold text-gray-800 break-all">TZS {{ number_format($bankBalance + $mobileMoneyBalance, 2) }}</p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Recent Accounting Entries -->
        <div class="card rounded-2xl p-6">
            <h2 class="text-xl font-bold text-primary-900 mb-4">Recent Transactions</h2>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-600">Date</th>
                            <th class="px-4 py-3 text-left text-gray-600">Account</th>
                            <th class="px-4 py-3 text-left text-gray-600">Type</th>
                            <th class="px-4 py-3 text-left text-gray-600">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($recentEntries as $entry)
                            <tr>
                                <td class="px-4 py-3">{{ $entry->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-4 py-3 font-medium">{{ $entry->account }}</td>
                                <td class="px-4 py-3">
                                    <span class="px-2 py-1 rounded-full text-xs font-bold {{ $entry->type === 'debit' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                        {{ strtoupper($entry->type) }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 font-bold">TZS {{ number_format($entry->amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-8 text-center text-gray-500">No transactions yet!</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="card rounded-2xl p-6">
            <h2 class="text-xl font-bold text-primary-900 mb-4">Quick Actions</h2>
            <div class="grid grid-cols-2 gap-4">
                <a href="{{ route('finance.income') }}" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-dollar-sign text-green-500 mb-2 text-xl"></i>
                    <h4 class="font-semibold">Add Income</h4>
                </a>
                
                <a href="{{ route('finance.expenses') }}" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-file-invoice-dollar text-red-500 mb-2 text-xl"></i>
                    <h4 class="font-semibold">Add Expense</h4>
                </a>
                
                <a href="{{ route('finance.balance-sheet') }}" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-balance-scale text-yellow-500 mb-2 text-xl"></i>
                    <h4 class="font-semibold">Balance Sheet</h4>
                </a>
                
                <a href="{{ route('finance.income-statement') }}" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-chart-pie text-indigo-500 mb-2 text-xl"></i>
                    <h4 class="font-semibold">Income Statement</h4>
                </a>
                
                <a href="{{ route('finance.reports') }}" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-chart-line text-blue-500 mb-2 text-xl"></i>
                    <h4 class="font-semibold">View Reports</h4>
                </a>
                
                <a href="{{ route('finance.transactions') }}" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <i class="fas fa-list text-purple-500 mb-2 text-xl"></i>
                    <h4 class="font-semibold">All Transactions</h4>
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
