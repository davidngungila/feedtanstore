@extends('layouts.app')

@section('page-title', 'Return ' . $return->return_number)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Return {{ $return->return_number }}</h2>
            <div class="flex gap-3">
                <a href="{{ route('sales.returns.download', $return) }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg">
                    <i class="fas fa-download mr-2"></i>Download PDF
                </a>
                <a href="{{ route('sales.returns') }}" class="px-4 py-2 border border-gray-300 rounded-lg">Back to Returns</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">Invoice #</p>
                <p class="font-medium">
                    <a href="{{ route('sales.show', $return->sale) }}" class="text-primary-600 hover:text-primary-800">
                        {{ $return->sale->invoice_number }}
                    </a>
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Return Date</p>
                <p class="font-medium">{{ $return->created_at->format('M d, Y H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Processed By</p>
                <p class="font-medium">{{ $return->user->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Total</p>
                <p class="font-medium text-red-600">TZS {{ number_format($return->total, 2) }}</p>
            </div>
        </div>

        <div class="mb-6">
            <p class="text-sm text-gray-500 mb-1">Reason for Return</p>
            <p class="p-4 bg-gray-50 rounded-lg">{{ $return->reason }}</p>
        </div>

        <h3 class="text-lg font-semibold text-primary-900 mb-3">Returned Items</h3>
        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">Product</th>
                        <th class="text-left">Price</th>
                        <th class="text-left">Quantity</th>
                        <th class="text-left">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($return->items as $item)
                    <tr>
                        <td class="font-medium">
                            {{ $item->saleItem->product->name ?? 'Product' }}
                        </td>
                        <td>TZS {{ number_format($item->unit_price, 2) }}</td>
                        <td>{{ $item->quantity }}</td>
                        <td>TZS {{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
