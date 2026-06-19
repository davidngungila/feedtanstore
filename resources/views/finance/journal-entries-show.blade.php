@extends('layouts.app')

@section('page-title', 'Journal Entry - ' . $journalEntry->journal_number)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Journal Entry - {{ $journalEntry->journal_number }}</h2>
            <a href="{{ route('finance.journal-entries') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Journal Entries
            </a>
        </div>

        <div class="flex items-center gap-4 mb-6">
            <span class="px-3 py-1 rounded-full text-sm font-bold {{ $journalEntry->is_balanced ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                {{ $journalEntry->is_balanced ? 'Balanced' : 'Unbalanced' }}
            </span>
            <span class="px-3 py-1 rounded-full text-sm font-bold {{ $journalEntry->is_manual ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                {{ $journalEntry->is_manual ? 'Manual' : 'System' }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="text-sm text-gray-600 mb-1">Entry Date</div>
                <div class="font-semibold">{{ \Carbon\Carbon::parse($journalEntry->entry_date)->format('l, d F Y') }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Created At</div>
                <div class="font-semibold">{{ $journalEntry->created_at->format('l, d F Y H:i:s') }}</div>
            </div>
            <div class="md:col-span-2">
                <div class="text-sm text-gray-600 mb-1">Description</div>
                <div class="font-semibold">{{ $journalEntry->description }}</div>
            </div>
        </div>
    </div>

    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-primary-900 mb-4">Journal Entry Lines</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700">Account Code</th>
                        <th class="px-4 py-3 text-left text-gray-700">Account Name</th>
                        <th class="px-4 py-3 text-left text-gray-700">Description</th>
                        <th class="px-4 py-3 text-right text-gray-700">Debit</th>
                        <th class="px-4 py-3 text-right text-gray-700">Credit</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($journalEntry->entries as $entry)
                    <tr>
                        <td class="px-4 py-3 text-gray-600">{{ optional($entry->accountModel)->account_code ?? 'N/A' }}</td>
                        <td class="px-4 py-3 font-medium">{{ optional($entry->accountModel)->name ?? $entry->account }}</td>
                        <td class="px-4 py-3">{{ $entry->description }}</td>
                        <td class="px-4 py-3 text-right font-bold text-blue-700">
                            @if($entry->type === 'debit')
                                TZS {{ number_format($entry->amount, 2) }}
                            @endif
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-green-700">
                            @if($entry->type === 'credit')
                                TZS {{ number_format($entry->amount, 2) }}
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="font-bold bg-gray-50">
                        <td colspan="3" class="px-4 py-3 text-right text-gray-900">Total</td>
                        <td class="px-4 py-3 text-right text-gray-900">TZS {{ number_format($journalEntry->total_debits, 2) }}</td>
                        <td class="px-4 py-3 text-right text-gray-900">TZS {{ number_format($journalEntry->total_credits, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
