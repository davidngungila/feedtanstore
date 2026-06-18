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

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @forelse($budgets as $budget)
                @php
                    $totalSpent = $budget->expenses->sum('amount');
                    $percentage = $budget->amount > 0 ? min(($totalSpent / $budget->amount) * 100, 100) : 0;
                    $color = $percentage > 100 ? 'red' : ($percentage > 80 ? 'yellow' : 'blue');
                @endphp
                <a href="{{ route('finance.budgets.show', $budget) }}" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                    <div class="flex items-center justify-between mb-2">
                        <h3 class="font-semibold">{{ $budget->name }}</h3>
                        @if($budget->is_active)
                            <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded-full">Active</span>
                        @else
                            <span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded-full">Inactive</span>
                        @endif
                    </div>
                    <p class="text-3xl font-bold text-primary-900 mb-2">TZS {{ number_format($budget->amount, 2) }}</p>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full" style="width: {{ $percentage }}%; background-color: {{ $color === 'red' ? '#dc2626' : ($color === 'yellow' ? '#f59e0b' : '#2563eb') }}"></div>
                    </div>
                    <p class="text-sm text-gray-600 mt-2">{{ number_format($percentage, 1) }}% spent (TZS {{ number_format($totalSpent, 2) }})</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $budget->start_date->format('d/m/Y') }} - {{ $budget->end_date->format('d/m/Y') }}</p>
                </a>
            @empty
                <div class="col-span-1 md:col-span-3 p-8 text-center text-gray-500">
                    <i class="fas fa-wallet text-4xl mb-3 text-gray-300"></i>
                    <p>No budgets yet. <a href="{{ route('finance.budgets.create') }}" class="text-primary-600 hover:underline">Create your first budget!</a></p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection