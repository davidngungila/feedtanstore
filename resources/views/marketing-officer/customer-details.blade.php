@extends('layouts.app')

@section('page-title', 'Customer Details')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <a href="{{ route('marketing-officer.customers') }}" class="text-primary-600 hover:text-primary-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Customers
        </a>
    </div>

    <!-- Customer Profile Card -->
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-start gap-6">
            <div class="flex-shrink-0">
                <div class="w-24 h-24 bg-primary-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-primary-600 text-4xl"></i>
                </div>
            </div>
            <div class="flex-1">
                <h1 class="text-2xl font-bold text-primary-900 mb-2">{{ $customer->name }}</h1>
                <p class="text-gray-600 mb-4">{{ $customer->email ?? 'No email provided' }}</p>
                <div class="flex items-center gap-4 text-sm text-gray-600">
                    <span><i class="fas fa-phone mr-1"></i>{{ $customer->phone ?? 'N/A' }}</span>
                    <span><i class="fas fa-shopping-cart mr-1"></i>{{ $customer->onlineOrders->count() }} orders</span>
                    <span><i class="fas fa-wallet mr-1"></i>TZS {{ number_format($customer->total_spent ?? 0, 0) }} total spent</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Contact Information -->
        <div class="card rounded-2xl p-6">
            <h2 class="text-lg font-bold text-primary-900 mb-4">Contact Information</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Email</span>
                    <span class="font-semibold">{{ $customer->email ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Phone</span>
                    <span class="font-semibold">{{ $customer->phone ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Address</span>
                    <span class="font-semibold">{{ $customer->address ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- Account Information -->
        <div class="card rounded-2xl p-6">
            <h2 class="text-lg font-bold text-primary-900 mb-4">Account Information</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Customer Since</span>
                    <span class="font-semibold">{{ $customer->created_at ? $customer->created_at->format('M d, Y') : 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Orders</span>
                    <span class="font-semibold">{{ $customer->onlineOrders->count() }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Balance</span>
                    <span class="font-semibold {{ $customer->balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                        TZS {{ number_format($customer->balance ?? 0, 0) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Order History -->
    <div class="card rounded-2xl p-6">
        <h2 class="text-lg font-bold text-primary-900 mb-4">Order History</h2>
        
        @if($customer->onlineOrders->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order Number</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Payment Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($customer->onlineOrders->take(10) as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $order->order_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $order->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">TZS {{ number_format($order->total, 0) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($order->status == 'delivered') bg-green-100 text-green-700
                                @elseif($order->status == 'out_for_delivery') bg-blue-100 text-blue-700
                                @elseif($order->status == 'cancelled') bg-red-100 text-red-700
                                @else bg-yellow-100 text-yellow-700
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($order->payment_status == 'paid') bg-green-100 text-green-700
                                @elseif($order->payment_status == 'failed') bg-red-100 text-red-700
                                @else bg-yellow-100 text-yellow-700
                                @endif">
                                {{ ucfirst($order->payment_status) }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-shopping-cart text-4xl mb-4 text-gray-300"></i>
            <p class="font-medium">No orders yet</p>
            <p class="text-sm">This customer hasn't placed any orders</p>
        </div>
        @endif
    </div>
</div>
@endsection
