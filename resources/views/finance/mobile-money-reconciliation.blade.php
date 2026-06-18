@extends('layouts.app')

@section('page-title', 'Mobile Money Reconciliation')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <!-- Mobile Money Accounts Summary -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        @forelse($accounts as $account)
            <div class="card rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-bold text-primary-900">{{ $account->provider }}</h3>
                        <p class="text-sm text-gray-600">{{ $account->phone_number }}</p>
                    </div>
                    <span class="px-2 py-1 rounded-full text-xs font-bold {{ $account->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $account->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <p class="text-2xl font-bold text-green-700">TZS {{ number_format($account->balance, 2) }}</p>
            </div>
        @empty
            <div class="col-span-3">
                <div class="card rounded-2xl p-6 text-center">
                    <p class="text-gray-600">No active mobile money accounts found!</p>
                    <a href="{{ route('finance.mobile-money.create') }}" class="inline-block mt-4 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                        Add Account
                    </a>
                </div>
            </div>
        @endforelse
    </div>
    
    <!-- Reconciliation Table -->
    <div class="card rounded-2xl p-6">
        <h2 class="text-xl font-bold text-primary-900 mb-4">Mobile Money Transactions</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-600">Date</th>
                        <th class="px-4 py-3 text-left text-gray-600">Reference</th>
                        <th class="px-4 py-3 text-left text-gray-600">Type</th>
                        <th class="px-4 py-3 text-left text-gray-600">Amount</th>
                        <th class="px-4 py-3 text-left text-gray-600">Description</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($entries as $entry)
                        <tr>
                            <td class="px-4 py-3">{{ $entry->created_at->format('d/m/Y H:i') }}</td>
                            <td class="px-4 py-3 font-medium">{{ $entry->reference_number }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-bold {{ $entry->type === 'debit' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ strtoupper($entry->type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-bold">TZS {{ number_format($entry->amount, 2) }}</td>
                            <td class="px-4 py-3">{{ $entry->description }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">No mobile money transactions yet!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($entries->hasPages())
            <div class="mt-6">
                {{ $entries->links() }}
            </div>
        @endif
    </div>
</div>
@endsection