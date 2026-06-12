@extends('layouts.app')

@section('page-title', 'Customer History')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Customer History</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Customer</th>
                        <th class="text-left">Total Sales</th>
                        <th class="text-left">Total Spent</th>
                        <th class="text-left">Phone</th>
                        <th class="text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($customers as $customer)
                    <tr>
                        <td class="font-medium text-primary-900">
                            <a href="{{ route('customers.show', $customer) }}">{{ $customer->name }}</a>
                        </td>
                        <td class="text-gray-600">{{ $customer->sales->count() }}</td>
                        <td class="text-gray-600">TZS {{ number_format($customer->sales->sum('total'), 2) }}</td>
                        <td class="text-gray-600">{{ $customer->phone ?? '-' }}</td>
                        <td class="flex items-center gap-2">
                            <a href="{{ route('customers.show', $customer) }}" class="text-primary-600 hover:text-primary-800" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection