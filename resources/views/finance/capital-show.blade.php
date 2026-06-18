@extends('layouts.app')

@section('page-title', 'Capital Details')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Capital Details</h2>
            <a href="{{ route('finance.capital') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <div class="text-sm text-gray-600 mb-1">Transaction Type</div>
                <span class="px-2 py-1 rounded-full text-xs font-bold {{ $capital->transaction_type === 'add' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ ucfirst($capital->transaction_type) }}
                </span>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Date</div>
                <div class="font-semibold">{{ $capital->date->format('l, d F Y') }}</div>
            </div>
            <div class="md:col-span-2">
                <div class="text-sm text-gray-600 mb-1">Amount</div>
                <div class="text-2xl font-bold {{ $capital->transaction_type === 'add' ? 'text-green-700' : 'text-red-700' }}">
                    TZS {{ number_format($capital->amount, 2) }}
                </div>
            </div>
            <div class="md:col-span-2">
                <div class="text-sm text-gray-600 mb-1">Description</div>
                <div class="font-semibold">{{ $capital->description ?? 'N/A' }}</div>
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('finance.capital.edit', $capital) }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
        </div>
    </div>
</div>
@endsection
