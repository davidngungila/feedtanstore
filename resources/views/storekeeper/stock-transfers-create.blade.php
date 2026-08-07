@extends('layouts.app')

@section('page-title', 'Create Stock Transfer')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-primary-900">Create Stock Transfer</h1>
        <p class="text-gray-600">Transfer stock between locations</p>
    </div>

    <div class="card rounded-2xl p-6">
        <form action="{{ route('storekeeper.stock-transfers.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Product</label>
                    <select name="product_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->quantity }} {{ $product->unit?->short_name ?? 'pcs' }} available)</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Quantity</label>
                    <input type="number" name="quantity" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" required min="1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">From Location</label>
                    <select name="from_location_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                        <option value="">Select Source Location</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">To Location</label>
                    <select name="to_location_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                        <option value="">Select Destination Location</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" rows="3"></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-4">
                <a href="{{ route('storekeeper.stock-transfers') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Submit Transfer Request</button>
            </div>
        </form>
    </div>
</div>
@endsection
