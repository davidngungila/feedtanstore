@extends('layouts.app')

@section('page-title', 'Cancelled Sales')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <h2 class="text-xl font-bold text-primary-900 mb-6">Cancelled Sales</h2>

        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Invoice #</th>
                        <th class="text-left">Customer</th>
                        <th class="text-left">Date</th>
                        <th class="text-left">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
                    <tr>
                        <td class="font-medium text-primary-900">{{ $sale->invoice_number }}</td>
                        <td class="text-gray-600">{{ $sale->customer->name ?? 'Walk-in' }}</td>
                        <td class="text-gray-600">{{ $sale->created_at->format('M d, Y H:i') }}</td>
                        <td class="text-gray-600">TZS {{ number_format($sale->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
