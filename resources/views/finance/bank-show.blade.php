@extends('layouts.app')

@section('page-title', 'Bank Account Details')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Bank Account Details</h2>
            <a href="{{ route('finance.bank') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Bank Accounts
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <div class="text-sm text-gray-600 mb-1">Account Name</div>
                <div class="font-semibold text-lg">{{ $bankAccount->name }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Account Number</div>
                <div class="font-semibold">{{ $bankAccount->account_number }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Bank Name</div>
                <div class="font-semibold">{{ $bankAccount->bank_name }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Branch</div>
                <div class="font-semibold">{{ $bankAccount->branch ?? 'N/A' }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Current Balance</div>
                <div class="font-bold text-green-700 text-2xl">TZS {{ number_format($bankAccount->balance, 2) }}</div>
            </div>
            <div>
                <div class="text-sm text-gray-600 mb-1">Status</div>
                <span class="px-2 py-1 rounded-full text-sm font-semibold 
                    @if($bankAccount->is_active) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                    {{ $bankAccount->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
        </div>

        <div class="mt-8 flex gap-3">
            <a href="{{ route('finance.bank.edit', $bankAccount) }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                <i class="fas fa-edit mr-2"></i>Edit Account
            </a>
        </div>
    </div>
</div>
@endsection
