@extends('layouts.app')

@section('page-title', 'Supplier Payments')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Supplier Payments</h2>
            <a href="{{ route('purchasing.payments.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>New Payment
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Payment Number</th>
                        <th class="text-left">Supplier</th>
                        <th class="text-left">Amount</th>
                        <th class="text-left">Method</th>
                        <th class="text-left">Transaction ID</th>
                        <th class="text-left">Date</th>
                        <th class="text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payments as $payment)
                    <tr>
                        <td class="font-medium text-primary-900">
                            <a href="{{ route('purchasing.payments.show', $payment) }}" class="hover:underline">{{ $payment->payment_number }}</a>
                        </td>
                        <td class="text-gray-600">{{ $payment->supplier->name ?? 'N/A' }}</td>
                        <td class="text-gray-600">TZS {{ number_format($payment->amount, 2) }}</td>
                        <td class="text-gray-600">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                        <td class="text-gray-600">{{ $payment->transaction_id ?? '-' }}</td>
                        <td class="text-gray-600">{{ $payment->payment_date ? date('M d, Y', strtotime($payment->payment_date)) : '-' }}</td>
                        <td class="flex items-center gap-2">
                            <a href="{{ route('purchasing.payments.show', $payment) }}" class="text-primary-600 hover:text-primary-800 p-1" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('purchasing.payments.edit', $payment) }}" class="text-primary-600 hover:text-primary-800 p-1" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('purchasing.payments.destroy', $payment) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this payment?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 p-1" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
