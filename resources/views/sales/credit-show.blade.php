@extends('layouts.app')

@section('page-title', 'Credit Sale Details')

@section('content')
<div class="animate-[fadeIn_0.4s_ease] space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold text-primary-900">Credit Sale Details</h1>
        <a href="{{ route('sales.credit') }}" class="text-gray-500 hover:text-gray-700">
            <i class="fas fa-arrow-left mr-2"></i>Back
        </a>
    </div>

    <div class="card rounded-2xl p-6">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-sm text-gray-500">Invoice Number</p>
                <p class="text-lg font-semibold text-primary-900">{{ $sale->invoice_number }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Date</p>
                <p class="text-lg font-semibold text-primary-900">{{ $sale->created_at->format('M d, Y H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Customer</p>
                <p class="text-lg font-semibold text-primary-900">{{ $sale->customer->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500">Status</p>
                <span class="badge {{ $sale->total - $sale->paid == 0 ? 'badge-green' : 'badge-yellow' }}">
                    {{ $sale->total - $sale->paid == 0 ? 'Paid' : 'Pending' }}
                </span>
            </div>
        </div>

        <div class="overflow-x-auto mb-6">
            <h3 class="text-lg font-semibold text-primary-900 mb-4">Items</h3>
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Product</th>
                        <th class="text-left">Quantity</th>
                        <th class="text-left">Unit Price</th>
                        <th class="text-left">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sale->items as $item)
                    <tr>
                        <td class="text-gray-600">{{ $item->product->name ?? 'N/A' }}</td>
                        <td class="text-gray-600">{{ $item->quantity }}</td>
                        <td class="text-gray-600">TZS {{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-gray-600">TZS {{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="border-t pt-4">
            <div class="flex justify-between mb-2">
                <span class="text-gray-600">Subtotal</span>
                <span class="text-gray-900">TZS {{ number_format($sale->subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between mb-2">
                <span class="text-gray-600">Tax</span>
                <span class="text-gray-900">TZS {{ number_format($sale->tax, 2) }}</span>
            </div>
            <div class="flex justify-between mb-2">
                <span class="text-gray-600">Discount</span>
                <span class="text-gray-900">TZS {{ number_format($sale->discount, 2) }}</span>
            </div>
            <div class="flex justify-between mb-2 font-bold">
                <span class="text-primary-900">Total</span>
                <span class="text-primary-900">TZS {{ number_format($sale->total, 2) }}</span>
            </div>
            <div class="flex justify-between mb-2">
                <span class="text-gray-600">Paid</span>
                <span class="text-green-600">TZS {{ number_format($sale->paid, 2) }}</span>
            </div>
            <div class="flex justify-between font-bold">
                <span class="text-gray-600">Due</span>
                <span class="text-red-600">TZS {{ number_format($sale->total - $sale->paid, 2) }}</span>
            </div>
        </div>

        @if($payments->count() > 0)
        <div class="mt-8">
            <h3 class="text-lg font-semibold text-primary-900 mb-4">Payment History</h3>
            <div class="overflow-x-auto">
                <table class="data-table w-full">
                    <thead>
                        <tr>
                            <th class="text-left">Payment #</th>
                            <th class="text-left">Date</th>
                            <th class="text-left">Method</th>
                            <th class="text-left">Amount</th>
                            <th class="text-left">Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payments as $payment)
                        <tr>
                            <td class="text-gray-600">{{ $payment->payment_number }}</td>
                            <td class="text-gray-600">{{ $payment->created_at->format('M d, Y H:i') }}</td>
                            <td class="text-gray-600">{{ ucfirst($payment->payment_method) }}</td>
                            <td class="text-gray-600">TZS {{ number_format($payment->amount, 2) }}</td>
                            <td class="text-gray-600">{{ $payment->notes ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        <div class="flex gap-4 mt-8">
            @if($sale->total - $sale->paid > 0)
            <a href="{{ route('sales.credit.payment', $sale) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-money-bill-wave mr-2"></i>Add Payment
            </a>
            @endif
            <a href="{{ route('sales.credit.edit', $sale) }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
        </div>
    </div>
</div>
@endsection
