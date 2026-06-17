@extends('layouts.app')

@section('page-title', 'Cash Reconciliation')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class="text-xl font-bold text-primary-900">Cash Reconciliation</h2>
            <div class="flex items-center gap-3">
                <input type="date" name="date" value="{{ request('date', $date) }}" class="form-input input-field px-4 py-2" id="date-filter">
                <button onclick="filterReport()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">
                    Filter
                </button>
                <a href="{{ route('reports.cash.reconciliation.download', ['date' => request('date')]) }}" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">
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
                <p class="text-sm text-primary-700 mb-1">Cash Sales</p>
                <h3 class="text-2xl font-bold text-primary-900">TZS {{ number_format($cashSales, 2) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-200 flex items-center justify-center">
                        <i class="fas fa-wallet text-blue-700"></i>
                    </div>
                </div>
                <p class="text-sm text-blue-700 mb-1">Expenses</p>
                <h3 class="text-2xl font-bold text-blue-900">TZS {{ number_format($expenses, 2) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-purple-200 flex items-center justify-center">
                        <i class="fas fa-calculator text-purple-700"></i>
                    </div>
                </div>
                <p class="text-sm text-purple-700 mb-1">Expected Cash</p>
                <h3 class="text-2xl font-bold text-purple-900">TZS {{ number_format($expectedCash, 2) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-green-200 flex items-center justify-center">
                        <i class="fas fa-balance-scale text-green-700"></i>
                    </div>
                </div>
                <p class="text-sm text-green-700 mb-1">Difference</p>
                <h3 class="text-2xl font-bold {{ $difference >= 0 ? 'text-green-900' : 'text-red-900' }}">
                    TZS {{ number_format($difference, 2) }}
                </h3>
            </div>
        </div>

        <!-- Reconciliation Breakdown -->
        <div class="bg-gray-50 rounded-xl p-6 mb-6">
            <h4 class="text-lg font-semibold text-primary-900 mb-4">Reconciliation Breakdown</h4>
            <div class="space-y-3">
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">Total Opening Cash</span>
                    <span class="font-semibold text-lg">TZS {{ number_format($totalOpeningCash, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">+ Cash Sales</span>
                    <span class="font-semibold text-lg text-green-700">TZS {{ number_format($cashSales, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">- Expenses</span>
                    <span class="font-semibold text-lg text-red-700">TZS {{ number_format($expenses, 2) }}</span>
                </div>
                <div class="border-t border-gray-300 pt-3 flex justify-between items-center">
                    <span class="text-gray-700 font-semibold">Expected Cash</span>
                    <span class="font-semibold text-lg text-blue-700">TZS {{ number_format($expectedCash, 2) }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-700">Actual Closing Cash</span>
                    <span class="font-semibold text-lg">TZS {{ number_format($totalClosingCash, 2) }}</span>
                </div>
                <div class="border-t border-gray-300 pt-3 flex justify-between items-center">
                    <span class="text-gray-700 font-semibold text-lg">Difference</span>
                    <span class="font-bold text-xl {{ $difference >= 0 ? 'text-green-700' : 'text-red-700' }}">
                        TZS {{ number_format($difference, 2) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Shifts Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Shift ID</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Cashier</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Opened At</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Closed At</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Opening Cash</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Closing Cash</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($shifts as $shift)
                    <tr>
                        <td class="px-4 py-3 font-medium">#{{ $shift->id }}</td>
                        <td class="px-4 py-3">{{ $shift->cashier ? $shift->cashier->name : 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $shift->created_at->format('H:i') }}</td>
                        <td class="px-4 py-3">{{ $shift->closed_at ? $shift->closed_at->format('H:i') : '-' }}</td>
                        <td class="px-4 py-3 text-right">TZS {{ number_format($shift->opening_cash, 2) }}</td>
                        <td class="px-4 py-3 text-right">{{ $shift->closing_cash ? 'TZS ' . number_format($shift->closing_cash, 2) : '-' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $shift->status === 'closed' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                {{ ucfirst($shift->status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                    @if($shifts->isEmpty())
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            No shifts found for this date
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
    const date = document.getElementById('date-filter').value;
    window.location.href = `?date=${date}`;
}
</script>
@endsection
