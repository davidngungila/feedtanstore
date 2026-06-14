@extends('layouts.app')

@section('page-title', 'Cancelled Sales')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Cancelled Sales</h2>
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
                        <th class="text-left">Invoice #</th>
                        <th class="text-left">Customer</th>
                        <th class="text-left">Date</th>
                        <th class="text-left">Subtotal</th>
                        <th class="text-left">Tax</th>
                        <th class="text-left">Discount</th>
                        <th class="text-left">Total</th>
                        <th class="text-left">Type</th>
                        <th class="text-left">Payment</th>
                        <th class="text-left">Cancelled At</th>
                        <th class="text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sales as $sale)
                    <tr>
                        <td class="font-medium text-primary-900">
                            <a href="{{ route('sales.show', $sale) }}">{{ $sale->invoice_number }}</a>
                        </td>
                        <td class="text-gray-600">{{ $sale->customer->name ?? 'Walk-in' }}</td>
                        <td class="text-gray-600">{{ $sale->created_at->format('M d, Y H:i') }}</td>
                        <td class="text-gray-600">TZS {{ number_format($sale->subtotal, 2) }}</td>
                        <td class="text-gray-600">TZS {{ number_format($sale->tax, 2) }}</td>
                        <td class="text-gray-600">TZS {{ number_format($sale->discount, 2) }}</td>
                        <td class="text-gray-600">TZS {{ number_format($sale->total, 2) }}</td>
                        <td class="text-gray-600">{{ ucfirst($sale->type) }}</td>
                        <td class="text-gray-600">{{ ucfirst($sale->payment_method) }}</td>
                        <td class="text-gray-600">{{ $sale->deleted_at ? $sale->deleted_at->format('M d, Y H:i') : 'N/A' }}</td>
                        <td class="flex items-center gap-2">
                            <a href="{{ route('sales.show', $sale) }}" class="text-primary-600 hover:text-primary-800" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            @if($sale->deleted_at)
                            <form action="{{ route('sales.restore', $sale) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to restore this sale?')">
                                @csrf
                                <button type="submit" class="text-green-600 hover:text-green-800" title="Restore">
                                    <i class="fas fa-undo"></i>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
