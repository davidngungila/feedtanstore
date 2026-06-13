@extends('layouts.app')

@section('page-title', $purchaseOrder->po_number)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">{{ $purchaseOrder->po_number }}</h2>
            <div class="flex gap-3">
                <a href="{{ route('purchasing.orders.download', $purchaseOrder) }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-download mr-2"></i>Download PDF
                </a>
                <a href="{{ route('purchasing.orders.edit', $purchaseOrder) }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Edit
                </a>
                <a href="{{ route('purchasing.orders') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Back to Orders
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">Supplier</p>
                <p class="font-medium">{{ $purchaseOrder->supplier->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Status</p>
                <span class="badge {{ $purchaseOrder->status === 'received' ? 'badge-green' : ($purchaseOrder->status === 'canceled' ? 'badge-red' : 'badge-yellow') }}">
                    {{ ucfirst($purchaseOrder->status) }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Order Date</p>
                <p class="font-medium">{{ $purchaseOrder->order_date ? date('M d, Y', strtotime($purchaseOrder->order_date)) : '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Expected Date</p>
                <p class="font-medium">{{ $purchaseOrder->expected_date ? date('M d, Y', strtotime($purchaseOrder->expected_date)) : '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Subtotal</p>
                <p class="font-medium">TZS {{ number_format($purchaseOrder->subtotal, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Tax</p>
                <p class="font-medium">TZS {{ number_format($purchaseOrder->tax, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Discount</p>
                <p class="font-medium">TZS {{ number_format($purchaseOrder->discount, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Total</p>
                <p class="font-semibold text-lg">TZS {{ number_format($purchaseOrder->total, 2) }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500 mb-1">Notes</p>
                <p>{{ $purchaseOrder->notes ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-primary-900 mb-6">Products</h3>
        <div class="overflow-x-auto">
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
                    @foreach($purchaseOrder->items as $item)
                    <tr>
                        <td class="font-medium">{{ $item->product->name ?? 'N/A' }}</td>
                        <td class="text-gray-600">{{ $item->quantity }}</td>
                        <td class="text-gray-600">TZS {{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-gray-600">TZS {{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
