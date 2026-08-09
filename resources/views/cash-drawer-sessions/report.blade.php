@extends('layouts.app')

@section('page-title', 'Cash Drawer Reconciliation Report')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6 flex items-center justify-between">
        <div class="flex items-center gap-4">
            <a href="{{ route('cash-drawer-sessions.show', $cashDrawerSession) }}" class="text-gray-600 hover:text-gray-900 transition-colors">
                <i class="fas fa-arrow-left text-xl"></i>
            </a>
            <div>
                <h1 class="text-2xl font-bold text-primary-900">Cash Drawer Reconciliation Report</h1>
                <p class="text-gray-600">{{ $cashDrawerSession->session_number }}</p>
            </div>
        </div>
        <div class="flex gap-3">
            @if($cashDrawerSession->status == 'closed' && in_array(auth()->user()->role, ['admin', 'manager']))
            <form action="{{ route('cash-drawer-sessions.reconcile', $cashDrawerSession) }}" method="POST">
                @method('PUT')
                @csrf
                <div class="flex gap-2">
                    <input type="text" name="notes" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Reconciliation notes...">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-check mr-2"></i>Reconcile
                    </button>
                </div>
            </form>
            @endif
            <button onclick="window.print()" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-print mr-2"></i>Print Report
            </button>
        </div>
    </div>

    <div class="card rounded-2xl p-8 mb-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
            <div>
                <p class="text-sm text-gray-600">Cashier</p>
                <p class="font-semibold">{{ $cashDrawerSession->user->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Session Number</p>
                <p class="font-semibold">{{ $cashDrawerSession->session_number }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Opened At</p>
                <p class="font-semibold">{{ $cashDrawerSession->opened_at->format('M d, Y H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Closed At</p>
                <p class="font-semibold">{{ $cashDrawerSession->closed_at ? $cashDrawerSession->closed_at->format('M d, Y H:i') : 'N/A' }}</p>
            </div>
        </div>

        <h2 class="text-lg font-bold text-primary-900 mb-4">Financial Summary</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-600">Opening Balance</p>
                <p class="text-2xl font-bold text-primary-600">TZS {{ number_format($cashDrawerSession->opening_balance, 0) }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-600">Total Cash Sales</p>
                <p class="text-2xl font-bold text-green-600">TZS {{ number_format($totalCashSales, 0) }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-600">Total Card Sales</p>
                <p class="text-2xl font-bold text-blue-600">TZS {{ number_format($totalCardSales, 0) }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-600">Total Mobile Sales</p>
                <p class="text-2xl font-bold text-purple-600">TZS {{ number_format($totalMobileSales, 0) }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-blue-50 rounded-lg p-4">
                <p class="text-sm text-gray-600">Expected Balance</p>
                <p class="text-2xl font-bold text-blue-600">TZS {{ number_format($cashDrawerSession->expected_balance ?? 0, 0) }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-600">Actual Balance</p>
                <p class="text-2xl font-bold text-gray-900">TZS {{ number_format($cashDrawerSession->closing_balance ?? 0, 0) }}</p>
            </div>
            <div class="bg-yellow-50 rounded-lg p-4">
                <p class="text-sm text-gray-600">Difference</p>
                <p class="text-2xl font-bold {{ $cashDrawerSession->difference >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    TZS {{ number_format($cashDrawerSession->difference ?? 0, 0) }}
                </p>
            </div>
        </div>

        @if($cashDrawerSession->notes)
        <div class="mb-8">
            <h2 class="text-lg font-bold text-primary-900 mb-2">Notes</h2>
            <p class="text-gray-600">{{ $cashDrawerSession->notes }}</p>
        </div>
        @endif

        @if($cashDrawerSession->reconciled_by)
        <div class="bg-green-50 rounded-lg p-4 mb-8">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                <div>
                    <p class="font-semibold text-green-800">Reconciled</p>
                    <p class="text-sm text-green-700">By {{ $cashDrawerSession->reconciler->name }} on {{ $cashDrawerSession->reconciled_at->format('M d, Y H:i') }}</p>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="card rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-bold text-primary-900">Sales Breakdown</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Sale Number</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Payment Method</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Items</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($cashDrawerSession->sales as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold text-gray-900">
                            <a href="{{ route('sales.show', $sale) }}" class="text-primary-600 hover:text-primary-800 hover:underline">
                                {{ $sale->invoice_number ?? 'N/A' }}
                            </a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $sale->created_at ? $sale->created_at->format('H:i') : 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">TZS {{ number_format($sale->total ?? 0, 0) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $sale->payment_method ? ucfirst($sale->payment_method) : 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $sale->items ? $sale->items->count() : 0 }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
