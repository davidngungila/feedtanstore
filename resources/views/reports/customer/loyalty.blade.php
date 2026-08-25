@extends('layouts.app')

@section('page-title', 'Loyalty Report')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class='text-xl font-bold text-primary-900'>Customer Loyalty Points</h2>
            <a href="{{ route('reports.customer.loyalty.download') }}" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">Export PDF</a>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-xl p-5">
                <p class="text-sm text-primary-700 mb-1">Customers with Points</p>
                <h3 class="text-2xl font-bold text-primary-900">{{ number_format($customers->count()) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5">
                <p class="text-sm text-green-700 mb-1">Total Points Earned</p>
                <h3 class="text-2xl font-bold text-green-900">{{ number_format($customers->sum('points_earned')) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5">
                <p class="text-sm text-purple-700 mb-1">Outstanding Balance</p>
                <h3 class="text-2xl font-bold text-purple-900">{{ number_format($customers->sum('loyalty_balance')) }}</h3>
            </div>
        </div>

        @if($customers->isEmpty())
        <div class="text-center py-10 text-gray-500">
            <i class="fas fa-star text-4xl mb-3"></i>
            <p>No loyalty points recorded yet.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Customer</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Phone</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Points Earned</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Points Redeemed</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Balance</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($customers as $customer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $customer->name }}</td>
                        <td class="px-4 py-3">{{ $customer->phone ?? '-' }}</td>
                        <td class="px-4 py-3 text-right text-green-700">+{{ number_format($customer->points_earned) }}</td>
                        <td class="px-4 py-3 text-right text-red-700">-{{ number_format($customer->points_redeemed) }}</td>
                        <td class="px-4 py-3 text-right font-bold {{ $customer->loyalty_balance >= 0 ? 'text-primary-900' : 'text-red-700' }}">{{ number_format($customer->loyalty_balance) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
