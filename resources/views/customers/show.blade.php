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
                <p class="text-sm text-gray-500">Loyalty Points</p>
                <p class="font-medium text-blue-600">{{ $customer->total_loyalty_points }} points</p>
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

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <div class="card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-primary-900 mb-4">Add Payment</h3>
            <form action="{{ route('customers.add-payment', $customer) }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                        <input type="number" name="amount" required min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                        <select name="payment_method" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option>Cash</option>
                            <option>Bank Transfer</option>
                            <option>Mobile Money</option>
                            <option>Credit Card</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                    </div>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">Add Payment</button>
            </form>
        </div>

        <div class="card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-primary-900 mb-4">Loyalty Points</h3>
            <form action="{{ route('customers.add-loyalty', $customer) }}" method="POST">
                @csrf
                <div class="grid grid-cols-1 gap-4 mb-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Points</label>
                        <input type="number" name="points" required min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select name="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="earned">Earn</option>
                            <option value="redeemed">Redeem</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg"></textarea>
                    </div>
                </div>
                <button type="submit" class="w-full px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">Update Points</button>
            </form>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-primary-900 mb-4">Payments History</h3>
            @if($customer->payments->count() > 0)
                <div class="space-y-3">
                    @foreach($customer->payments as $payment)
                        <div class="p-3 bg-gray-50 rounded-lg">
                            <div class="flex items-center justify-between mb-1">
                                <p class="font-semibold text-primary-900">{{ $payment->payment_number }}</p>
                                <p class="text-green-600 font-medium">TZS {{ number_format($payment->amount, 2) }}</p>
                            </div>
                            <p class="text-sm text-gray-500">{{ $payment->payment_method }} - {{ $payment->created_at->format('M d, Y H:i') }}</p>
                            @if($payment->notes)
                                <p class="text-xs text-gray-400 mt-1">{{ $payment->notes }}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-center text-gray-500 py-8">No payments yet.</p>
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
</div>
@endsection
