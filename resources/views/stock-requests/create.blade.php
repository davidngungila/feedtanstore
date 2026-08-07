@extends('layouts.app')

@section('page-title', 'New Stock Request')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <a href="{{ route('stock-requests.index') }}" class="text-primary-600 hover:text-primary-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Stock Requests
        </a>
    </div>

    <div class="card rounded-2xl p-6">
        <h1 class="text-2xl font-bold text-primary-900 mb-6">New Stock Request</h1>

        <form action="{{ route('stock-requests.store') }}" method="POST">
            @csrf
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Request Type *</label>
                    <select name="request_type" id="request_type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Select Request Type</option>
                        <option value="store_use">Store Use</option>
                        <option value="online_order">Online Order</option>
                    </select>
                </div>

                <div id="online_order_section" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Online Order *</label>
                    <select name="online_order_id" id="online_order_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Select Online Order</option>
                        @foreach($onlineOrders as $order)
                            <option value="{{ $order->id }}">{{ $order->order_number }} - {{ $order->customer_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Products *</label>
                    <div id="products_container">
                        <div class="product-item mb-4 p-4 border border-gray-200 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                                    <select name="products[0][product_id]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product-select">
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-quantity="{{ $product->quantity }}">{{ $product->name }} (Available: {{ $product->quantity }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity Requested *</label>
                                    <input type="number" name="products[0][quantity_requested]" required min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                    <input type="text" name="products[0][notes]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                </div>
                            </div>
                            <button type="button" class="remove-product mt-2 text-red-600 hover:text-red-800 text-sm">
                                <i class="fas fa-trash mr-1"></i>Remove
                            </button>
                        </div>
                    </div>
                    <button type="button" id="add_product" class="mt-2 text-primary-600 hover:text-primary-800 text-sm">
                        <i class="fas fa-plus mr-1"></i>Add Product
                    </button>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" rows="3" placeholder="Any additional notes..."></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('stock-requests.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                        Submit Request
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let productIndex = 1;

document.getElementById('request_type').addEventListener('change', function() {
    const onlineOrderSection = document.getElementById('online_order_section');
    const onlineOrderSelect = document.getElementById('online_order_id');
    
    if (this.value === 'online_order') {
        onlineOrderSection.classList.remove('hidden');
        onlineOrderSelect.setAttribute('required', 'required');
    } else {
        onlineOrderSection.classList.add('hidden');
        onlineOrderSelect.removeAttribute('required');
        onlineOrderSelect.value = '';
    }
});

document.getElementById('add_product').addEventListener('click', function() {
    const container = document.getElementById('products_container');
    const template = `
        <div class="product-item mb-4 p-4 border border-gray-200 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                    <select name="products[${productIndex}][product_id]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-quantity="{{ $product->quantity }}">{{ $product->name }} (Available: {{ $product->quantity }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity Requested *</label>
                    <input type="number" name="products[${productIndex}][quantity_requested]" required min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <input type="number" name="products[${productIndex}][notes]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>
            <button type="button" class="remove-product mt-2 text-red-600 hover:text-red-800 text-sm">
                <i class="fas fa-trash mr-1"></i>Remove
            </button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', template);
    productIndex++;
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-product')) {
        e.target.closest('.product-item').remove();
    }
});
</script>
@endsection
