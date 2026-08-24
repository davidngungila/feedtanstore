@extends('layouts.app')

@section('page-title', 'Stock Requests')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-primary-900">Stock Requests</h1>
            <p class="text-gray-600">Request new stock purchases</p>
        </div>
        <a href="{{ route('storekeeper.purchase-order-requests.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <i class="fas fa-plus mr-2"></i>New Request
        </a>
    </div>

    <!-- Request Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Pending</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $stats['pending'] }}</p>
                </div>
                <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Approved</p>
                    <p class="text-3xl font-bold text-green-600">{{ $stats['approved'] }}</p>
                </div>
                <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-green-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Rejected</p>
                    <p class="text-3xl font-bold text-red-600">{{ $stats['rejected'] }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-times text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Processed</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $stats['processed'] }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-box text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Requests Table -->
    <div class="card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Request Number</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Products (Quantity)</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Supplier(s)</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($groupPage as $group)
                        @php
                            $items = $group->sortBy('request_number')->values();
                            $baseRequest = $items->first();
                            $statuses = $items->pluck('status')->unique();
                            $supplierNames = $items->map(fn ($r) => $r->supplier->name ?? null)->filter()->unique()->values();
                        @endphp
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4">
                                <p class="font-semibold text-gray-900">{{ preg_replace('/-\d+$/', '', $baseRequest->request_number) }}</p>
                                @if($items->count() > 1)
                                    <span class="inline-flex items-center px-2 py-0.5 mt-1 rounded-full text-xs font-semibold bg-primary-100 text-primary-700">
                                        {{ $items->count() }} products
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @foreach($items as $item)
                                    <p class="text-sm text-gray-900">{{ $item->product->name ?? 'N/A' }} <span class="text-gray-500">({{ $item->requested_quantity }} {{ $item->product->unit?->short_name ?? 'pcs' }})</span></p>
                                @endforeach
                            </td>
                            <td class="px-6 py-4">
                                @if($statuses->count() === 1)
                                    @php $status = $statuses->first(); @endphp
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                                        {{ in_array($status, ['processed', 'received']) ? 'bg-green-100 text-green-700' :
                                           ($status == 'approved' ? 'bg-blue-100 text-blue-700' :
                                           ($status == 'rejected' ? 'bg-red-100 text-red-700' :
                                           'bg-yellow-100 text-yellow-700')) }}">
                                        {{ ucfirst($status) }}
                                    </span>
                                @else
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($statuses as $status)
                                            <span class="px-2 py-0.5 text-xs font-medium rounded-full
                                                {{ in_array($status, ['processed', 'received']) ? 'bg-green-100 text-green-700' :
                                                   ($status == 'approved' ? 'bg-blue-100 text-blue-700' :
                                                   ($status == 'rejected' ? 'bg-red-100 text-red-700' :
                                                   'bg-yellow-100 text-yellow-700')) }}">
                                                {{ $items->where('status', $status)->count() }} {{ ucfirst($status) }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $supplierNames->count() === 1 ? $supplierNames->first() : ($supplierNames->count() > 1 ? 'Mixed (' . $supplierNames->implode(', ') . ')' : 'Not Assigned') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">{{ $baseRequest->created_at->format('M d, Y') }}</td>
                            <td class="px-6 py-4">
                                <a href="{{ route('storekeeper.purchase-order-requests.show', $baseRequest) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $groupPage->links() }}
        </div>
    </div>
</div>
@endsection
