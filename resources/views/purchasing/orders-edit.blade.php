@extends('layouts.app')

@section('page-title', 'Edit Purchase Order')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Edit Purchase Order</h2>
            <a href="{{ route('purchasing.orders') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Orders
            </a>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-800 rounded-lg">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('purchasing.orders.update', $purchaseOrder) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier *</label>
                    <select name="supplier_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id', $purchaseOrder->supplier_id) == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Order Date *</label>
                    <input type="date" name="order_date" value="{{ old('order_date', $purchaseOrder->order_date) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Expected Date</label>
                    <input type="date" name="expected_date" value="{{ old('expected_date', $purchaseOrder->expected_date) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Discount</label>
                    <input type="number" step="0.01" name="discount" value="{{ old('discount', $purchaseOrder->discount) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tax</label>
                    <input type="number" step="0.01" name="tax" value="{{ old('tax', $purchaseOrder->tax) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                    <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="pending" {{ old('status', $purchaseOrder->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="received" {{ old('status', $purchaseOrder->status) == 'received' ? 'selected' : '' }}>Received</option>
                        <option value="canceled" {{ old('status', $purchaseOrder->status) == 'canceled' ? 'selected' : '' }}>Canceled</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('notes', $purchaseOrder->notes) }}</textarea>
                </div>
            </div>

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-primary-900 mb-4">Products</h3>
                <div id="products-container">
                    @foreach($purchaseOrder->items as $index => $item)
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 product-item">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                            <select name="products[{{ $index }}][product_id]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product-select">
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" 
                                            data-price="{{ $product->cost_price }}" 
                                            {{ (old('products.' . $index . '.product_id', $item->product_id) == $product->id) ? 'selected' : '' }}>
                                        {{ $product->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                            <input type="number" name="products[{{ $index }}][quantity]" 
                                   value="{{ old('products.' . $index . '.quantity', $item->quantity) }}" 
                                   min="1" required 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product-quantity">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price *</label>
                            <input type="number" step="0.01" name="products[{{ $index }}][unit_price]" 
                                   value="{{ old('products.' . $index . '.unit_price', $item->unit_price) }}" 
                                   required 
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product-price">
                        </div>
                        <div class="flex items-end">
                            <button type="button" class="remove-product text-red-600 hover:text-red-800 px-4 py-2 border border-red-300 rounded-lg">Remove</button>
                        </div>
                    </div>
                    @endforeach
                </div>
                <button type="button" id="add-product" class="mt-2 text-primary-600 hover:text-primary-800">
                    <i class="fas fa-plus mr-2"></i>Add Product
                </button>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('purchasing.orders') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Update Purchase Order
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let productIndex = {{ $purchaseOrder->items->count() }};

document.getElementById('add-product').addEventListener('click', function() {
    const container = document.getElementById('products-container');
    const template = document.querySelector('.product-item');
    const clone = template.cloneNode(true);
    
    clone.querySelectorAll('input, select').forEach(input => {
        const name = input.name.replace(/\[\d+\]/, '[' + productIndex + ']');
        input.name = name;
        input.value = input.tagName === 'SELECT' ? '' : (input.name.includes('quantity') ? '1' : '');
    });
    
    container.appendChild(clone);
    productIndex++;
    
    clone.querySelector('.remove-product').addEventListener('click', function() {
        clone.remove();
    });
    
    clone.querySelector('.product-select').addEventListener('change', function() {
        const price = this.options[this.selectedIndex].dataset.price || 0;
        clone.querySelector('.product-price').value = price;
    });
});

document.querySelectorAll('.remove-product').forEach(btn => {
    btn.addEventListener('click', function() {
        this.closest('.product-item').remove();
    });
});

document.querySelectorAll('.product-select').forEach(select => {
    select.addEventListener('change', function() {
        const price = this.options[this.selectedIndex].dataset.price || 0;
        this.closest('.product-item').querySelector('.product-price').value = price;
    });
});
</script>
@endsection
