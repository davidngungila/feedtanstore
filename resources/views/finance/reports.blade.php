@extends('layouts.app')

@section('page-title', 'Financial Reports')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="card rounded-2xl p-6 bg-gradient-to-br from-blue-50 to-blue-100 border border-blue-200">
            <h4 class="text-sm font-medium text-blue-800 mb-2">Total Sales</h4>
            <p class="text-2xl font-bold text-blue-900">TZS {{ number_format($totalSales, 2) }}</p>
        </div>
        <div class="card rounded-2xl p-6 bg-gradient-to-br from-green-50 to-green-100 border border-green-200">
            <h4 class="text-sm font-medium text-green-800 mb-2">Total Income</h4>
            <p class="text-2xl font-bold text-green-900">TZS {{ number_format($totalIncome, 2) }}</p>
        </div>
        <div class="card rounded-2xl p-6 bg-gradient-to-br from-red-50 to-red-100 border border-red-200">
            <h4 class="text-sm font-medium text-red-800 mb-2">Total Expenses</h4>
            <p class="text-2xl font-bold text-red-900">TZS {{ number_format($totalExpenses, 2) }}</p>
        </div>
        <div class="card rounded-2xl p-6 bg-gradient-to-br from-purple-50 to-purple-100 border border-purple-200">
            <h4 class="text-sm font-medium text-purple-800 mb-2">Net Profit</h4>
            <p class="text-2xl font-bold text-purple-900">TZS {{ number_format($profit, 2) }}</p>
        </div>
    </div>

    <div class="card rounded-2xl p-6">
        <h2 class="text-xl font-bold text-primary-900 mb-6">Recent Accounting Entries</h2>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700">Date</th>
                        <th class="px-4 py-3 text-left text-gray-700">Reference No</th>
                        <th class="px-4 py-3 text-left text-gray-700">Account</th>
                        <th class="px-4 py-3 text-left text-gray-700">Description</th>
                        <th class="px-4 py-3 text-left text-gray-700">Debit</th>
                        <th class="px-4 py-3 text-left text-gray-700">Credit</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($recentEntries as $entry)
                    <tr>
                        <td class="px-4 py-3">{{ $entry->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $entry->reference_number }}</td>
                        <td class="px-4 py-3">{{ $entry->account }}</td>
                        <td class="px-4 py-3">{{ $entry->description ?? 'N/A' }}</td>
                        <td class="px-4 py-3">
                            @if($entry->type == 'debit')
                                <span class="font-semibold text-orange-700">TZS {{ number_format($entry->amount, 2) }}</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            @if($entry->type == 'credit')
                                <span class="font-semibold text-green-700">TZS {{ number_format($entry->amount, 2) }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">No recent accounting entries.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection