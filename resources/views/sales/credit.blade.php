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
                        <th class="text-left">Actions</th>
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
                        <td class="flex items-center gap-2">
                            <a href="{{ route('sales.credit.show', $sale) }}" class="text-primary-600 hover:text-primary-800 p-1" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('sales.credit.edit', $sale) }}" class="text-primary-600 hover:text-primary-800 p-1" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($sale->total - $sale->paid > 0)
                            <a href="{{ route('sales.credit.payment', $sale) }}" class="text-green-600 hover:text-green-800 p-1" title="Add Payment">
                                <i class="fas fa-money-bill-wave"></i>
                            </a>
                            @endif
                            <form action="{{ route('sales.credit.destroy', $sale) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this credit sale?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 p-1" title="Cancel">
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
