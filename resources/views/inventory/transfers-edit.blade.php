@extends('layouts.app')

@section('page-title', 'Edit Stock Transfer')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Edit Stock Transfer</h2>
            <a href="{{ route('inventory.transfers') }}" class="text-primary-600 hover:text-primary-800">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
        
        @if ($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-800 rounded-lg">
                @foreach ($errors->all() as $error)
                    <p>{{ $error }}</p>
                @endforeach
            </div>
        @endif
        
        <form method="POST" action="{{ route('inventory.transfers.update', $transfer->id) }}">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-semibold text-primary-900 mb-2">Product</label>
                    <select name="product_id" class="w-full p-3 rounded-lg border border-primary-200 bg-white focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200" required>
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" {{ $product->id == $transfer->product_id ? 'selected' : '' }}>{{ $product->name }} (Stock: {{ $product->quantity }})</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-primary-900 mb-2">From Location</label>
                    <select name="from_location_id" class="w-full p-3 rounded-lg border border-primary-200 bg-white focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200" required>
                        <option value="">Select From Location</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ $location->id == $transfer->from_location_id ? 'selected' : '' }}>{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-primary-900 mb-2">To Location</label>
                    <select name="to_location_id" class="w-full p-3 rounded-lg border border-primary-200 bg-white focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200" required>
                        <option value="">Select To Location</option>
                        @foreach($locations as $location)
                            <option value="{{ $location->id }}" {{ $location->id == $transfer->to_location_id ? 'selected' : '' }}>{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-primary-900 mb-2">Quantity</label>
                    <input type="number" name="quantity" min="1" value="{{ $transfer->quantity }}" class="w-full p-3 rounded-lg border border-primary-200 bg-primary-50 text-primary-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200" required placeholder="Enter quantity">
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-primary-900 mb-2">Transfer Date</label>
                    <input type="date" name="transfer_date" value="{{ $transfer->transfer_date ? date('Y-m-d', strtotime($transfer->transfer_date)) : date('Y-m-d') }}" class="w-full p-3 rounded-lg border border-primary-200 bg-primary-50 text-primary-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200" required>
                </div>
                
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-semibold text-primary-900 mb-2">Notes (Optional)</label>
                    <textarea name="notes" rows="3" class="w-full p-3 rounded-lg border border-primary-200 bg-primary-50 text-primary-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200" placeholder="Enter notes">{{ $transfer->notes }}</textarea>
                </div>
                
                <div class="col-span-1 md:col-span-2 flex gap-3 mt-2">
                    <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-save mr-2"></i>Update Transfer
                    </button>
                    <a href="{{ route('inventory.transfers') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
