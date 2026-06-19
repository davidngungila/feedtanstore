@extends('layouts.app')

@section('page-title', 'Journal Entries')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Journal Entries</h2>
            <a href="{{ route('finance.journal-entries.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>New Journal Entry
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-800 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Entry Number</th>
                        <th class="text-left">Date</th>
                        <th class="text-left">Description</th>
                        <th class="text-left">Status</th>
                        <th class="text-left">Posted By</th>
                        <th class="text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($journalEntries as $entry)
                    <tr>
                        <td class="font-medium text-primary-900">
                            <a href="{{ route('finance.journal-entries.show', $entry) }}" class="hover:underline">{{ $entry->entry_number }}</a>
                        </td>
                        <td class="text-gray-600">{{ $entry->entry_date->format('M d, Y') }}</td>
                        <td class="text-gray-600">{{ Str::limit($entry->description, 50) }}</td>
                        <td>
                            <span class="badge {{ $entry->is_posted ? 'badge-green' : 'badge-yellow' }}">
                                {{ $entry->is_posted ? 'Posted' : 'Draft' }}
                            </span>
                        </td>
                        <td class="text-gray-600">{{ $entry->postedBy->name ?? '-' }}</td>
                        <td class="flex items-center gap-2">
                            <a href="{{ route('finance.journal-entries.show', $entry) }}" class="text-primary-600 hover:text-primary-800 p-1" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
