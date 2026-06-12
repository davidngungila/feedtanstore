@extends('layouts.app')

@section('page-title', 'New Stock Adjustment')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">New Stock Adjustment</h2>
            <a href="{{ route('inventory.adjustments') }}" class="text-primary-600 hover:text-primary-800">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
        
        <form method="POST" action="{{ route('inventory.adjustments.store') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-semibold text-primary-900 mb-2">Product</label>
                    <select name="product_id" class="w-full p-3 rounded-lg border border-primary-200 bg-white focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200" required>
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} (Stock: {{ $product->quantity }})</option>
                        @endforeach
                    </select>
                    @error('product_id')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-primary-900 mb-2">Type</label>
                    <select name="type" class="w-full p-3 rounded-lg border border-primary-200 bg-white focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200" required>
                        <option value="addition">Addition (+)</option>
                        <option value="subtraction">Subtraction (-)</option>
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-primary-900 mb-2">Quantity Change</label>
                    <input type="number" name="quantity_change" min="1" class="w-full p-3 rounded-lg border border-primary-200 bg-primary-50 text-primary-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200" required placeholder="Enter quantity">
                    @error('quantity_change')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-primary-900 mb-2">Adjustment Date</label>
                    <input type="date" name="adjustment_date" class="w-full p-3 rounded-lg border border-primary-200 bg-primary-50 text-primary-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200" required value="{{ date('Y-m-d') }}">
                    @error('adjustment_date')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-semibold text-primary-900 mb-2">Reason</label>
                    <input type="text" name="reason" class="w-full p-3 rounded-lg border border-primary-200 bg-primary-50 text-primary-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200" required placeholder="Enter reason">
                    @error('reason')
                        <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-semibold text-primary-900 mb-2">Notes (Optional)</label>
                    <textarea name="notes" rows="3" class="w-full p-3 rounded-lg border border-primary-200 bg-primary-50 text-primary-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200" placeholder="Enter notes"></textarea>
                </div>
                
                <div class="col-span-1 md:col-span-2 flex gap-3 mt-2">
                    <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                        <i class="fas fa-save mr-2"></i>Save Adjustment
                    </button>
                    <a href="{{ route('inventory.adjustments') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors">
                        Cancel
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
