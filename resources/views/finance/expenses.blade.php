@extends('layouts.app')

@section('page-title', 'Expenses')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Expenses</h2>
            <a href="{{ route('finance.expenses.create') }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Add Expense
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
                        <th class="px-4 py-3 text-left text-gray-700">Date</th>
                        <th class="px-4 py-3 text-left text-gray-700">Reference No</th>
                        <th class="px-4 py-3 text-left text-gray-700">Category</th>
                        <th class="px-4 py-3 text-left text-gray-700">Description</th>
                        <th class="px-4 py-3 text-left text-gray-700">Amount</th>
                        <th class="px-4 py-3 text-left text-gray-700">Payment Method</th>
                        <th class="px-4 py-3 text-left text-gray-700">Added By</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($expenses as $expense)
                    <tr>
                        <td class="px-4 py-3">{{ $expense->date->format('d/m/Y') }}</td>
                        <td class="px-4 py-3 font-semibold">{{ $expense->reference_number }}</td>
                        <td class="px-4 py-3">{{ $expense->category }}</td>
                        <td class="px-4 py-3">{{ $expense->description ?? 'N/A' }}</td>
                        <td class="px-4 py-3 font-bold text-red-700">TZS {{ number_format($expense->amount, 2) }}</td>
                        <td class="px-4 py-3">{{ $expense->payment_method }}</td>
                        <td class="px-4 py-3">{{ $expense->user->name }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">No expenses found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection