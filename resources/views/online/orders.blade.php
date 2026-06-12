@extends('layouts.app')

@section('page-title', 'Online Orders')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Online Orders</h2>
            <a href="{{ route('online.orders.create') }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>New Order
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700">Order #</th>
                        <th class="px-4 py-3 text-left text-gray-700">Customer</th>
                        <th class="px-4 py-3 text-left text-gray-700">Total</th>
                        <th class="px-4 py-3 text-left text-gray-700">Status</th>
                        <th class="px-4 py-3 text-left text-gray-700">Payment</th>
                        <th class="px-4 py-3 text-left text-gray-700">Rider</th>
                        <th class="px-4 py-3 text-left text-gray-700">Date</th>
                        <th class="px-4 py-3 text-left text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($orders as $order)
                    <tr>
                        <td class="px-4 py-3 font-medium text-primary-600">{{ $order->order_number }}</td>
                        <td class="px-4 py-3">
                            <div>{{ $order->customer_name }}</div>
                            <div class="text-xs text-gray-500">{{ $order->customer_phone }}</div>
                        </td>
                        <td class="px-4 py-3 font-semibold">TZS {{ number_format($order->total, 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                @if($order->status === 'Pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status === 'Confirmed') bg-blue-100 text-blue-800
                                @elseif($order->status === 'Preparing') bg-purple-100 text-purple-800
                                @elseif($order->status === 'Ready') bg-cyan-100 text-cyan-800
                                @elseif($order->status === 'Out for Delivery') bg-orange-100 text-orange-800
                                @elseif($order->status === 'Delivered') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $order->status }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                @if($order->payment_status === 'Paid') bg-green-100 text-green-800
                                @elseif($order->payment_status === 'Pending') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $order->payment_status }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $order->rider ? $order->rider->name : 'Not Assigned' }}</td>
                        <td class="px-4 py-3">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('online.orders.show', $order) }}" class="text-primary-600 hover:text-primary-800 transition-colors">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="px-4 py-8 text-center text-gray-500">
                            No online orders found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection