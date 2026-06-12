@extends('layouts.app')

@section('page-title', 'Payments')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <h2 class="text-xl font-bold text-primary-900 mb-6">Payments</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <a href="{{ route('customers.credit') }}" class="block border rounded-lg p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-3 bg-blue-100 rounded-lg">
                        <i class="fas fa-user-tie text-blue-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-900">Customer Payments</h3>
                </div>
                <p class="text-sm text-gray-600">Manage customer credit and payments</p>
            </a>
            <a href="{{ route('purchasing.payments') }}" class="block border rounded-lg p-6 hover:shadow-lg transition-shadow">
                <div class="flex items-center gap-3 mb-2">
                    <div class="p-3 bg-orange-100 rounded-lg">
                        <i class="fas fa-truck-loading text-orange-600 text-xl"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-primary-900">Supplier Payments</h3>
                </div>
                <p class="text-sm text-gray-600">Manage supplier payments and purchases</p>
            </a>
        </div>
    </div>
</div>
@endsection