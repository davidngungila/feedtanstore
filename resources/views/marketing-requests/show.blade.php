@extends('layouts.app')

@section('page-title', 'Marketing Request Details')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <a href="{{ route('marketing-requests.index') }}" class="text-primary-600 hover:text-primary-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Marketing Requests
        </a>
    </div>

    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-primary-900">{{ $marketingRequest->title }}</h1>
                <p class="text-gray-600">{{ $marketingRequest->request_number }} &middot; Requested by {{ $marketingRequest->requester->name }}</p>
            </div>
            <div>
                @if($marketingRequest->status === 'pending')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                @elseif($marketingRequest->status === 'accepted')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-blue-100 text-blue-800">Processing</span>
                @elseif($marketingRequest->status === 'processed')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-green-100 text-green-800">Processed</span>
                @elseif($marketingRequest->status === 'rejected')
                    <span class="px-3 py-1 text-sm font-semibold rounded-full bg-red-100 text-red-800">Rejected</span>
                @endif
            </div>
        </div>

        @if($marketingRequest->description)
        <div class="mb-4">
            <p class="text-sm text-gray-600">Description</p>
            <p class="text-gray-900">{{ $marketingRequest->description }}</p>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-600">Requested At</p>
                <p class="font-semibold">{{ $marketingRequest->created_at->format('M d, Y H:i') }}</p>
            </div>
            @if($marketingRequest->processor)
            <div>
                <p class="text-sm text-gray-600">Processed By</p>
                <p class="font-semibold">{{ $marketingRequest->processor->name }}</p>
            </div>
            @endif
            @if($marketingRequest->processed_at)
            <div>
                <p class="text-sm text-gray-600">Processed At</p>
                <p class="font-semibold">{{ $marketingRequest->processed_at->format('M d, Y H:i') }}</p>
            </div>
            @endif
        </div>

        @if($marketingRequest->storekeeper_notes)
        <div class="mt-4 p-4 bg-blue-50 rounded-lg">
            <p class="text-sm text-blue-700 font-medium mb-1">Storekeeper Notes</p>
            <p class="text-blue-900">{{ $marketingRequest->storekeeper_notes }}</p>
        </div>
        @endif
    </div>

    <!-- Items Table -->
    <div class="card rounded-2xl overflow-hidden mb-6">
        <div class="p-6 border-b border-gray-200">
            <h2 class="text-lg font-bold text-primary-900">Requested Products</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Qty Requested</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Available Stock</th>
                        @if($marketingRequest->status === 'processed')
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Qty Provided</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Unit Price</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Notes</th>
                        @endif
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($marketingRequest->items as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4">
                            <p class="font-semibold text-gray-900">{{ $item->product->name }}</p>
                            <p class="text-sm text-gray-500">{{ $item->product->sku }}</p>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->quantity_requested }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->product->quantity }}</td>
                        @if($marketingRequest->status === 'processed')
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->quantity_provided ?? '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->unit_price ? 'TZS ' . number_format($item->unit_price, 0) : '-' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $item->notes ?? '-' }}</td>
                        @endif
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Storekeeper Actions -->
    @if(Auth::user()->role === 'storekeeper' || Auth::user()->role === 'admin')

        {{-- Accept button when pending --}}
        @if($marketingRequest->status === 'pending')
        <div class="card rounded-2xl p-6 mt-6">
            <h2 class="text-lg font-bold text-primary-900 mb-4">Action Required</h2>
            <p class="text-sm text-gray-600 mb-4">Accept this request to start processing it.</p>
            <div class="flex justify-end gap-3">
                <form action="{{ route('marketing-requests.reject', $marketingRequest) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-6 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">Reject</button>
                </form>
                <form action="{{ route('marketing-requests.accept', $marketingRequest) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">Accept Request</button>
                </form>
            </div>
        </div>
        @endif

        {{-- Process form when accepted --}}
        @if($marketingRequest->status === 'accepted')
        <div class="card rounded-2xl p-6 mt-6">
            <h2 class="text-lg font-bold text-primary-900 mb-4">Process Request</h2>
            <p class="text-sm text-gray-600 mb-4">Fill in the details for each product and submit.</p>
            <form action="{{ route('marketing-requests.process', $marketingRequest) }}" method="POST">
                @csrf
                <div class="space-y-4">
                    @foreach($marketingRequest->items as $item)
                    <div class="p-4 border border-gray-200 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-center">
                            <div class="md:col-span-1">
                                <p class="font-semibold text-gray-900">{{ $item->product->name }}</p>
                                <p class="text-sm text-gray-600">Requested: {{ $item->quantity_requested }} | Available: {{ $item->product->quantity }}</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity to Provide *</label>
                                <input type="number" name="items[{{ $item->id }}][quantity_provided]" min="0" max="{{ $item->product->quantity }}" value="{{ min($item->quantity_requested, $item->product->quantity) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price (TZS)</label>
                                <input type="number" name="items[{{ $item->id }}][unit_price]" min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                <input type="text" name="items[{{ $item->id }}][notes]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes for Marketing Officer</label>
                    <textarea name="storekeeper_notes" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Any notes for the marketing officer..."></textarea>
                </div>
                <div class="flex justify-end mt-6">
                    <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                        <i class="fas fa-check mr-2"></i>Submit Processed Request
                    </button>
                </div>
            </form>
        </div>
        @endif

    @endif
</div>
@endsection
