@extends('layouts.app')

@section('page-title', 'Online Orders')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-primary-900">Online Orders</h1>
            <p class="text-gray-600">Manage online orders and deliveries</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('marketing-officer.dispatch-batches') }}" class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50">
                <i class="fas fa-layer-group mr-2"></i>Dispatch Batches
            </a>
            <a href="{{ route('marketing-officer.bulk-dispatch') }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-layer-group mr-2"></i>Bulk Dispatch
            </a>
        </div>
    </div>

    <!-- Order Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Pending</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $orders->where('status', 'pending')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Confirmed</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $orders->where('status', 'confirmed')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Out for Delivery</p>
                    <p class="text-3xl font-bold text-indigo-600">{{ $orders->where('status', 'out_for_delivery')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-indigo-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-motorcycle text-indigo-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Delivered</p>
                    <p class="text-3xl font-bold text-green-600">{{ $orders->where('status', 'delivered')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-box text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Orders Table -->
    <div class="card rounded-2xl overflow-hidden">
        <form action="{{ route('marketing-officer.bulk-dispatch') }}" method="GET" id="bulk-select-form">
        <div class="flex items-center justify-between px-6 py-3 border-b border-gray-200 bg-gray-50">
            <div class="flex items-center gap-3">
                <input type="checkbox" id="select-all" class="rounded text-primary-600" onchange="toggleSelectAll(this)">
                <label for="select-all" class="text-sm font-medium text-gray-700">Select eligible orders</label>
            </div>
            <div class="flex items-center gap-3">
                <span id="bulk-count" class="text-sm text-gray-600">0 selected</span>
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-paper-plane mr-2"></i>Bulk Dispatch Selected
                </button>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Select</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order Number</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Rider</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($orders as $order)
                    @php
                        $eligible = $order->delivery_rider_id === null
                            && $order->packaging_status === 'completed'
                            && $order->reconciliation_status === 'completed';
                    @endphp
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <input type="checkbox" name="order_ids[]" value="{{ $order->id }}" {{ $eligible ? '' : 'disabled' }} class="bulk-check rounded text-primary-600">
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900">{{ $order->order_number }}</p>
                            @if($eligible)
                                <p class="text-xs text-emerald-600"><i class="fas fa-check-circle mr-1"></i>Ready for dispatch</p>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <p class="font-medium text-gray-900">{{ $order->customer_name }}</p>
                            <p class="text-sm text-gray-500">{{ $order->customer_phone }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $order->created_at->format('M d, Y H:i') }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">TZS {{ number_format($order->total, 0) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $order->status == 'delivered' ? 'bg-green-100 text-green-700' : 
                                   ($order->status == 'pending' ? 'bg-yellow-100 text-yellow-700' : 
                                   ($order->status == 'cancelled' ? 'bg-red-100 text-red-700' : 
                                   ($order->status == 'confirmed' ? 'bg-blue-100 text-blue-700' : 
                                   ($order->status == 'out_for_delivery' ? 'bg-indigo-100 text-indigo-700' : 
                                   'bg-gray-100 text-gray-700')))) }}">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $order->deliveryRider->name ?? 'Not Assigned' }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('marketing-officer.order-details', $order->id) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $orders->links() }}
        </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function toggleSelectAll(source) {
        document.querySelectorAll('.bulk-check:not(:disabled)').forEach(cb => cb.checked = source.checked);
        updateBulkCount();
    }
    function updateBulkCount() {
        const n = document.querySelectorAll('.bulk-check:checked').length;
        document.getElementById('bulk-count').textContent = n + ' selected';
    }
    document.querySelectorAll('.bulk-check').forEach(cb => cb.addEventListener('change', updateBulkCount));
    document.querySelectorAll('.bulk-check').forEach(cb => { if (cb.checked) updateBulkCount(); });
    updateBulkCount();
</script>
@endsection
