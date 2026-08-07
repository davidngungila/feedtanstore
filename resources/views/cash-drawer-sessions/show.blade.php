@extends('layouts.app')

@section('page-title', 'Cash Drawer Session Details')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-primary-900">Cash Drawer Session</h1>
        <p class="text-gray-600">{{ $session->session_number }}</p>
    </div>

    <!-- Session Status -->
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm text-gray-600">Status</p>
                <span class="px-3 py-1 text-sm font-semibold rounded-full 
                    {{ $session->status == 'opened' ? 'bg-green-100 text-green-700' : 
                       ($session->status == 'closed' ? 'bg-yellow-100 text-yellow-700' : 
                       'bg-blue-100 text-blue-700') }}">
                    {{ ucfirst($session->status) }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-600">Cashier</p>
                <p class="font-semibold">{{ $session->user->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Opened At</p>
                <p class="font-semibold">{{ $session->opened_at ? $session->opened_at->format('M d, Y H:i') : 'N/A' }}</p>
            </div>
            @if($session->closed_at)
            <div>
                <p class="text-sm text-gray-600">Closed At</p>
                <p class="font-semibold">{{ $session->closed_at->format('M d, Y H:i') }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Financial Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="card rounded-2xl p-6">
            <p class="text-gray-600 text-sm mb-1">Opening Balance</p>
            <p class="text-2xl font-bold text-primary-600">TZS {{ number_format($session->opening_balance, 0) }}</p>
        </div>
        <div class="card rounded-2xl p-6">
            <p class="text-gray-600 text-sm mb-1">Total Cash Sales</p>
            <p class="text-2xl font-bold text-green-600">TZS {{ number_format($totalCashSales, 0) }}</p>
        </div>
        <div class="card rounded-2xl p-6">
            <p class="text-gray-600 text-sm mb-1">Expected Balance</p>
            <p class="text-2xl font-bold text-blue-600">TZS {{ number_format($session->expected_balance ?? 0, 0) }}</p>
        </div>
        <div class="card rounded-2xl p-6">
            <p class="text-gray-600 text-sm mb-1">Actual Balance</p>
            <p class="text-2xl font-bold {{ $session->difference >= 0 ? 'text-green-600' : 'text-red-600' }}">
                TZS {{ number_format($session->closing_balance ?? 0, 0) }}
            </p>
        </div>
    </div>

    @if($session->status == 'closed')
    <!-- Difference -->
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-600 text-sm mb-1">Difference</p>
                <p class="text-3xl font-bold {{ $session->difference >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    TZS {{ number_format($session->difference ?? 0, 0) }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600">{{ $session->difference >= 0 ? 'Overage' : 'Shortage' }}</p>
            </div>
        </div>
    </div>
    @endif

    <!-- Actions -->
    @if($session->status == 'opened')
    <div class="card rounded-2xl p-6 mb-6">
        <a href="{{ route('cash-drawer-sessions.close', $session) }}" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <i class="fas fa-lock mr-2"></i>Close Cash Drawer
        </a>
    </div>
    @elseif($session->status == 'closed' && in_array(auth()->user()->role, ['admin', 'manager']))
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex gap-4">
            <a href="{{ route('cash-drawer-sessions.report', $session) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-file-pdf mr-2"></i>Generate Report
            </a>
            <form action="{{ route('cash-drawer-sessions.reconcile', $session) }}" method="POST" class="flex-1">
                @method('PUT')
                @csrf
                <div class="flex gap-4">
                    <input type="text" name="notes" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Reconciliation notes...">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-check mr-2"></i>Reconcile
                    </button>
                </div>
            </form>
        </div>
    </div>
    @elseif($session->status == 'closed')
    <div class="card rounded-2xl p-6 mb-6 bg-yellow-50">
        <div class="flex items-center">
            <i class="fas fa-clock text-yellow-600 text-xl mr-3"></i>
            <p class="text-yellow-800">Waiting for manager reconciliation before logout.</p>
        </div>
    </div>
    @elseif($session->status == 'reconciled')
    <div class="card rounded-2xl p-6 mb-6 bg-green-50">
        <div class="flex items-center justify-between">
            <div class="flex items-center">
                <i class="fas fa-check-circle text-green-600 text-xl mr-3"></i>
                <p class="text-green-800">Session reconciled by {{ $session->reconciler->name }} on {{ $session->reconciled_at->format('M d, Y H:i') }}</p>
            </div>
            <a href="{{ route('cash-drawer-sessions.report', $session) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-file-pdf mr-2"></i>View Report
            </a>
        </div>
    </div>
    @endif

    <!-- Sales List -->
    <div class="card rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-bold text-primary-900">Sales in this Session</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Sale Number</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Time</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Payment Method</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($session->sales as $sale)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $sale->sale_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $sale->created_at->format('H:i') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">TZS {{ number_format($sale->total, 0) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ ucfirst($sale->payment_method) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
