@extends('layouts.app')

@section('page-title', 'Capital Management')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Capital Management</h2>
            <a href="{{ route('finance.capital.create') }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Add Capital
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-600">Date</th>
                        <th class="px-4 py-3 text-left text-gray-600">Transaction Type</th>
                        <th class="px-4 py-3 text-left text-gray-600">Amount</th>
                        <th class="px-4 py-3 text-left text-gray-600">Description</th>
                        <th class="px-4 py-3 text-left text-gray-600">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($capitals as $capital)
                        <tr>
                            <td class="px-4 py-3">{{ $capital->date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-1 rounded-full text-xs font-bold {{ $capital->transaction_type === 'add' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ ucfirst($capital->transaction_type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-bold {{ $capital->transaction_type === 'add' ? 'text-green-700' : 'text-red-700' }}">
                                TZS {{ number_format($capital->amount, 2) }}
                            </td>
                            <td class="px-4 py-3">{{ $capital->description ?? 'N/A' }}</td>
                            <td class="px-4 py-3 flex items-center gap-2">
                                <a href="{{ route('finance.capital.show', $capital) }}" class="text-blue-600 hover:text-blue-800" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('finance.capital.edit', $capital) }}" class="text-primary-600 hover:text-primary-800" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('finance.capital.destroy', $capital) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this transaction?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">No capital transactions yet!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
