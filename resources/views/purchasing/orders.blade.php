@extends('layouts.app')

@section('page-title', 'Purchase Orders')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Purchase Orders</h2>
            <a href="{{ route('purchasing.orders.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>New Purchase Order
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">PO Number</th>
                        <th class="text-left">Supplier</th>
                        <th class="text-left">Order Date</th>
                        <th class="text-left">Total</th>
                        <th class="text-left">Status</th>
                        <th class="text-left">Approval Status</th>
                        <th class="text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseOrders as $po)
                    <tr>
                        <td class="font-medium text-primary-900">
                            <a href="{{ route('purchasing.orders.show', $po) }}" class="hover:underline">{{ $po->po_number }}</a>
                        </td>
                        <td class="text-gray-600">{{ $po->supplier->name ?? 'N/A' }}</td>
                        <td class="text-gray-600">{{ $po->order_date ? date('M d, Y', strtotime($po->order_date)) : '-' }}</td>
                        <td class="text-gray-600">TZS {{ number_format($po->total, 2) }}</td>
                        <td>
                            <span class="badge {{ $po->status === 'received' ? 'badge-green' : ($po->status === 'canceled' ? 'badge-red' : 'badge-yellow') }}">
                                {{ ucfirst($po->status) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $po->approval_status === 'approved' ? 'badge-green' : ($po->approval_status === 'rejected' ? 'badge-red' : 'badge-yellow') }}">
                                {{ ucfirst($po->approval_status) }}
                            </span>
                        </td>
                        <td class="flex items-center gap-2">
                            <a href="{{ route('purchasing.orders.show', $po) }}" class="text-primary-600 hover:text-primary-800 p-1" title="View / Review">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('purchasing.orders.edit', $po) }}" class="text-primary-600 hover:text-primary-800 p-1" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="{{ route('purchasing.payments.create', ['purchase_order_id' => $po->id]) }}" class="text-green-600 hover:text-green-800 p-1" title="Record Payment">
                                <i class="fas fa-money-bill-wave"></i>
                            </a>
                            <form action="{{ route('purchasing.orders.destroy', $po) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this purchase order?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 p-1" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
