@extends('layouts.app')

@section('page-title', 'Stock Transfers')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-primary-900">Stock Transfers</h1>
            <p class="text-gray-600">Transfer stock between locations</p>
        </div>
        <a href="{{ route('storekeeper.stock-transfers.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
            <i class="fas fa-plus mr-2"></i>New Transfer
        </a>
    </div>

    <!-- Transfer Statistics -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Pending</p>
                    <p class="text-3xl font-bold text-yellow-600">{{ $transfers->where('status', 'pending')->count() }}</p>
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
                    <p class="text-3xl font-bold text-green-600">{{ $transfers->where('status', 'approved')->count() }}</p>
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
                    <p class="text-3xl font-bold text-red-600">{{ $transfers->where('status', 'rejected')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-times text-red-600 text-xl"></i>
                </div>
            </div>
        </div>
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-gray-600 text-sm mb-1">Completed</p>
                    <p class="text-3xl font-bold text-blue-600">{{ $transfers->where('status', 'completed')->count() }}</p>
                </div>
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-box text-blue-600 text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Transfers Table -->
    <div class="card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Transfer Number</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">From</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">To</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Quantity</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($transfers as $transfer)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900">{{ $transfer->transfer_number }}</p>
                        </td>
                        <td class="px-6 py-4">
                            @if($transfer->isBulkTransfer())
                                <p class="font-medium text-gray-900">{{ $transfer->items->count() }} items</p>
                                <p class="text-sm text-gray-500">{{ $transfer->items->first()->product->name ?? '' }} @if($transfer->items->count() > 1) + {{ $transfer->items->count() - 1 }} more @endif</p>
                            @else
                                <p class="font-medium text-gray-900">{{ $transfer->product->name ?? 'N/A' }}</p>
                                <p class="text-sm text-gray-500">{{ $transfer->product->sku ?? '' }}</p>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $transfer->fromLocation->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $transfer->toLocation->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">
                            @if($transfer->isBulkTransfer())
                                {{ $transfer->items->sum('quantity') }} total
                            @else
                                {{ $transfer->quantity }} {{ $transfer->product->unit?->short_name ?? 'pcs' }}
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $transfer->status == 'completed' ? 'bg-green-100 text-green-700' : 
                                   ($transfer->status == 'approved' ? 'bg-blue-100 text-blue-700' : 
                                   ($transfer->status == 'rejected' ? 'bg-red-100 text-red-700' : 
                                   'bg-yellow-100 text-yellow-700')) }}">
                                {{ ucfirst($transfer->status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $transfer->created_at->format('M d, Y') }}</td>
                        <td class="px-6 py-4">
                            <a href="{{ route('storekeeper.stock-transfers.show', $transfer) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $transfers->links() }}
        </div>
    </div>
</div>
@endsection
