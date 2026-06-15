@extends('layouts.app')

@section('page-title', $sale->invoice_number)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-800 rounded-xl">
            <i class="fas fa-check-circle mr-2"></i>
            {{ session('success') }}
        </div>
    @endif
    
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-6 gap-4">
            <h2 class="text-xl font-bold text-primary-900">{{ $sale->invoice_number }}</h2>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('sales.receipts.download', $sale) }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 flex items-center">
                    <i class="fas fa-download mr-2"></i>Download PDF
                </a>
                <a href="{{ route('sales.receipts.print', $sale) }}" target="_blank" class="px-4 py-2 border border-gray-300 rounded-lg flex items-center">
                    <i class="fas fa-print mr-2"></i>Print
                </a>
                <a href="{{ route('sales.new') }}" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 flex items-center">
                    <i class="fas fa-plus mr-2"></i>New Sale
                </a>
                <a href="{{ route('sales.history') }}" class="px-4 py-2 border border-gray-300 rounded-lg flex items-center">Back</a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">Customer</p>
                <p class="font-medium">{{ $sale->customer->name ?? 'Walk-in Customer' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Date</p>
                <p class="font-medium">{{ $sale->created_at->format('M d, Y H:i') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Cashier</p>
                <p class="font-medium">{{ $sale->user->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Status</p>
                <span class="badge {{ $sale->status == 'completed' ? 'badge-green' : 'badge-red' }}">{{ ucfirst($sale->status) }}</span>
            </div>
            @if($sale->discount_id && $sale->discountApplied)
            <div>
                <p class="text-sm text-gray-500 mb-1">Discount Applied</p>
                <p class="font-medium text-primary-600">{{ $sale->discountApplied->name }}</p>
            </div>
            @endif
        </div>

        <div class="border-t pt-4 mb-6">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b">
                            <th class="text-left py-2">Product</th>
                            <th class="text-left py-2">Qty</th>
                            <th class="text-left py-2">Price</th>
                            <th class="text-left py-2">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($sale->items as $item)
                        <tr class="border-b border-gray-100">
                            <td class="py-3">{{ $item->product->name ?? 'Product Not Found' }}</td>
                            <td class="py-3">{{ $item->quantity }}</td>
                            <td class="py-3">TZS {{ number_format($item->unit_price, 2) }}</td>
                            <td class="py-3">TZS {{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="flex flex-col items-end gap-2 mb-6">
            <div class="flex justify-between w-64">
                <span class="text-gray-600">Subtotal:</span>
                <span>TZS {{ number_format($sale->subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between w-64">
                <span class="text-gray-600">Tax:</span>
                <span>TZS {{ number_format($sale->tax, 2) }}</span>
            </div>
            <div class="flex justify-between w-64 text-red-600">
                <span>Discount:</span>
                <span>-TZS {{ number_format($sale->discount, 2) }}</span>
            </div>
            <div class="flex justify-between w-64 text-lg font-bold border-t pt-2">
                <span>Total:</span>
                <span>TZS {{ number_format($sale->total, 2) }}</span>
            </div>
            <div class="flex justify-between w-64">
                <span class="text-gray-600">Paid:</span>
                <span>TZS {{ number_format($sale->paid, 2) }}</span>
            </div>
            <div class="flex justify-between w-64">
                <span class="text-gray-600">Change:</span>
                <span>TZS {{ number_format($sale->change, 2) }}</span>
            </div>
        </div>

        @if($sale->notes)
        <div class="border-t pt-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">Notes</h3>
            <p class="text-gray-600 whitespace-pre-wrap">{{ $sale->notes }}</p>
        </div>
        @endif
        
        @if($sale->cancellation_reason)
        <div class="border-t pt-4 mt-4">
            <h3 class="text-sm font-semibold text-red-700 mb-2">Cancellation Reason</h3>
            <p class="text-gray-600 whitespace-pre-wrap">{{ $sale->cancellation_reason }}</p>
        </div>
        @endif
    </div>
</div>
@endsection
