@extends('layouts.app')

@section('page-title', $customer->name)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">{{ $customer->name }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('customers.edit', $customer) }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg">Edit</a>
                <a href="{{ route('customers.list') }}" class="px-4 py-2 border border-gray-300 rounded-lg">Back</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
            <div>
                <p class="text-sm text-gray-500">Email</p>
                <p class="font-medium">{{ $customer->email ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Phone</p>
                <p class="font-medium">{{ $customer->phone ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Customer Group</p>
                <p class="font-medium">{{ $customer->group->name ?? '-' }} @if($customer->group)({{ $customer->group->discount_percentage }}% off)@endif</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Credit Limit</p>
                <p class="font-medium">TZS {{ number_format($customer->credit_limit, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Balance</p>
                <p class="font-bold text-lg {{ $customer->balance > 0 ? 'text-red-600' : 'text-green-600' }}">TZS {{ number_format($customer->balance, 2) }}</p>
            </div>
        </div>

        @if($customer->address)
            <div class="mb-4">
                <p class="text-sm text-gray-500 mb-1">Address</p>
                <p>{{ $customer->address }}</p>
            </div>
        @endif
    </div>

    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-primary-900 mb-4">Sales History</h3>
        @if($customer->sales->count() > 0)
            <div class="overflow-x-auto">
                <table class="data-table w-full">
                    <thead>
                        <tr>
                            <th class="text-left">Invoice #</th>
                            <th class="text-left">Date</th>
                            <th class="text-left">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($customer->sales as $sale)
                            <tr>
                                <td class="text-primary-600 font-medium">
                                    <a href="{{ route('sales.show', $sale) }}">{{ $sale->invoice_number }}</a>
                                </td>
                                <td class="text-gray-600">{{ $sale->created_at->format('M d, Y') }}</td>
                                <td class="text-gray-600">TZS {{ number_format($sale->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-center text-gray-500 py-8">No sales yet for this customer.</p>
        @endif
    </div>
</div>
@endsection
