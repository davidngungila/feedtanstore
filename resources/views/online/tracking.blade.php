@extends('layouts.app')

@section('page-title', 'Order Tracking')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Order Tracking</h2>
        </div>

        <form action="" method="GET" class="mb-6">
            <div class="flex gap-2">
                <input type="text" name="order_number" placeholder="Enter Order Number" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Track Order
                </button>
            </div>
        </form>

        <h3 class="text-lg font-semibold text-primary-900 mb-3">All Orders</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700">Order #</th>
                        <th class="px-4 py-3 text-left text-gray-700">Customer</th>
                        <th class="px-4 py-3 text-left text-gray-700">Status</th>
                        <th class="px-4 py-3 text-left text-gray-700">Rider</th>
                        <th class="px-4 py-3 text-left text-gray-700">Total</th>
                        <th class="px-4 py-3 text-left text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($orders as $order)
                    <tr>
                        <td class="px-4 py-3 font-medium text-primary-600">{{ $order->order_number }}</td>
                        <td class="px-4 py-3">{{ $order->customer_name }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                @if($order->status === 'Delivered')
                                    <i class="fas fa-check-circle text-green-500"></i>
                                @elseif($order->status === 'Out for Delivery')
                                    <i class="fas fa-truck text-orange-500"></i>
                                @elseif($order->status === 'Ready')
                                    <i class="fas fa-box text-cyan-500"></i>
                                @else
                                    <i class="fas fa-clock text-yellow-500"></i>
                                @endif
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
                            </div>
                        </td>
                        <td class="px-4 py-3">{{ $order->rider ? $order->rider->name : 'Not Assigned' }}</td>
                        <td class="px-4 py-3 font-semibold">TZS {{ number_format($order->total, 2) }}</td>
                        <td class="px-4 py-3">
                            <a href="{{ route('online.orders.show', $order) }}" class="text-primary-600 hover:text-primary-800 transition-colors">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">No orders found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection