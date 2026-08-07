@extends('layouts.app')

@section('page-title', 'Create Stock Request')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-primary-900">Create Stock Request</h1>
        <p class="text-gray-600">Request new stock purchase (admin will select supplier)</p>
    </div>

    <div class="card rounded-2xl p-6">
        <form action="{{ route('storekeeper.purchase-order-requests.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Product</label>
                    <select name="product_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->sku }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Requested Quantity</label>
                    <input type="number" name="requested_quantity" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" required min="1">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Estimated Cost (TZS)</label>
                    <input type="number" name="estimated_cost" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" min="0">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason</label>
                    <textarea name="reason" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" rows="3" placeholder="Why do you need this stock?"></textarea>
                </div>
            </div>
            <div class="mt-6 flex justify-end gap-4">
                <a href="{{ route('storekeeper.purchase-order-requests') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Submit Request</button>
            </div>
        </form>
    </div>
</div>
@endsection
