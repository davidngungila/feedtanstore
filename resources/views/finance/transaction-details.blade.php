@extends('layouts.app')

@section('page-title', 'Transaction Details')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Transaction Details</h2>
            <a href="{{ route('finance.transactions') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Transactions
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="text-sm text-gray-600 mb-1">Reference Number</div>
                <div class="font-semibold text-lg">
                    <a href="{{ route('finance.transactions.show', $entry) }}" class="text-primary-600 hover:text-primary-800">
                        {{ $entry->reference_number }}
                    </a>
                </div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Date & Time</div>
                <div class="font-semibold">{{ $entry->created_at->format('l, d F Y H:i:s') }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Reference Type</div>
                <div class="font-semibold">{{ $entry->reference_type ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Account</div>
                <div class="font-semibold">{{ $entry->account }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Type</div>
                <div class="font-semibold">
                    <span class="px-2 py-1 rounded-full text-xs font-bold {{ $entry->type === 'debit' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                        {{ strtoupper($entry->type) }}
                    </span>
                </div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Amount</div>
                <div class="font-bold text-2xl {{ $entry->type === 'debit' ? 'text-blue-700' : 'text-green-700' }}">TZS {{ number_format($entry->amount, 2) }}</div>
            </div>
            <div class="md:col-span-2">
                <div class="text-sm text-gray-600 mb-1">Description</div>
                <div class="font-semibold">{{ $entry->description }}</div>
            </div>
        </div>
    </div>

    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-primary-900 mb-4">Related Accounting Entries</h3>
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
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($relatedEntries as $related)
                    <tr class="{{ $related->id === $entry->id ? 'bg-primary-50' : '' }}">
                        <td class="px-4 py-3">{{ $related->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('finance.transactions.show', $related) }}" class="text-primary-600 hover:text-primary-800 font-semibold">
                                {{ $related->reference_number }}
                            </a>
                        </td>
                        <td class="px-4 py-3 font-medium">{{ $related->account }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-bold {{ $related->type === 'debit' ? 'bg-blue-100 text-blue-800' : 'bg-green-100 text-green-800' }}">
                                {{ strtoupper($related->type) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 font-bold">TZS {{ number_format($related->amount, 2) }}</td>
                        <td class="px-4 py-3">{{ $related->description }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
