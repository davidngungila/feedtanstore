@extends('layouts.app')

@section('page-title', 'Budgets')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Budgets</h2>
            <a href="{{ route('finance.budgets.create') }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Add Budget
            </a>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($budgets as $budget)
            <div class="p-4 border border-gray-200 rounded-lg hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="font-semibold text-lg">{{ $budget->name }}</h3>
                    <div class="flex gap-2">
                        <a href="{{ route('finance.budgets.show', $budget) }}" class="text-primary-600 hover:text-primary-800">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('finance.budgets.edit', $budget) }}" class="text-primary-600 hover:text-primary-800">
                            <i class="fas fa-edit"></i>
                        </a>
                        <form action="{{ route('finance.budgets.destroy', $budget) }}" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete this budget?')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
                @if($budget->category)
                <p class="text-sm text-gray-500 mb-2">{{ $budget->category }}</p>
                @endif
                @if($budget->description)
                <p class="text-sm text-gray-600 mb-3">{{ $budget->description }}</p>
                @endif
                <p class="text-3xl font-bold text-primary-900 mb-2">{{ number_format($budget->total_amount, 2) }}</p>
                <p class="text-sm text-gray-600 mb-2">
                    {{ $budget->start_date->format('M d, Y') }} - {{ $budget->end_date->format('M d, Y') }}
                </p>
                <div class="w-full bg-gray-200 rounded-full h-2 mb-2">
                    @php
                    $percent = $budget->utilization_percentage;
                    $color = $percent > 90 ? 'bg-red-600' : ($percent > 70 ? 'bg-yellow-600' : 'bg-green-600');
                    @endphp
                    <div class="{{ $color }} h-2 rounded-full transition-all" style="width: {{ $percent }}%"></div>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Spent: {{ number_format($budget->spent_amount, 2) }}</span>
                    <span class="text-gray-600">Remaining: {{ number_format($budget->remaining_amount, 2) }}</span>
                </div>
                <p class="text-sm text-gray-600 mt-1">{{ $percent }}% utilized</p>
            </div>
            @endforeach
        </div>
        
        @if($budgets->isEmpty())
        <div class="text-center py-12">
            <i class="fas fa-wallet text-6xl text-gray-300 mb-4"></i>
            <h3 class="text-lg font-medium text-gray-700 mb-2">No budgets yet</h3>
            <p class="text-gray-500 mb-4">Create your first budget to get started</p>
            <a href="{{ route('finance.budgets.create') }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>Add Budget
            </a>
        </div>
        @endif
    </div>
</div>
@endsection