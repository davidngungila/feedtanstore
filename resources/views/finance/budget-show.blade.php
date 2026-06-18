@extends('layouts.app')

@section('page-title', $budget->name)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">{{ $budget->name }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('finance.budgets.edit', $budget) }}" class="px-3 py-1 bg-primary-100 text-primary-700 rounded-lg hover:bg-primary-200 transition-colors">
                    <i class="fas fa-edit mr-1"></i>Edit
                </a>
                <a href="{{ route('finance.budgets') }}" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i>Back
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Budget</p>
                <p class="font-bold text-primary-900 text-2xl">TZS {{ number_format($budget->amount, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Total Spent</p>
                @php
                    $totalSpent = $budget->expenses->sum('amount');
                    $percentage = $budget->amount > 0 ? min(($totalSpent / $budget->amount) * 100, 100) : 0;
                @endphp
                <p class="font-bold text-red-700 text-2xl">TZS {{ number_format($totalSpent, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Remaining</p>
                <p class="font-bold text-green-700 text-2xl">TZS {{ number_format($budget->amount - $totalSpent, 2) }}</p>
            </div>
        </div>

        <div class="mb-6">
            <div class="flex justify-between text-sm mb-2">
                <span class="text-gray-600">Spent</span>
                <span class="font-bold text-gray-800">{{ number_format($percentage, 2) }}%</span>
            </div>
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="h-3 rounded-full transition-all" style="width: {{ $percentage }}%; background-color: {{ $percentage > 100 ? '#dc2626' : ($percentage > 80 ? '#f59e0b' : '#10b981') }}"></div>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-600 mb-1">Start Date</p>
                <p class="font-medium">{{ $budget->start_date->format('d/m/Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">End Date</p>
                <p class="font-medium">{{ $budget->end_date->format('d/m/Y') }}</p>
            </div>
            @if($budget->description)
                <div class="md:col-span-2">
                    <p class="text-sm text-gray-600 mb-1">Description</p>
                    <p class="font-medium">{{ $budget->description }}</p>
                </div>
            @endif
        </div>
    </div>

    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-primary-900 mb-4">Expenses in this Budget</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700">Date</th>
                        <th class="px-4 py-3 text-left text-gray-700">Reference No</th>
                        <th class="px-4 py-3 text-left text-gray-700">Category</th>
                        <th class="px-4 py-3 text-left text-gray-700">Description</th>
                        <th class="px-4 py-3 text-left text-gray-700">Amount</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($budget->expenses as $expense)
                        <tr>
                            <td class="px-4 py-3">{{ $expense->date->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 font-semibold">{{ $expense->reference_number }}</td>
                            <td class="px-4 py-3">{{ $expense->category }}</td>
                            <td class="px-4 py-3">{{ $expense->description ?? 'N/A' }}</td>
                            <td class="px-4 py-3 font-bold text-red-700">TZS {{ number_format($expense->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">No expenses in this budget yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
