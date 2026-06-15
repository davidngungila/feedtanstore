@extends('layouts.app')

@section('page-title', 'Add Payment')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Add Payment</h2>
            <a href="{{ route('sales.credit') }}" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>

        <div class="mb-6 p-4 bg-primary-50 rounded-lg">
            <p class="text-primary-900 font-semibold">Invoice: {{ $sale->invoice_number }}</p>
            <p class="text-gray-600">Customer: {{ $sale->customer->name ?? '-' }}</p>
            <p class="text-gray-600">Total Amount: TZS {{ number_format($sale->total, 2) }}</p>
            <p class="text-gray-600">Paid So Far: TZS {{ number_format($sale->paid, 2) }}</p>
            <p class="text-red-600 font-semibold">Due Amount: TZS {{ number_format($sale->total - $sale->paid, 2) }}</p>
        </div>

        <form action="{{ route('sales.credit.payment.store', $sale) }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Amount</label>
                    <input type="number" name="amount" step="0.01" min="0.01" max="{{ $sale->total - $sale->paid }}" value="{{ $sale->total - $sale->paid }}" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                    @error('amount')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                    <select name="payment_method" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                        <option value="cash">Cash</option>
                        <option value="card">Card</option>
                        <option value="mobile">Mobile Money</option>
                    </select>
                </div>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Notes (Optional)</label>
                <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500"></textarea>
            </div>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-check mr-2"></i>Record Payment
            </button>
        </form>
    </div>
</div>
@endsection
