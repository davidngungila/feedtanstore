@extends('layouts.app')

@section('page-title', 'Journal Entries')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex flex-col md:flex-row items-center justify-between mb-6 gap-4">
            <h2 class="text-xl font-bold text-primary-900">Journal Entries</h2>
            <a href="{{ route('finance.journal-entries.create') }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                New Journal Entry
            </a>
        </div>

        <form method="GET" action="{{ route('finance.journal-entries') }}" class="flex flex-col md:flex-row gap-4 mb-6">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate ?? '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div class="flex items-end">
                <button type="submit" class="px-6 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg transition-colors">
                    Filter
                </button>
            </div>
            @if($startDate || $endDate)
            <div class="flex items-end">
                <a href="{{ route('finance.journal-entries') }}" class="px-6 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg transition-colors">
                    Clear
                </a>
            </div>
            @endif
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700">Journal #</th>
                        <th class="px-4 py-3 text-left text-gray-700">Date</th>
                        <th class="px-4 py-3 text-left text-gray-700">Description</th>
                        <th class="px-4 py-3 text-left text-gray-700">Type</th>
                        <th class="px-4 py-3 text-right text-gray-700">Debits</th>
                        <th class="px-4 py-3 text-right text-gray-700">Credits</th>
                        <th class="px-4 py-3 text-center text-gray-700">Status</th>
                        <th class="px-4 py-3 text-center text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($journalEntries as $journalEntry)
                    <tr>
                        <td class="px-4 py-3">
                            <a href="{{ route('finance.journal-entries.show', $journalEntry) }}" class="text-primary-600 hover:text-primary-800 font-semibold">
                                {{ $journalEntry->journal_number }}
                            </a>
                        </td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($journalEntry->entry_date)->format('d/m/Y') }}</td>
                        <td class="px-4 py-3">{{ Str::limit($journalEntry->description, 50) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-bold {{ $journalEntry->is_manual ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800' }}">
                                {{ $journalEntry->is_manual ? 'Manual' : 'System' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-bold">TZS {{ number_format($journalEntry->total_debits, 2) }}</td>
                        <td class="px-4 py-3 text-right font-bold">TZS {{ number_format($journalEntry->total_credits, 2) }}</td>
                        <td class="px-4 py-3 text-center">
                            <span class="px-2 py-1 rounded-full text-xs font-bold {{ $journalEntry->is_balanced ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $journalEntry->is_balanced ? 'Balanced' : 'Unbalanced' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a href="{{ route('finance.journal-entries.show', $journalEntry) }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                                View
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">No journal entries found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($journalEntries->hasPages())
        <div class="mt-6">
            {{ $journalEntries->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
