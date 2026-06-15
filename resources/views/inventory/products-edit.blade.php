@extends('layouts.app')

@section('page-title', 'Edit Product')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Edit Product</h2>
            <a href="{{ route('inventory.products') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Products
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

        <form action="{{ route('inventory.products.update', $product) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product Name *</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                    <input type="text" name="sku" value="{{ old('sku', $product->sku) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Barcode</label>
                    <input type="text" name="barcode" value="{{ old('barcode', $product->barcode) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                    <select name="category_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id', $product->category_id) == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
                    <select name="brand_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Select Brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('brand_id', $product->brand_id) == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit *</label>
                    <select name="unit_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Select Unit</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_id', $product->unit_id) == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->short_name }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cost Price (TZS) *</label>
                    <input type="number" step="0.01" name="cost_price" id="cost_price" value="{{ old('cost_price', $product->cost_price) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pricing Method</label>
                    <select name="pricing_method" id="pricing_method" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="percentage">Percentage (%)</option>
                        <option value="flat">Flat Amount</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Profit Value</label>
                    <input type="number" step="0.01" name="profit_value" id="profit_value" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Selling Price (TZS) *</label>
                    <input type="number" step="0.01" name="selling_price" id="selling_price" value="{{ old('selling_price', $product->selling_price) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div class="md:col-span-2">
                    <div class="flex gap-4 mt-2">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Profit per Unit</label>
                            <div class="px-4 py-2 bg-green-50 border border-green-200 rounded-lg text-green-800 font-medium" id="profit_per_unit">
                                0.00
                            </div>
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Profit Margin (%)</label>
                            <div class="px-4 py-2 bg-blue-50 border border-blue-200 rounded-lg text-blue-800 font-medium" id="profit_percentage">
                                0.00
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                    <input type="number" name="quantity" value="{{ old('quantity', $product->quantity) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reorder Level *</label>
                    <input type="number" name="reorder_level" value="{{ old('reorder_level', $product->reorder_level) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                    <input type="date" name="expiry_date" value="{{ old('expiry_date', $product->expiry_date) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="flex items-center gap-2 mt-6">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $product->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm font-medium text-gray-700">Active</span>
                    </label>
                    <label class="flex items-center gap-2 mt-3">
                        <input type="checkbox" name="is_available_online" value="1" {{ old('is_available_online', $product->is_available_online ?? true) ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm font-medium text-gray-700">Available Online</span>
                    </label>
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('description', $product->description) }}</textarea>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('inventory.products') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Update Product
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    function calculateProfit() {
        const costPrice = parseFloat(document.getElementById('cost_price').value) || 0;
        const sellingPrice = parseFloat(document.getElementById('selling_price').value) || 0;
        const profitPerUnit = sellingPrice - costPrice;
        const profitPercentage = costPrice > 0 ? ((profitPerUnit / costPrice) * 100) : 0;
        
        document.getElementById('profit_per_unit').textContent = profitPerUnit.toFixed(2);
        document.getElementById('profit_percentage').textContent = profitPercentage.toFixed(2) + '%';
    }

    function calculateSellingPrice() {
        const costPrice = parseFloat(document.getElementById('cost_price').value) || 0;
        const pricingMethod = document.getElementById('pricing_method').value;
        const profitValue = parseFloat(document.getElementById('profit_value').value) || 0;
        
        let sellingPrice;
        if (pricingMethod === 'percentage') {
            sellingPrice = costPrice * (1 + profitValue / 100);
        } else {
            sellingPrice = costPrice + profitValue;
        }
        
        document.getElementById('selling_price').value = sellingPrice.toFixed(2);
        calculateProfit();
    }

    document.addEventListener('DOMContentLoaded', function() {
        calculateProfit(); // Calculate initial profit
        document.getElementById('cost_price').addEventListener('input', calculateSellingPrice);
        document.getElementById('pricing_method').addEventListener('change', calculateSellingPrice);
        document.getElementById('profit_value').addEventListener('input', calculateSellingPrice);
        document.getElementById('selling_price').addEventListener('input', calculateProfit);
    });
</script>
@endsection
