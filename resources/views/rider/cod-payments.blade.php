@extends('layouts.app')

@section('page-title', 'Cash on Delivery')

@section('content')
<div class="animate-[fadeIn_0.4s_ease] space-y-6">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="{{ route('rider.dashboard') }}" class="text-primary-600 hover:text-primary-800 mb-2 inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
                <h2 class="text-xl font-bold text-primary-900">Cash on Delivery</h2>
            </div>
        </div>

        @php
            $totalCollected = $orders->where('status', 'delivered')->sum('total');
            $pendingOrdersCount = $orders->count();
            $pendingCount = $orders->where('status', '!=', 'delivered')->count();
        @endphp

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
            <div class="p-4 border rounded-lg bg-green-50">
                <div class="text-2xl font-bold text-green-700">TZS {{ number_format($totalCollected, 2) }}</div>
                <div class="text-sm text-green-600">Total Collected</div>
            </div>
            <div class="p-4 border rounded-lg bg-blue-50">
                <div class="text-2xl font-bold text-blue-700">{{ $pendingOrdersCount }}</div>
                <div class="text-sm text-blue-600">Total COD Orders</div>
            </div>
            <div class="p-4 border rounded-lg bg-yellow-50">
                <div class="text-2xl font-bold text-yellow-700">{{ $pendingCount }}</div>
                <div class="text-sm text-yellow-600">Pending Collection</div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700">Order #</th>
                        <th class="px-4 py-3 text-left text-gray-700">Customer</th>
                        <th class="px-4 py-3 text-left text-gray-700">Amount</th>
                        <th class="px-4 py-3 text-left text-gray-700">Status</th>
                        <th class="px-4 py-3 text-left text-gray-700">Date</th>
                        <th class="px-4 py-3 text-left text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($orders as $order)
                    <tr>
                        <td class="px-4 py-3 font-medium text-primary-600">
                            {{ $order->short_customer_reference }}
                        </td>
                        <td class="px-4 py-3">
                            <div>{{ $order->customer_name }}</div>
                            <div class="text-xs text-gray-500">{{ $order->customer_phone }}</div>
                        </td>
                        <td class="px-4 py-3 font-semibold">TZS {{ number_format($order->total, 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                @if($order->status === 'delivered') bg-green-100 text-green-800
                                @else bg-yellow-100 text-yellow-800
                                @endif">
                                {{ ucwords(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('rider.orders.show', $order) }}" class="text-primary-600 hover:text-primary-800 transition-colors">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            No COD orders found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
