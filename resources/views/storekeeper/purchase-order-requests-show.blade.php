@extends('layouts.app')

@section('page-title', 'Purchase Order Request Details')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <a href="{{ route('storekeeper.purchase-order-requests') }}" class="text-primary-600 hover:text-primary-800 font-medium">
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
            @if($purchaseOrderRequest->supplier)
            <div>
                <p class="text-sm text-gray-600">Requested Supplier</p>
                <p class="font-semibold">{{ $purchaseOrderRequest->supplier->name }}</p>
            </div>
            @endif
            @if($purchaseOrderRequest->estimated_cost !== null)
            <div>
                <p class="text-sm text-gray-600">Estimated Cost</p>
                <p class="font-semibold">{{ number_format($purchaseOrderRequest->estimated_cost, 2) }}</p>
            </div>
            @endif
        </div>

        <div class="mb-6">
            <p class="text-sm text-gray-600">Reason</p>
            <p class="text-gray-900">{{ $purchaseOrderRequest->reason }}</p>
        </div>

        @php $isAdminOrManager = in_array(Auth::user()->role, ['admin', 'manager']); @endphp

        @if($purchaseOrderRequest->status === 'pending')
            @if($isAdminOrManager)
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Admin Actions</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <form action="{{ route('storekeeper.purchase-order-requests.approve', $purchaseOrderRequest) }}" method="POST">
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

                <!-- Receive Products Directly -->
                <form action="{{ route('storekeeper.purchase-order-requests.receive', $purchaseOrderRequest) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Received Quantity *</label>
                        <input type="number" name="received_quantity" value="{{ $purchaseOrderRequest->requested_quantity }}" min="1" max="{{ $purchaseOrderRequest->requested_quantity }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price *</label>
                        <input type="number" name="unit_price" step="0.01" min="0" value="{{ $purchaseOrderRequest->product->cost_price ?? 0 }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Price per unit received">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Batch Number</label>
                        <input type="text" name="batch_number" maxlength="100" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="e.g., BATCH-2026-001">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Expiry Date
                            @if($purchaseOrderRequest->product->category?->requires_expiry_date)
                                <span class="text-red-500">*</span>
                            @endif
                        </label>
                        <input type="date" name="expiry_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea name="notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Optional notes (e.g., batch number, condition)"></textarea>
                    </div>
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                        <i class="fas fa-truck-loading mr-2"></i>Receive Products
                    </button>
                </form>

                <!-- Reject -->
                <form action="{{ route('storekeeper.purchase-order-requests.reject', $purchaseOrderRequest) }}" method="POST">
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
            @else
            <div class="border-t border-gray-200 pt-6">
                <div class="p-4 bg-blue-50 border border-blue-200 rounded-lg">
                    <p class="text-sm text-blue-800">
                        <i class="fas fa-hourglass-half mr-2"></i>
                        This request is awaiting review by an administrator or manager.
                    </p>
                </div>
            </div>
            @endif
        @endif

        @if($purchaseOrderRequest->status === 'approved')
        <div class="border-t border-gray-200 pt-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Next Step</h3>
            <div class="p-4 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm text-green-800 mb-3">
                    <i class="fas fa-check-circle mr-2"></i>
                    This request is approved and assigned to {{ $purchaseOrderRequest->supplier->name ?? 'supplier' }}.
                </p>
                <form action="{{ route('storekeeper.purchase-order-requests.process', $purchaseOrderRequest) }}" method="POST">
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