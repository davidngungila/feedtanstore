@extends('layouts.app')

@section('page-title', 'General Ledger')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">General Ledger</h2>
        </div>

        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div>
                <label class="form-label">Date From</label>
                <input type="date" name="date_from" value="{{ $dateFrom ?? '' }}" class="form-input">
            </div>
            <div>
                <label class="form-label">Date To</label>
                <input type="date" name="date_to" value="{{ $dateTo ?? '' }}" class="form-input">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                    Filter
                </button>
                <a href="{{ route('finance.general-ledger') }}" class="text-gray-600 hover:text-gray-800 px-4 py-2 rounded-lg border border-gray-300 transition-colors">
                    Clear
                </a>
            </div>
        </form>

        @foreach($accounts as $account)
            <div class="mb-8 pb-6 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-primary-900 mb-3">{{ $account->account_code }} - {{ $account->name }} <span class="text-sm text-gray-500">({{ $account->type }})</span></h3>
                <div class="overflow-x-auto">
                    <table class="data-table w-full">
                        <thead>
                            <tr>
                                <th class="text-left">Date</th>
                                <th class="text-left">Reference</th>
                                <th class="text-left">Description</th>
                                <th class="text-right">Debit</th>
                                <th class="text-right">Credit</th>
                                <th class="text-right">Balance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $balance = 0;
                                $entries = $account->accountingEntries->sortBy('created_at');
                            @endphp
                            @foreach($entries as $entry)
                                @php
                                    if(in_array($account->type, ['Asset', 'Expense'])) {
                                        $balance += $entry->type === 'debit' ? $entry->amount : -$entry->amount;
                                    } else {
                                        $balance += $entry->type === 'credit' ? $entry->amount : -$entry->amount;
                                    }
                                @endphp
                            <tr>
                                <td class="text-gray-600">{{ $entry->created_at->format('M d, Y') }}</td>
                                <td class="text-primary-600">{{ $entry->reference_number }}</td>
                                <td class="text-gray-600">{{ $entry->description }}</td>
                                <td class="text-right text-gray-800">{{ $entry->type === 'debit' ? number_format($entry->amount, 2) : '' }}</td>
                                <td class="text-right text-gray-800">{{ $entry->type === 'credit' ? number_format($entry->amount, 2) : '' }}</td>
                                <td class="text-right text-gray-800">{{ number_format($balance, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
