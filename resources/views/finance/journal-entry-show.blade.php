@extends('layouts.app')

@section('page-title', 'Journal Entry: ' . $journalEntry->entry_number)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Journal Entry: {{ $journalEntry->entry_number }}</h2>
            <div class="flex items-center gap-4">
                @if(!$journalEntry->is_posted)
                <form action="{{ route('finance.journal-entries.post', $journalEntry) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors" onclick="return confirm('Are you sure you want to post this journal entry?')">
                        <i class="fas fa-check mr-2"></i>Post
                    </button>
                </form>
                @endif
                <a href="{{ route('finance.journal-entries') }}" class="text-gray-600 hover:text-gray-800 px-4 py-2 rounded-lg border border-gray-300 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div>
                <span class="text-gray-600 text-sm">Entry Number</span>
                <p class="font-semibold text-primary-900">{{ $journalEntry->entry_number }}</p>
            </div>
            <div>
                <span class="text-gray-600 text-sm">Entry Date</span>
                <p class="font-semibold text-primary-900">{{ $journalEntry->entry_date->format('F d, Y') }}</p>
            </div>
            <div>
                <span class="text-gray-600 text-sm">Status</span>
                <p>
                    <span class="badge {{ $journalEntry->is_posted ? 'badge-green' : 'badge-yellow' }}">
                        {{ $journalEntry->is_posted ? 'Posted' : 'Draft' }}
                    </span>
                </p>
            </div>
            <div>
                <span class="text-gray-600 text-sm">Posted By</span>
                <p class="font-semibold text-primary-900">{{ $journalEntry->postedBy->name ?? '-' }}</p>
            </div>
        </div>
        <div class="mb-6">
            <span class="text-gray-600 text-sm">Description</span>
            <p class="text-gray-800">{{ $journalEntry->description }}</p>
        </div>
    </div>

    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-semibold text-primary-900 mb-4">Line Items</h3>
        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Account</th>
                        <th class="text-left">Description</th>
                        <th class="text-right">Debit</th>
                        <th class="text-right">Credit</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($journalEntry->items as $item)
                    <tr>
                        <td class="text-gray-800">{{ $item->account->account_code }} - {{ $item->account->name }}</td>
                        <td class="text-gray-600">{{ $item->description ?? '-' }}</td>
                        <td class="text-right text-gray-800">{{ $item->type === 'debit' ? number_format($item->amount, 2) : '' }}</td>
                        <td class="text-right text-gray-800">{{ $item->type === 'credit' ? number_format($item->amount, 2) : '' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray-50 font-bold">
                        <td colspan="2" class="text-primary-900">Totals</td>
                        <td class="text-right text-primary-900">
                            {{ number_format($journalEntry->items->where('type', 'debit')->sum('amount'), 2) }}
                        </td>
                        <td class="text-right text-primary-900">
                            {{ number_format($journalEntry->items->where('type', 'credit')->sum('amount'), 2) }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
