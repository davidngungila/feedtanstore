@extends('layouts.app')

@section('page-title', 'Purchase Order Request Details')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <a href="{{ route('purchase-order-requests') }}" class="text-primary-600 hover:text-primary-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Requests
        </a>
    </div>

    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-primary-900">{{ $purchaseOrderRequest->request_number }}</h1>
                <p class="text-gray-600">Requested by {{ $purchaseOrderRequest->requester->name ?? 'Unknown' }}</p>
            </div>
            <span class="px-3 py-1 text-sm font-semibold rounded-full
                @if($purchaseOrderRequest->status == 'approved') bg-green-100 text-green-700
                @elseif($purchaseOrderRequest->status == 'rejected') bg-red-100 text-red-700
                @elseif($purchaseOrderRequest->status == 'processed') bg-blue-100 text-blue-700
                @else bg-yellow-100 text-yellow-700
                @endif">
                {{ ucfirst($purchaseOrderRequest->status) }}
            </span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div>
                <p class="text-sm text-gray-600">Product</p>
                <p class="font-semibold">{{ $purchaseOrderRequest->product->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Requested Quantity</p>
                <p class="font-semibold">{{ $purchaseOrderRequest->requested_quantity }} {{ $purchaseOrderRequest->product->unit->short_name ?? 'pcs' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Requested Date</p>
                <p class="font-semibold">{{ $purchaseOrderRequest->created_at->format('M d, Y H:i') }}</p>
            </div>
        </div>

        <div class="mb-6">
            <p class="text-sm text-gray-600">Reason</p>
            <p class="text-gray-900">{{ $purchaseOrderRequest->reason }}</p>
        </div>

        @if($purchaseOrderRequest->status === 'pending')
        <div class="border-t border-gray-200 pt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Admin Actions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <form action="{{ route('purchase-order-requests.approve', $purchaseOrderRequest) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier *</label>
                        <select name="supplier_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="admin_notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Optional admin notes"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">
                        <i class="fas fa-check mr-2"></i>Approve & Assign Supplier
                    </button>
                </form>

                <form action="{{ route('purchase-order-requests.reject', $purchaseOrderRequest) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Rejection Reason *</label>
                        <textarea name="rejection_reason" rows="2" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Why are you rejecting this request?"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-medium">
                        <i class="fas fa-times mr-2"></i>Reject
                    </button>
                </form>
            </div>
        </div>
        @endif

        @if($purchaseOrderRequest->status === 'approved')
        <div class="border-t border-gray-200 pt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Next Step</h3>
            <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-800 mb-3">
                    <i class="fas fa-check-circle mr-2"></i>
                    This request is approved and assigned to {{ $purchaseOrderRequest->supplier->name ?? 'supplier' }}.
                </p>
                <form action="{{ route('purchase-order-requests.process', $purchaseOrderRequest) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                        <i class="fas fa-cog mr-2"></i>Process (Create Purchase Order)
                    </button>
                </form>
            </div>
        </div>
        @endif

        @if($purchaseOrderRequest->status === 'rejected')
        <div class="border-t border-gray-200 pt-6">
            <div class="p-4 bg-red-50 border border-red-200 rounded-lg">
                <p class="text-sm text-red-800">
                    <i class="fas fa-times-circle mr-2"></i>
                    <strong>Rejected by {{ $purchaseOrderRequest->approver->name ?? 'admin' }}:</strong>
                    {{ $purchaseOrderRequest->rejection_reason }}
                </p>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection