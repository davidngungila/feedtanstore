@extends('layouts.app')

@section('page-title', $supplier->name)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">{{ $supplier->name }}</h2>
            <div class="flex gap-3">
                <a href="{{ route('purchasing.suppliers.edit', $supplier) }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Edit
                </a>
                <a href="{{ route('purchasing.suppliers') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Back to Suppliers
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">Email</p>
                <p class="font-medium">{{ $supplier->email ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Phone</p>
                <p class="font-medium">{{ $supplier->phone ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Contact Person</p>
                <p class="font-medium">{{ $supplier->contact_person ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">City</p>
                <p class="font-medium">{{ $supplier->city ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Country</p>
                <p class="font-medium">{{ $supplier->country ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Status</p>
                <span class="badge {{ $supplier->is_active ? 'badge-green' : 'badge-gray' }}">
                    {{ $supplier->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500 mb-1">Address</p>
                <p>{{ $supplier->address ?? '-' }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500 mb-1">Notes</p>
                <p>{{ $supplier->notes ?? '-' }}</p>
            </div>
        </div>
    </div>

    <div class="card rounded-2xl p-6 mb-6">
        <h3 class="text-lg font-bold text-primary-900 mb-6">Purchase Orders</h3>
        @if($supplier->purchaseOrders->count() > 0)
            <div class="overflow-x-auto">
                <table class="data-table w-full">
                    <thead>
                        <tr>
                            <th class="text-left">PO Number</th>
                            <th class="text-left">Order Date</th>
                            <th class="text-left">Expected Date</th>
                            <th class="text-left">Total</th>
                            <th class="text-left">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($supplier->purchaseOrders as $po)
                        <tr>
                            <td class="font-medium">{{ $po->po_number }}</td>
                            <td>{{ $po->order_date ? date('M d, Y', strtotime($po->order_date)) : '-' }}</td>
                            <td>{{ $po->expected_date ? date('M d, Y', strtotime($po->expected_date)) : '-' }}</td>
                            <td>TZS {{ number_format($po->total, 2) }}</td>
                            <td>
                                <span class="badge {{ $po->status === 'received' ? 'badge-green' : ($po->status === 'canceled' ? 'badge-red' : 'badge-yellow') }}">
                                    {{ ucfirst($po->status) }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-center py-8">No purchase orders found for this supplier.</p>
        @endif
    </div>

    <div class="card rounded-2xl p-6 mb-6">
        <h3 class="text-lg font-bold text-primary-900 mb-6">Goods Received Notes</h3>
        @if($supplier->goodsReceivedNotes->count() > 0)
            <div class="overflow-x-auto">
                <table class="data-table w-full">
                    <thead>
                        <tr>
                            <th class="text-left">GRN Number</th>
                            <th class="text-left">Received Date</th>
                            <th class="text-left">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($supplier->goodsReceivedNotes as $grn)
                        <tr>
                            <td class="font-medium">{{ $grn->grn_number }}</td>
                            <td>{{ $grn->received_date ? date('M d, Y', strtotime($grn->received_date)) : '-' }}</td>
                            <td>TZS {{ number_format($grn->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-center py-8">No goods received notes found for this supplier.</p>
        @endif
    </div>

    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-primary-900 mb-6">Payments</h3>
        @if($supplier->payments->count() > 0)
            <div class="overflow-x-auto">
                <table class="data-table w-full">
                    <thead>
                        <tr>
                            <th class="text-left">Payment Number</th>
                            <th class="text-left">Payment Date</th>
                            <th class="text-left">Amount</th>
                            <th class="text-left">Payment Method</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($supplier->payments as $payment)
                        <tr>
                            <td class="font-medium">{{ $payment->payment_number }}</td>
                            <td>{{ $payment->payment_date ? date('M d, Y', strtotime($payment->payment_date)) : '-' }}</td>
                            <td>TZS {{ number_format($payment->amount, 2) }}</td>
                            <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-center py-8">No payments found for this supplier.</p>
        @endif
    </div>
</div>
@endsection
