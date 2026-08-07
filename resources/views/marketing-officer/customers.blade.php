@extends('layouts.app')

@section('page-title', 'Customers')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-primary-900">Customers</h1>
        <p class="text-gray-600">Manage customer information</p>
    </div>

    <!-- Customers Table -->
    <div class="card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Phone</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Orders</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total Spent</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($customers as $customer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900">{{ $customer->name }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $customer->email ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $customer->phone ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $customer->orders_count ?? 0 }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">TZS {{ number_format($customer->total_spent ?? 0, 0) }}</td>
                        <td class="px-6 py-4">
                            <a href="#" class="text-primary-600 hover:text-primary-700 text-sm font-medium">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $customers->links() }}
        </div>
    </div>
</div>
@endsection
