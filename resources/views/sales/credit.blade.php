@extends('layouts.app')

@section('page-title', 'Credit Sales')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <h2 class="text-xl font-bold text-primary-900 mb-6">Credit Sales</h2>

        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Invoice #</th>
                        <th class="text-left">Customer</th>
                        <th class="text-left">Date</th>
                        <th class="text-left">Total</th>
                        <th class="text-left">Paid</th>
                        <th class="text-left">Due</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
                    <tr>
                        <td class="font-medium text-primary-900">{{ $sale->invoice_number }}</td>
                        <td class="text-gray-600">{{ $sale->customer->name ?? '-' }}</td>
                        <td class="text-gray-600">{{ $sale->created_at->format('M d, Y') }}</td>
                        <td class="text-gray-600">TZS {{ number_format($sale->total, 2) }}</td>
                        <td class="text-gray-600">TZS {{ number_format($sale->paid, 2) }}</td>
                        <td class="text-gray-600 font-bold text-red-600">TZS {{ number_format($sale->total - $sale->paid, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
