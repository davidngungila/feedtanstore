@extends('layouts.app')

@section('page-title', 'Purchase Orders')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
            <h2 class="text-xl font-bold text-primary-900">Purchase Orders</h2>
            <div class="flex flex-col md:flex-row items-center gap-3 w-full md:w-auto">
                <form action="{{ route('purchasing.orders') }}" method="GET" id="purchaseOrderSearchForm" class="w-full md:w-72">
                    <div class="relative">
                        <input
                            type="text"
                            name="search"
                            id="purchaseOrderSearch"
                            value="{{ $search ?? '' }}"
                            placeholder="Search purchase orders..."
                            class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            autocomplete="off"
                        >
                        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary-600">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                <a href="{{ route('purchasing.orders.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors whitespace-nowrap">
                    <i class="fas fa-plus mr-2"></i>New Purchase Order
                </a>
            </div>
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
                        <th class="text-left">Sent</th>
                        <th class="text-left">Actions</th>
                    </tr>
                </thead>
                <tbody id="purchase-orders-table-body">
                    @forelse($purchaseOrders as $po)
                    <tr data-search="{{ strtolower($po->po_number . ' ' . ($po->supplier->name ?? '') . ' ' . ($po->status ?? '') . ' ' . ($po->approval_status ?? '') . ' ' . ($po->order_date ?? '') . ' ' . $po->total) }}">
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
                            @if($po->sent_at)
                                <i class="fas fa-check-circle text-green-600 text-xl" title="Sent"></i>
                            @else
                                <i class="fas fa-times-circle text-gray-400 text-xl" title="Not Sent"></i>
                            @endif
                        </td>
                        <td class="flex items-center gap-2">
                            <a href="{{ route('purchasing.orders.show', $po) }}" class="text-primary-600 hover:text-primary-800 p-1" title="View / Review">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('purchasing.orders.edit', $po) }}" class="text-primary-600 hover:text-primary-800 p-1" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            @if($po->approval_status === 'approved' && !$po->isFullyPaid())
                            <a href="{{ route('purchasing.payments.create', ['purchase_order_id' => $po->id]) }}" class="text-green-600 hover:text-green-800 p-1" title="Record Payment">
                                <i class="fas fa-money-bill-wave"></i>
                            </a>
                            @endif
                            <form action="{{ route('purchasing.orders.destroy', $po) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this purchase order?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 p-1" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-gray-500 py-8">No purchase orders found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <script>
            const purchaseOrderSearch = document.getElementById('purchaseOrderSearch');
            const purchaseOrderRows = document.querySelectorAll('#purchase-orders-table-body tr');
            let purchaseOrderSearchTimer = null;

            if (purchaseOrderSearch) {
                purchaseOrderSearch.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();

                    purchaseOrderRows.forEach(row => {
                        const searchData = row.getAttribute('data-search') || '';
                        row.style.display = searchData.includes(searchTerm) ? '' : 'none';
                    });

                    clearTimeout(purchaseOrderSearchTimer);
                    purchaseOrderSearchTimer = setTimeout(() => {
                        document.getElementById('purchaseOrderSearchForm').submit();
                    }, 350);
                });
            }
        </script>
    </div>
</div>
@endsection
