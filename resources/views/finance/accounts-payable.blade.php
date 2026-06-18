@extends('layouts.app')

@section('page-title', 'Accounts Payable')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <h2 class="text-xl font-bold text-primary-900 mb-4">Accounts Payable</h2>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Outstanding Purchase Orders -->
            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Outstanding Purchase Orders</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-gray-600">PO Number</th>
                                <th class="px-4 py-3 text-left text-gray-600">Supplier</th>
                                <th class="px-4 py-3 text-left text-gray-600">Amount</th>
                                <th class="px-4 py-3 text-left text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($purchaseOrders as $po)
                                <tr>
                                    <td class="px-4 py-3 font-semibold">{{ $po->po_number }}</td>
                                    <td class="px-4 py-3">{{ $po->supplier ? $po->supplier->name : 'N/A' }}</td>
                                    <td class="px-4 py-3 font-bold">TZS {{ number_format($po->total, 2) }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('purchasing.orders.show', $po) }}" class="text-blue-600 hover:text-blue-800">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">No outstanding POs!</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Supplier Payments -->
            <div>
                <h3 class="text-lg font-semibold text-gray-700 mb-3">Supplier Payments</h3>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-gray-600">Payment</th>
                                <th class="px-4 py-3 text-left text-gray-600">Supplier</th>
                                <th class="px-4 py-3 text-left text-gray-600">Amount</th>
                                <th class="px-4 py-3 text-left text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y">
                            @forelse($supplierPayments as $payment)
                                <tr>
                                    <td class="px-4 py-3 font-semibold">{{ $payment->payment_number }}</td>
                                    <td class="px-4 py-3">{{ $payment->supplier ? $payment->supplier->name : 'N/A' }}</td>
                                    <td class="px-4 py-3 font-bold">TZS {{ number_format($payment->amount, 2) }}</td>
                                    <td class="px-4 py-3">
                                        <a href="{{ route('purchasing.payments.show', $payment) }}" class="text-blue-600 hover:text-blue-800">
                                            View
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-4 py-8 text-center text-gray-500">No supplier payments yet!</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection