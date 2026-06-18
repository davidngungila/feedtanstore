@extends('layouts.app')

@section('page-title', 'Finance Dashboard')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-600">Total Income</h3>
                <i class="fas fa-arrow-up text-green-600 text-2xl"></i>
            </div>
            <p class="text-3xl font-bold text-gray-800">TZS 0.00</p>
        </div>
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-600">Total Expenses</h3>
                <i class="fas fa-arrow-down text-red-600 text-2xl"></i>
            </div>
            <p class="text-3xl font-bold text-gray-800">TZS 0.00</p>
        </div>
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-600">Accounts Receivable</h3>
                <i class="fas fa-receipt text-blue-600 text-2xl"></i>
            </div>
            <p class="text-3xl font-bold text-gray-800">TZS 0.00</p>
        </div>
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-sm font-medium text-gray-600">Accounts Payable</h3>
                <i class="fas fa-credit-card text-orange-600 text-2xl"></i>
            </div>
            <p class="text-3xl font-bold text-gray-800">TZS 0.00</p>
        </div>
    </div>
    <div class="card rounded-2xl p-6">
        <h2 class="text-xl font-bold text-primary-900 mb-4">Welcome to Finance Dashboard</h2>
        <p class="text-gray-600 mb-4">This is a placeholder for your finance dashboard. More features coming soon!</p>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="{{ route('finance.income') }}" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-dollar-sign text-blue-500 mb-2"></i>
                <h4 class="font-semibold">Manage Income</h4>
            </a>
            <a href="{{ route('finance.expenses') }}" class="p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors">
                <i class="fas fa-file-invoice-dollar text-red-500 mb-2"></i>
                <h4 class="font-semibold">Manage Expenses</h4>
            </a>
        </div>
    </div>
</div>
@endsection
