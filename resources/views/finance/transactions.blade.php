@extends('layouts.app')

@section('page-title', 'Transactions')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <h2 class="text-xl font-bold text-primary-900 mb-6">Transactions</h2>
        
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700">Date</th>
                        <th class="px-4 py-3 text-left text-gray-700">Reference No</th>
                        <th class="px-4 py-3 text-left text-gray-700">Account</th>
                        <th class="px-4 py-3 text-left text-gray-700">Type</th>
                        <th class="px-4 py-3 text-left text-gray-700">Amount</th>
                        <th class="px-4 py-3 text-left text-gray-700">Description</th>
                        <th class="px-4 py-3 text-left text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($entries as $entry)
                    <tr>
                        <td class="px-4 py-3">{{ $entry->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('finance.transactions.show', $entry) }}" class="text-primary-600 hover:text-primary-800 font-semibold">
                                {{ $entry->reference_number }}
                            </a>
                        </td>
                        <td class="px-4 py-3 font-medium">{{ $entry->account }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-bold {{ $entry->type === 'debit' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ strtoupper($entry->type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-bold">TZS {{ number_format($entry->amount, 2) }}</td>
                        <td class="px-4 py-3">{{ $entry->description }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('finance.transactions.show', $entry) }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                                View Details
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">No transactions found.</td>
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
