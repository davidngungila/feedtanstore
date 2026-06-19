@extends('layouts.app')

@section('page-title', 'General Ledger')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <h2 class="text-xl font-bold text-primary-900 mb-6">General Ledger</h2>

        <div class="mb-6">
            <form method="GET" action="{{ route('finance.general-ledger') }}" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Account</label>
                    <select name="account_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">-- Choose an Account --</option>
                        @foreach($accounts as $account)
                            <option value="{{ $account->id }}" {{ $selectedAccountId == $account->id ? 'selected' : '' }}>
                                {{ $account->account_code }} - {{ $account->name }} ({{ $account->type }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Start Date</label>
                    <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">End Date</label>
                    <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="flex items-end">
                    <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                        View Ledger
                    </button>
                </div>
            </form>
        </div>

        @if(isset($ledgerEntries))
            <div class="border-t border-gray-200 pt-6">
                <div class="mb-6">
                    <h3 class="text-lg font-bold text-gray-900 mb-2">{{ $account->account_code }} - {{ $account->name }}</h3>
                    <p class="text-gray-600">{{ $account->type }}</p>
                    <div class="mt-4 inline-block px-6 py-3 bg-gray-50 rounded-lg">
                        <p class="text-sm text-gray-600 mb-1">Current Balance</p>
                        <p class="text-2xl font-bold text-gray-900">TZS {{ number_format($balance, 2) }}</p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-gray-700">Date</th>
                                <th class="px-4 py-3 text-left text-gray-700">Journal #</th>
                                <th class="px-4 py-3 text-left text-gray-700">Description</th>
                                <th class="px-4 py-3 text-right text-gray-700">Debit</th>
                                <th class="px-4 py-3 text-right text-gray-700">Credit</th>
                                <th class="px-4 py-3 text-right text-gray-700">Balance</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @foreach($ledgerEntries as $item)
                            <tr>
                                <td class="px-4 py-3">{{ $item['entry']->created_at->format('d/m/Y') }}</td>
                                <td class="px-4 py-3">
                                    @if($item['entry']->journalEntry)
                                        <a href="{{ route('finance.journal-entries.show', $item['entry']->journalEntry) }}" class="text-primary-600 hover:text-primary-800 font-medium">
                                            {{ $item['entry']->journalEntry->journal_number }}
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td class="px-4 py-3">{{ $item['entry']->description }}</td>
                                <td class="px-4 py-3 text-right font-bold text-blue-700">
                                    @if($item['entry']->type === 'debit')
                                        TZS {{ number_format($item['entry']->amount, 2) }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-green-700">
                                    @if($item['entry']->type === 'credit')
                                        TZS {{ number_format($item['entry']->amount, 2) }}
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right font-bold text-gray-900">TZS {{ number_format($item['balance'], 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
