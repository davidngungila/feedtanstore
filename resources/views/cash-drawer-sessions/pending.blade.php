@extends('layouts.app')

@section('page-title', 'Reconciliation Queue')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-primary-900">Cash Drawer Reconciliation Queue</h1>
        <p class="text-gray-600">Review closed cash drawer sessions and approve them so cashiers can logout.</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="card rounded-2xl p-5">
            <p class="text-gray-600 text-sm mb-1">Awaiting Approval</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $pendingCount }}</p>
        </div>
        <div class="card rounded-2xl p-5">
            <p class="text-gray-600 text-sm mb-1">Cash Value Pending</p>
            <p class="text-2xl font-bold text-blue-600">TZS {{ number_format($pendingValue, 0) }}</p>
        </div>
        <div class="card rounded-2xl p-5">
            <p class="text-gray-600 text-sm mb-1">Net Difference</p>
            <p class="text-2xl font-bold {{ $pendingDifference >= 0 ? 'text-green-600' : 'text-red-600' }}">
                TZS {{ number_format($pendingDifference, 0) }}
            </p>
        </div>
    </div>

    @if(session('error'))
    <div class="mb-4 px-4 py-3 rounded-lg bg-red-50 border border-red-200 text-red-700 text-sm">
        {{ session('error') }}
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
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Notes</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($pendingSessions as $session)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="font-semibold text-primary-900">{{ $session->session_number }}</span>
                            <span class="ml-2 px-2 py-0.5 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
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
                        <td class="px-6 py-4 text-sm text-gray-600 max-w-[180px] truncate" title="{{ $session->notes ?? '' }}">
                            {{ $session->notes ?: '-' }}
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
                                <form action="{{ route('cash-drawer-sessions.reconcile', $session) }}" method="POST" class="flex flex-col gap-2">
                                    @method('PUT')
                                    @csrf
                                    <input type="text" name="notes" placeholder="Approval notes..." class="px-2 py-1.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm w-full">
                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded-lg text-sm font-medium transition-colors whitespace-nowrap">
                                        <i class="fas fa-check mr-1"></i>Approve
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="px-6 py-12 text-center text-gray-500">
                            <i class="fas fa-check-circle text-4xl mb-4 text-green-300"></i>
                            <p class="text-lg font-medium">No sessions awaiting reconciliation</p>
                            <p class="text-sm">All closed cash drawer sessions have been approved.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($pendingSessions->hasPages())
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            {{ $pendingSessions->links() }}
        </div>
        @endif
    </div>
</div>
@endsection