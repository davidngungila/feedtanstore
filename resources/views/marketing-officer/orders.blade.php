@extends('layouts.app')

@section('page-title', 'Online Orders')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-primary-900">Online Orders</h1>
        <p class="text-gray-600">Manage online orders and deliveries</p>
    </div>

    <!-- Order Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Pending</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $orders->where('status', 'pending')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Confirmed</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $orders->where('status', 'confirmed')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Out for Delivery</p>
                    <p class="text-3xl font-bold text-indigo-600">{{ $orders->where('status', 'out_for_delivery')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-motorcycle text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Delivered</p>
                    <p class="text-3xl font-bold text-green-600">{{ $orders->where('status', 'delivered')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-box text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order Number</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Rider</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($orders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900">{{ $order->order_number }}</p>
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $order->customer_name }}</p>
                            <p class="text-sm text-gray-500">{{ $order->customer_phone }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $order->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">TZS {{ number_format($order->total, 0) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $order->status == 'delivered' ? 'bg-green-100 text-green-700' : 
                                   ($order->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                   ($order->status == 'cancelled' ? 'bg-red-100 text-red-700' : 
                                   ($order->status == 'confirmed' ? 'bg-blue-100 text-blue-700' : 
                                   ($order->status == 'out_for_delivery' ? 'bg-indigo-100 text-indigo-700' : 
                                   'bg-gray-100 text-gray-700')))) }}">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $order->deliveryRider->name ?? 'Not Assigned' }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('marketing-officer.order-details', $order->id) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $orders->links() }}
        </div>
    </div>
</div>
@endsection
