@extends('layouts.app')

@section('page-title', 'New Goods Received Note')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]" x-data="{ items: [ { product_id: '', quantity: 1, unit_price: 0, expiry_date: '' } ] }">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">New Goods Received Note (GRN)</h2>
            <a href="{{ route('inventory.receiving') }}" class="text-primary-600 hover:text-primary-800">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
        
        <form method="POST" action="{{ route('inventory.receiving.store') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-primary-900 mb-2">Supplier</label>
                    <select name="supplier_id" class="w-full p-3 rounded-lg border border-primary-200 bg-white focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200" required>
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-primary-900 mb-2">Received Date</label>
                    <input type="date" name="received_date" class="w-full p-3 rounded-lg border border-primary-200 bg-primary-50 text-primary-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200" required value="{{ date('Y-m-d') }}">
                </div>
                
                <div class="col-span-1 md:col-span-2">
                    <label class="block text-sm font-semibold text-primary-900 mb-2">Notes (Optional)</label>
                    <textarea name="notes" rows="2" class="w-full p-3 rounded-lg border border-primary-200 bg-primary-50 text-primary-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200" placeholder="Enter notes"></textarea>
                </div>
            </div>
            
            <h3 class="text-lg font-semibold text-primary-900 mb-3">Products</h3>
            
            <div class="overflow-x-auto mb-4">
                <table class="data-table w-full">
                    <thead>
                        <tr>
                            <th class="text-left">Product</th>
                            <th class="text-left">Quantity</th>
                            <th class="text-left">Unit Price (TZS)</th>
                            <th class="text-left">Expiry Date (Optional)</th>
                            <th class="text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td class="p-2">
                                    <select :name="'products[' + index + '][product_id]'" class="w-full p-2 rounded border border-primary-200" required x-model="item.product_id">
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }} (Current: {{ $product->quantity }})</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="p-2">
                                    <input type="number" :name="'products[' + index + '][quantity]'" min="1" class="w-full p-2 rounded border border-primary-200" required x-model.number="item.quantity">
                                </td>
                                <td class="p-2">
                                    <input type="number" :name="'products[' + index + '][unit_price]'" min="0" step="0.01" class="w-full p-2 rounded border border-primary-200" required x-model.number="item.unit_price">
                                </td>
                                <td class="p-2">
                                    <input type="date" :name="'products[' + index + '][expiry_date]'" class="w-full p-2 rounded border border-primary-200" x-model="item.expiry_date">
                                </td>
                                <td class="p-2">
                                    <button type="button" @click="items.splice(index, 1)" class="text-red-600 hover:text-red-800" x-show="items.length > 1">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            
            <button type="button" @click="items.push({ product_id: '', quantity: 1, unit_price: 0, expiry_date: '' })" class="mb-4 bg-primary-100 hover:bg-primary-200 text-primary-700 px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>Add Product
            </button>
            
            <div class="flex gap-3">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i>Save GRN
                </button>
                <a href="{{ route('inventory.receiving') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
