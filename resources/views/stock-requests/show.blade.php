@extends('layouts.app')

@section('page-title', 'Stock Request Details')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <a href="{{ route('stock-requests.index') }}" class="text-primary-600 hover:text-primary-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Stock Requests
        </a>
    </div>

    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-primary-900">{{ $stockRequest->request_number }}</h1>
                <p class="text-gray-600">Requested by {{ $stockRequest->user->name }}</p>
            </div>
            <div>
                @if($stockRequest->status === 'pending')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                @elseif($stockRequest->status === 'approved')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Approved</span>
                @elseif($stockRequest->status === 'rejected')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                @elseif($stockRequest->status === 'completed')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">Completed</span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-600">Request Type</p>
                <p class="font-semibold">
                    @if($stockRequest->request_type === 'online_order')
                        Online Order
                    @else
                        Store Use
                    @endif
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Requested At</p>
                <p class="font-semibold">{{ $stockRequest->requested_at->format('M d, Y H:i') }}</p>
            </div>
            @if($stockRequest->onlineOrder)
            <div>
                <p class="text-sm text-gray-600">Linked Online Order</p>
                <p class="font-semibold">{{ $stockRequest->onlineOrder->order_number }}</p>
            </div>
            @endif
            @if($stockRequest->approved_by)
            <div>
                <p class="text-sm text-gray-600">Approved By</p>
                <p class="font-semibold">{{ $stockRequest->approvedBy->name }}</p>
            </div>
            @endif
        </div>

        @if($stockRequest->notes)
        <div class="mt-6">
            <p class="text-sm text-gray-600">Notes</p>
            <p class="text-gray-900">{{ $stockRequest->notes }}</p>
        </div>
        @endif
    </div>

    <div class="card rounded-2xl overflow-hidden">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-bold text-primary-900">Requested Products</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity Requested</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity Approved</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($stockRequest->items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $item->product->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->quantity_requested }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->quantity_approved }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->notes ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
