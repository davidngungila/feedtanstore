@extends('layouts.app')

@section('page-title', $payment->payment_number)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">{{ $payment->payment_number }}</h2>
            <div class="flex gap-3">
                <a href="{{ route('purchasing.payments.download', $payment) }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center gap-2">
                    <i class="fas fa-download"></i>
                    Download PDF
                </a>
                <a href="{{ route('purchasing.payments.edit', $payment) }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Edit
                </a>
                <a href="{{ route('purchasing.payments') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Back to Payments
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">Supplier</p>
                <p class="font-medium">{{ $payment->supplier->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Purchase Order</p>
                <p class="font-medium">{{ $payment->purchaseOrder->po_number ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Amount</p>
                <p class="font-semibold text-lg">TZS {{ number_format($payment->amount, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Payment Method</p>
                <p class="font-medium">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Transaction ID</p>
                <p class="font-medium">{{ $payment->transaction_id ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Payment Date</p>
                <p class="font-medium">{{ $payment->payment_date ? date('M d, Y', strtotime($payment->payment_date)) : '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Status</p>
                <span class="badge badge-green">{{ ucfirst($payment->status) }}</span>
            </div>
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500 mb-1">Notes</p>
                <p>{{ $payment->notes ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>
@endsection
