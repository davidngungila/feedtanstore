@extends('layouts.app')

@section('page-title', 'Purchase Request Details')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <a href="{{ route('purchasing.purchase-requests') }}" class="text-primary-600 hover:text-primary-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Purchase Requests
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-800 rounded-lg">
            {{ session('error') }}
        </div>
    @endif

    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-primary-900">{{ $purchaseOrderRequest->request_number }}</h1>
                <p class="text-gray-600 mt-1">
                    Requested by <span class="font-medium text-gray-900">{{ $purchaseOrderRequest->requester->name ?? 'Unknown' }}</span>
                    &middot; Submitted {{ $purchaseOrderRequest->created_at->format('M d, Y H:i') }}
                </p>
                @if($orderItems->count() > 1)
                <p class="text-sm text-primary-700 mt-1">
                    <i class="fas fa-layer-group mr-1"></i>
                    Part of multi-product order {{ $baseNumber }} ({{ $orderItems->count() }} items)
                </p>
                @endif
            </div>
            <span class="px-3 py-1 text-sm font-semibold rounded-full
                @if($purchaseOrderRequest->status == 'approved') bg-green-100 text-green-700
                @elseif($purchaseOrderRequest->status == 'rejected') bg-red-100 text-red-700
                @elseif($purchaseOrderRequest->status == 'processed') bg-blue-100 text-blue-700
                @elseif($purchaseOrderRequest->status == 'received') bg-emerald-100 text-emerald-700
                @else bg-yellow-100 text-yellow-700
                @endif">
                {{ ucfirst($purchaseOrderRequest->status) }}
            </span>
        </div>

        {{-- Full request details for EVERY product in this order --}}
        <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">
            Request Details
            @if($orderItems->count() > 1)
                <span class="normal-case font-normal text-gray-400">({{ $orderItems->count() }} products)</span>
            @endif
        </h3>

        @foreach($orderItems as $item)
        @php
            $estUnit = ($item->estimated_cost !== null && $item->requested_quantity > 0)
                ? $item->estimated_cost / $item->requested_quantity
                : null;
            $isCurrent = $item->id === $purchaseOrderRequest->id;
        @endphp
        <div class="rounded-xl border {{ $isCurrent ? 'border-primary-300 bg-primary-50/40' : 'border-gray-200 bg-white' }} p-5 mb-4">
            <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
                <div>
                    <p class="font-semibold text-gray-900">
                        {{ $item->product->name ?? 'N/A' }}
                        @if($isCurrent)
                            <span class="text-xs font-medium text-primary-600">(this item)</span>
                        @endif
                    </p>
                    @if($item->product?->sku)
                    <p class="text-xs text-gray-500">SKU: {{ $item->product->sku }}</p>
                    @endif
                    @if($orderItems->count() > 1)
                    <p class="text-xs font-mono text-gray-400 mt-0.5">{{ $item->request_number }}</p>
                    @endif
                </div>
                <span class="px-2 py-0.5 text-xs font-semibold rounded-full
                    @if($item->status == 'approved') bg-green-100 text-green-700
                    @elseif($item->status == 'rejected') bg-red-100 text-red-700
                    @elseif($item->status == 'processed') bg-blue-100 text-blue-700
                    @elseif($item->status == 'received') bg-emerald-100 text-emerald-700
                    @else bg-yellow-100 text-yellow-700
                    @endif">
                    {{ ucfirst($item->status) }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-3">
                <div>
                    <p class="text-sm text-gray-600">Requested Quantity</p>
                    <p class="font-semibold">{{ number_format($item->requested_quantity, 2) }} {{ $item->product->unit->short_name ?? 'pcs' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Current Stock</p>
                    <p class="font-semibold">{{ $item->product->quantity ?? 0 }} {{ $item->product->unit->short_name ?? 'pcs' }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Requested Supplier</p>
                    @if($item->supplier)
                        <p class="font-semibold">{{ $item->supplier->name }}</p>
                    @else
                        <p class="text-gray-400 italic">Not assigned yet</p>
                    @endif
                </div>
                <div>
                    <p class="text-sm text-gray-600">Estimated Unit Price</p>
                    @if($estUnit !== null)
                        <p class="font-semibold">{{ number_format($estUnit, 2) }}</p>
                    @else
                        <p class="text-gray-400 italic">Not provided</p>
                    @endif
                </div>
                <div>
                    <p class="text-sm text-gray-600">Estimated Total Cost</p>
                    @if($item->estimated_cost !== null)
                        <p class="font-semibold">{{ number_format($item->estimated_cost, 2) }}</p>
                    @else
                        <p class="text-gray-400 italic">Not provided</p>
                    @endif
                </div>
            </div>

            @if($item->reason)
            <div>
                <p class="text-sm text-gray-600">Reason</p>
                <p class="text-gray-900">{{ $item->reason }}</p>
            </div>
            @endif
        </div>
        @endforeach

        <div class="mb-6 mt-6">
            <p class="text-sm text-gray-600">Submitted</p>
            <p class="font-semibold">{{ $purchaseOrderRequest->created_at->format('M d, Y H:i') }}</p>
        </div>

        {{-- Approval & processing history --}}
        @if($purchaseOrderRequest->approved_at || $purchaseOrderRequest->processed_at || $purchaseOrderRequest->admin_notes)
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Approval &amp; Processing</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 p-4 bg-gray-50 rounded-xl">
                @if($purchaseOrderRequest->approver)
                <div>
                    <p class="text-sm text-gray-600">{{ $purchaseOrderRequest->status === 'rejected' ? 'Rejected By' : 'Approved By' }}</p>
                    <p class="font-semibold">{{ $purchaseOrderRequest->approver->name }}</p>
                </div>
                @endif
                @if($purchaseOrderRequest->approved_at)
                <div>
                    <p class="text-sm text-gray-600">{{ $purchaseOrderRequest->status === 'rejected' ? 'Rejected At' : 'Approved At' }}</p>
                    <p class="font-semibold">{{ $purchaseOrderRequest->approved_at->format('M d, Y H:i') }}</p>
                </div>
                @endif
                @if($purchaseOrderRequest->processed_at)
                <div>
                    <p class="text-sm text-gray-600">Processed At</p>
                    <p class="font-semibold">{{ $purchaseOrderRequest->processed_at->format('M d, Y H:i') }}</p>
                </div>
                @endif
                @if($purchaseOrderRequest->admin_notes)
                <div class="md:col-span-3">
                    <p class="text-sm text-gray-600">Admin Notes</p>
                    <p class="text-gray-900">{{ $purchaseOrderRequest->admin_notes }}</p>
                </div>
                @endif
            </div>
        </div>
        @endif

        {{-- Receiving info --}}
        @if($purchaseOrderRequest->status === 'received')
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wide mb-3">Receiving Info</h3>
            @if($grn)
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl grid grid-cols-1 md:grid-cols-3 gap-6">
                <div>
                    <p class="text-sm text-gray-600">Goods Received Note</p>
                    <p class="font-mono font-semibold">{{ $grn->grn_number }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Received Date</p>
                    <p class="font-semibold">{{ \Carbon\Carbon::parse($grn->received_date)->format('M d, Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Value</p>
                    <p class="font-semibold">{{ number_format($grn->total, 2) }}</p>
                </div>
            </div>
            <div class="mt-3">
                <a href="{{ route('purchasing.grn.show', $grn) }}" class="text-primary-600 hover:text-primary-800 text-sm font-medium">
                    <i class="fas fa-file-invoice mr-1"></i>View Full GRN
                </a>
            </div>
            @else
            <div class="p-4 bg-emerald-50 border border-emerald-200 rounded-xl">
                <p class="text-sm text-emerald-800"><i class="fas fa-check-circle mr-2"></i>This request has been fully received into stock.</p>
            </div>
            @endif
        </div>
        @endif

        @php $isAdminOrManager = in_array(Auth::user()->role, ['admin', 'manager']); @endphp
        <a id="actions"></a>

        @if($purchaseOrderRequest->status === 'pending')
            @if($isAdminOrManager)
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Actions</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <form action="{{ route('purchasing.purchase-requests.approve', $purchaseOrderRequest) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Supplier *</label>
                        <select name="supplier_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Select Supplier</option>
                            @foreach($suppliers as $supplier)
                                <option value="{{ $supplier->id }}" {{ $purchaseOrderRequest->supplier_id == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
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
                <form action="{{ route('purchasing.purchase-requests.receive', $purchaseOrderRequest) }}" method="POST">
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
                <form action="{{ route('purchasing.purchase-requests.reject', $purchaseOrderRequest) }}" method="POST">
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
                <div class="flex flex-col md:flex-row gap-3">
                    <form action="{{ route('purchasing.purchase-requests.process', $purchaseOrderRequest) }}" method="POST" class="flex-1">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                            <i class="fas fa-cog mr-2"></i>Process (Create Purchase Order)
                        </button>
                    </form>
                    <a href="#actions" class="flex-1 text-center bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium">
                        <i class="fas fa-truck-loading mr-2"></i>Receive Products Instead
                    </a>
                </div>
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
