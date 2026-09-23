@extends('layouts.app')

@section('page-title', 'Daily Reconciliation')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6 flex flex-col md:flex-row md:items-end md:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-primary-900">Daily Reconciliation</h1>
            <p class="text-gray-600">Review and approve cash drawer sessions closed on {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <form action="{{ route('cash-drawer-sessions.daily') }}" method="GET" class="flex items-center gap-2">
                <input type="date" name="date" value="{{ $date }}" class="px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors text-sm whitespace-nowrap">
                    <i class="fas fa-calendar-day mr-1"></i>View Day
                </button>
            </form>
            <a href="{{ route('cash-drawer-sessions.reconciliations') }}" class="border border-gray-300 text-gray-700 hover:bg-gray-50 px-4 py-2 rounded-lg font-medium transition-colors text-sm whitespace-nowrap">
                <i class="fas fa-list mr-1"></i>Full Queue
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
        <div class="card rounded-2xl p-5">
            <p class="text-gray-600 text-sm mb-1">Sessions Closed</p>
            <p class="text-2xl font-bold text-primary-900">{{ $sessions->total() }}</p>
        </div>
        <div class="card rounded-2xl p-5">
            <p class="text-gray-600 text-sm mb-1">Awaiting Approval</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $pendingCount }}</p>
        </div>
        <div class="card rounded-2xl p-5">
            <p class="text-gray-600 text-sm mb-1">Approved</p>
            <p class="text-2xl font-bold text-green-600">{{ $reconciledCount }}</p>
        </div>
        <div class="card rounded-2xl p-5">
            <p class="text-gray-600 text-sm mb-1">Value Pending</p>
            <p class="text-xl font-bold text-blue-600">TZS {{ number_format($pendingValue, 0) }}</p>
        </div>
        <div class="card rounded-2xl p-5">
            <p class="text-gray-600 text-sm mb-1">Expected Total</p>
            <p class="text-xl font-bold text-gray-800">TZS {{ number_format($totalExpected, 0) }}</p>
        </div>
        <div class="card rounded-2xl p-5">
            <p class="text-gray-600 text-sm mb-1">Net Difference</p>
            <p class="text-xl font-bold {{ $totalDifference >= 0 ? 'text-green-600' : 'text-red-600' }}">
                TZS {{ number_format($totalDifference, 0) }}
            </p>
        </div>
    </div>

    @if(session('error'))
    <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
        {{ session('error') }}
    </div>
    @endif

    @if(session('success'))
    <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 text-sm">
        {{ session('success') }}
    </div>
    @endif

    <div class="card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Session</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Cashier</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Opened</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Closed</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Opening</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Expected</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Closing</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Difference</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Approved By</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($sessions as $session)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-semibold text-primary-900">{{ $session->session_number }}</span>
                            @if($session->status == 'closed')
                            <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                            @else
                            <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8 rounded-full bg-primary-100 flex items-center justify-center">
                                    <span class="text-xs font-medium text-primary-600">{{ strtoupper(substr($session->user->name, 0, 1)) }}</span>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">{{ $session->user->name }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $session->opened_at ? $session->opened_at->format('M d, H:i') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $session->closed_at ? $session->closed_at->format('M d, H:i') : 'N/A' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            TZS {{ number_format($session->opening_balance, 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            TZS {{ number_format($session->expected_balance ?? 0, 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            TZS {{ number_format($session->closing_balance ?? 0, 0) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <span class="font-semibold {{ ($session->difference ?? 0) >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ ($session->difference ?? 0) >= 0 ? '+' : '' }}{{ number_format($session->difference ?? 0, 0) }}
                            </span>
                            <span class="block text-xs text-gray-500">{{ ($session->difference ?? 0) >= 0 ? 'Overage' : 'Shortage' }}</span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                            {{ $session->reconciler->name ?? '-' }}
                        </td>
                        <td class="px-6 py-4">
                            <div class="flex flex-col gap-2">
                                <div class="flex gap-2">
                                    <a href="{{ route('cash-drawer-sessions.show', $session) }}" class="text-primary-600 hover:text-primary-900 font-medium text-sm whitespace-nowrap">
                                        Details
                                    </a>
                                    <a href="{{ route('cash-drawer-sessions.report', $session) }}" class="text-blue-600 hover:text-blue-800 font-medium text-sm whitespace-nowrap">
                                        Report
                                    </a>
                                </div>
                                @if($session->status == 'closed')
                                <form action="{{ route('cash-drawer-sessions.reconcile', $session) }}" method="POST" class="flex flex-col gap-2">
                                    @method('PUT')
                                    @csrf
                                    <input type="text" name="notes" placeholder="Approval notes..." class="px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm w-full">
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors whitespace-nowrap">
                                        <i class="fas fa-check mr-1"></i>Approve
                                    </button>
                                </form>
                                @else
                                <span class="text-xs text-gray-400">Reconciled {{ $session->reconciled_at ? $session->reconciled_at->format('H:i') : '' }}</span>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-calendar-xmark text-4xl mb-4 text-gray-300"></i>
                            <p class="text-lg font-medium">No sessions closed on this day</p>
                            <p class="text-sm">Try a different date.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($sessions->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $sessions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection