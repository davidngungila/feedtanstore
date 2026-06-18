@extends('layouts.app')

@section('page-title', 'Budget Details')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Budget Details</h2>
            <div class="flex gap-2">
                <a href="{{ route('finance.budgets.edit', $budget) }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('finance.budgets') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Budgets
                </a>
            </div>
        </div>

        <div class="mb-8">
            <h3 class="text-2xl font-bold text-primary-900 mb-2">{{ $budget->name }}</h3>
            @if($budget->category)
            <p class="text-sm text-gray-500 mb-2">{{ $budget->category }}</p>
            @endif
            @if($budget->description)
            <p class="text-gray-600 mb-4">{{ $budget->description }}</p>
            @endif
            <p class="text-gray-600 mb-4">
                <i class="fas fa-calendar mr-2"></i>
                {{ $budget->start_date->format('M d, Y') }} - {{ $budget->end_date->format('M d, Y') }}
            </p>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div class="p-4 bg-gray-50 rounded-lg">
                    <p class="text-sm text-gray-600">Total Budget</p>
                    <p class="text-2xl font-bold text-primary-900">{{ number_format($budget->total_amount, 2) }}</p>
                </div>
                <div class="p-4 bg-red-50 rounded-lg">
                    <p class="text-sm text-gray-600">Spent</p>
                    <p class="text-2xl font-bold text-red-600">{{ number_format($budget->spent_amount, 2) }}</p>
                </div>
                <div class="p-4 bg-green-50 rounded-lg">
                    <p class="text-sm text-gray-600">Remaining</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($budget->remaining_amount, 2) }}</p>
                </div>
            </div>
            <div class="mb-4">
                <p class="text-sm text-gray-600 mb-2">Utilization: {{ $budget->utilization_percentage }}%</p>
                <div class="w-full bg-gray-200 rounded-full h-3">
                    @php
                    $percent = $budget->utilization_percentage;
                    $color = $percent > 90 ? 'bg-red-600' : ($percent > 70 ? 'bg-yellow-600' : 'bg-green-600');
                    @endphp
                    <div class="{{ $color }} h-3 rounded-full transition-all" style="width: {{ $percent }}%"></div>
                </div>
            </div>
        </div>

        <div>
            <h4 class="text-lg font-semibold text-primary-900 mb-4">Expenses</h4>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Category</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Description</th>
                            <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Amount</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($budget->expenses as $expense)
                        <tr>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $expense->date->format('M d, Y') }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $expense->category }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900">{{ $expense->description ?? '-' }}</td>
                            <td class="px-4 py-3 text-sm text-gray-900 text-right">{{ number_format($expense->amount, 2) }}</td>
                            <td class="px-4 py-3 text-center text-sm">
                                <a href="{{ route('finance.expenses.show', $expense) }}" class="text-primary-600 hover:text-primary-800">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @if($budget->expenses->isEmpty())
                <p class="text-center text-gray-500 py-8">No expenses for this budget yet</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
