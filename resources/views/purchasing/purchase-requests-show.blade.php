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
            <div class="flex flex-wrap items-center gap-2">
                @php $isAdminOrManager = in_array(Auth::user()->role, ['admin', 'manager']); @endphp
                @if($isAdminOrManager && $purchaseOrderRequest->status === 'pending')
                    <a href="{{ route('purchasing.purchase-requests.edit', $purchaseOrderRequest) }}" class="px-3 py-1.5 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium">
                        <i class="fas fa-edit mr-1"></i>Edit Request
                    </a>
                @endif
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
            @php
                $missingSuppliers = $orderItems->filter(fn ($i) => $i->status === 'pending' && !$i->supplier_id);
                $canApprove = $orderItems->where('status', 'pending')->isNotEmpty() && $missingSuppliers->isEmpty();
            @endphp
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-1">Actions</h3>
                <p class="text-sm text-gray-500 mb-4">Approving creates one Purchase Order per supplier and sends each automatically with only their own products. Receiving becomes available after approval.</p>

                {{-- Supplier split preview --}}
                <div class="mb-4 p-4 bg-gray-50 rounded-xl">
                    <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Order will be sent to</p>
                    @foreach($orderItems->where('status', 'pending')->groupBy('supplier_id') as $supplierId => $group)
                        <div class="flex items-start gap-2 text-sm py-1">
                            @if($supplierId)
                                <i class="fas fa-truck text-primary-500 mt-0.5"></i>
                                <p><span class="font-semibold">{{ $group->first()->supplier->name }}</span>
                                    <span class="text-gray-500">— {{ $group->pluck('product.name')->implode(', ') }}</span></p>
                            @else
                                <i class="fas fa-exclamation-triangle text-yellow-500 mt-0.5"></i>
                                <p class="text-yellow-800"><span class="font-semibold">No supplier assigned:</span> {{ $group->pluck('product.name')->implode(', ') }}</p>
                            @endif
                        </div>
                    @endforeach
                    @if($missingSuppliers->isNotEmpty())
                        <p class="text-xs text-red-600 mt-2"><i class="fas fa-ban mr-1"></i>Assign a supplier to every product (via Edit Request) before approving.</p>
                    @endif
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <form action="{{ route('purchasing.purchase-requests.approve', $purchaseOrderRequest) }}" method="POST" id="approveForm">
                    @csrf
                    @method('PUT')
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg text-sm text-green-800">
                        <i class="fas fa-info-circle mr-2"></i>Approving sends each Purchase Order to its supplier automatically.
                    </div>
                    <button type="button" {{ $canApprove ? '' : 'disabled' }} onclick="openApproveModal()" class="w-full bg-green-600 hover:bg-green-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg font-medium">
                        <i class="fas fa-check mr-2"></i>Approve &amp; Send to Suppliers
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

                <!-- Edit before approval -->
                <a href="{{ route('purchasing.purchase-requests.edit', $purchaseOrderRequest) }}" class="block p-4 border-2 border-dashed border-primary-300 rounded-lg hover:border-primary-500 hover:bg-primary-50/50 transition-colors">
                    <div class="text-center h-full flex flex-col items-center justify-center gap-2 py-6">
                        <i class="fas fa-edit text-3xl text-primary-500"></i>
                        <p class="font-semibold text-primary-900">Edit Request</p>
                        <p class="text-xs text-gray-500">Adjust quantity, supplier, prices or reason before approving</p>
                    </div>
                </a>
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
        <div class="border-t border-gray-200 pt-6" id="receive-section">
            <h3 class="text-lg font-semibold text-gray-900 mb-1">Receive Products</h3>
            <p class="text-sm text-gray-500 mb-4">
                <i class="fas fa-paper-plane mr-1 text-green-600"></i>
                Approved and sent to {{ $purchaseOrderRequest->supplier->name ?? 'supplier' }} — record the delivery below when goods arrive.
            </p>
            <form action="{{ route('purchasing.purchase-requests.receive', $purchaseOrderRequest) }}" method="POST" class="grid grid-cols-1 md:grid-cols-3 gap-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Received Quantity *</label>
                    <input type="number" name="received_quantity" value="{{ $purchaseOrderRequest->requested_quantity }}" min="1" max="{{ $purchaseOrderRequest->requested_quantity }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price *</label>
                    <input type="number" name="unit_price" step="0.01" min="0" value="{{ $purchaseOrderRequest->product->cost_price ?? 0 }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Price per unit received">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Batch Number</label>
                    <input type="text" name="batch_number" maxlength="100" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="e.g., BATCH-2026-001">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Expiry Date
                        @if($purchaseOrderRequest->product->category?->requires_expiry_date)
                            <span class="text-red-500">*</span>
                        @endif
                    </label>
                    <input type="date" name="expiry_date" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <input type="text" name="notes" maxlength="500" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Optional delivery notes">
                </div>
                <div class="md:col-span-3">
                    <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white px-6 py-2 rounded-lg font-medium">
                        <i class="fas fa-truck-loading mr-2"></i>Receive Products
                    </button>
                </div>
            </form>
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

{{-- Approve Confirmation Modal --}}
<div id="approveModal" class="hidden fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" onclick="closeApproveModal()"></div>
    <div class="flex min-h-full items-center justify-center p-4">
        <div class="relative bg-white rounded-2xl shadow-xl max-w-lg w-full p-6 animate-[fadeIn_0.2s_ease]">
            <button type="button" onclick="closeApproveModal()" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
                <i class="fas fa-times text-lg"></i>
            </button>
            <div class="flex items-start gap-4 mb-5">
                <div class="w-12 h-12 rounded-full bg-green-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-paper-plane text-green-600 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-primary-900">Approve &amp; Send to Suppliers?</h3>
                    <p class="text-sm text-gray-500 mt-1">This will create one Purchase Order per supplier and email/SMS each automatically with only their own products.</p>
                </div>
            </div>

            <div class="bg-gray-50 rounded-xl p-4 mb-5">
                <p class="text-xs font-semibold text-gray-700 uppercase tracking-wide mb-2">Order will be sent to</p>
                @foreach($orderItems->where('status', 'pending')->groupBy('supplier_id') as $supplierId => $group)
                    @if($supplierId)
                        <div class="flex items-start gap-2 text-sm py-1">
                            <i class="fas fa-truck text-primary-500 mt-0.5"></i>
                            <p><span class="font-semibold">{{ $group->first()->supplier->name }}</span>
                                <span class="text-gray-500">— {{ $group->pluck('product.name')->implode(', ') }}</span></p>
                        </div>
                    @endif
                @endforeach
            </div>

            <div class="mb-5">
                <label class="block text-sm font-medium text-gray-700 mb-1">Notes (optional)</label>
                <textarea form="approveForm" name="admin_notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Optional admin notes"></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <button type="button" onclick="closeApproveModal()" class="px-5 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</button>
                <button type="submit" form="approveForm" class="px-5 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors">
                    <i class="fas fa-check mr-2"></i>Yes, Approve &amp; Send
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function openApproveModal() {
    document.getElementById('approveModal').classList.remove('hidden');
    document.body.style.overflow = 'hidden';
}
function closeApproveModal() {
    document.getElementById('approveModal').classList.add('hidden');
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function (e) {
    if (e.key === 'Escape') closeApproveModal();
});
</script>
@endsection
