@extends('layouts.app')

@section('page-title', 'Stock Receiving')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <a href="{{ route('storekeeper.dashboard') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Dashboard
            </a>
            <h1 class="text-2xl font-bold text-primary-900 mt-2">Stock Receiving</h1>
            <p class="text-gray-600">Receive stock based on approved purchase orders</p>
        </div>
    </div>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 border border-green-200 text-green-700 rounded-lg text-sm">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    <div class="card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">PO Number</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Supplier</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @if($purchaseOrders->count() > 0)
                        @foreach($purchaseOrders as $po)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 font-semibold text-gray-900">{{ $po->po_number }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $po->supplier->name ?? 'N/A' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $po->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        @if($po->status == 'received') bg-green-100 text-green-700
                                        @elseif($po->status == 'partial') bg-yellow-100 text-yellow-700
                                        @elseif($po->approval_status == 'approved') bg-blue-100 text-blue-700
                                        @else bg-gray-100 text-gray-700
                                        @endif">
                                        {{ ucfirst($po->status) }}
                                    </span>
                                    @if($po->approval_status == 'approved')
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-purple-100 text-purple-700">Approved</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $po->items->count() }}</td>
                            <td class="px-6 py-4">
                                @if($po->approval_status == 'approved' && $po->status != 'received')
                                <a href="{{ route('storekeeper.stock-receiving.create', $po) }}"
                                   class="inline-flex items-center px-3 py-1.5 bg-primary-100 text-primary-700 text-sm font-medium rounded-lg hover:bg-primary-200 transition-colors">
                                    <i class="fas fa-truck-loading mr-1.5"></i>Receive
                                </a>
                                @else
                                <span class="inline-flex items-center px-3 py-1.5 bg-green-100 text-green-700 text-sm font-medium rounded-lg">
                                    <i class="fas fa-check-circle mr-1.5"></i>Completed
                                </span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-gray-500">
                                <i class="fas fa-inbox text-4xl mb-4 text-gray-300"></i>
                                <p class="font-medium">No pending purchase orders</p>
                                <p class="text-sm">All approved orders have been received</p>
                            </td>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $purchaseOrders->links() }}
        </div>
    </div>
</div>
@endsection