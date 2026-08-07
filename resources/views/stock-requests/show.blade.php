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
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Available Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity Approved</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($stockRequest->items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $item->product->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->quantity_requested }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->product->quantity }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->quantity_approved }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->notes ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @if($stockRequest->status === 'pending' && (Auth::user()->role === 'storekeeper' || Auth::user()->role === 'admin' || Auth::user()->role === 'manager'))
    <div class="card rounded-2xl p-6 mt-6">
        <h2 class="text-lg font-bold text-primary-900 mb-4">Approve Stock Request</h2>
        <div class="flex justify-end gap-3">
            <form action="{{ route('stock-requests.reject', $stockRequest) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                    Reject Request
                </button>
            </form>
            <form action="{{ route('stock-requests.approve', $stockRequest) }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    Approve Request
                </button>
            </form>
        </div>
    </div>
    @endif

    @if($stockRequest->status === 'approved' && (Auth::user()->role === 'storekeeper' || Auth::user()->role === 'admin' || Auth::user()->role === 'manager'))
    <div class="card rounded-2xl p-6 mt-6">
        <h2 class="text-lg font-bold text-primary-900 mb-4">Issue Products to Packaging</h2>
        <p class="text-sm text-gray-600 mb-4">Add products to packaging one by one. Each addition will be verified and recorded.</p>
        <form action="{{ route('stock-requests.issue', $stockRequest) }}" method="POST">
            @csrf
            <div class="space-y-4">
                @foreach($stockRequest->items as $item)
                <div class="p-4 border border-gray-200 rounded-lg @if($item->quantity_approved > 0) bg-green-50 @endif">
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                        <div class="md:col-span-2">
                            <p class="font-semibold text-gray-900">{{ $item->product->name }}</p>
                            <p class="text-sm text-gray-600">Requested: {{ $item->quantity_requested }} | Available: {{ $item->product->quantity }}</p>
                            @if($item->quantity_approved > 0)
                            <p class="text-sm text-green-600 font-medium">✓ Already issued: {{ $item->quantity_approved }}</p>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Add to Package</label>
                            <input type="number" 
                                   name="items[{{ $item->id }}][quantity_issued]" 
                                   min="0" 
                                   max="{{ min($item->quantity_requested - $item->quantity_approved, $item->product->quantity) }}"
                                   value="0"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($item->product->quantity >= ($item->quantity_requested - $item->quantity_approved)) bg-green-100 text-green-800 @else bg-red-100 text-red-800 @endif">
                                @if($item->product->quantity >= ($item->quantity_requested - $item->quantity_approved)) In Stock @else Insufficient Stock @endif
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="flex justify-end mt-6">
                <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Add Selected to Package
                </button>
            </div>
        </form>
    </div>
    @endif
</div>
@endsection
