@extends('layouts.app')

@section('page-title', 'New Stock Count')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]" x-data="{ items: [ { product_id: '', quantity_in_system: 0, quantity_actual: 0, notes: '' } ] }">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">New Stock Count</h2>
            <a href="{{ route('inventory.count') }}" class="text-primary-600 hover:text-primary-800">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>
        
        <form method="POST" action="{{ route('inventory.count.store') }}">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-sm font-semibold text-primary-900 mb-2">Location</label>
                    <select name="location_id" class="w-full p-3 rounded-lg border border-primary-200 bg-white focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200">
                        <option value="">Select Location (Optional)</option>
                        @foreach($locations ?? [] as $location)
                            <option value="{{ $location->id }}">{{ $location->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label class="block text-sm font-semibold text-primary-900 mb-2">Count Date</label>
                    <input type="date" name="count_date" class="w-full p-3 rounded-lg border border-primary-200 bg-primary-50 text-primary-900 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-200" required value="{{ date('Y-m-d') }}">
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
                            <th class="text-left">Quantity in System</th>
                            <th class="text-left">Actual Quantity</th>
                            <th class="text-left">Notes</th>
                            <th class="text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(item, index) in items" :key="index">
                            <tr>
                                <td class="p-2">
                                    <select :name="'products[' + index + '][product_id]'" class="w-full p-2 rounded border border-primary-200" required x-model="item.product_id" @change="updateSystemStock($event, index)">
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-quantity="{{ $product->quantity }}">{{ $product->name }} (Current: {{ $product->quantity }})</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="p-2">
                                    <input type="number" :name="'products[' + index + '][quantity_in_system]'" class="w-full p-2 rounded border border-primary-200" required x-model.number="item.quantity_in_system" readonly>
                                </td>
                                <td class="p-2">
                                    <input type="number" :name="'products[' + index + '][quantity_actual]'" min="0" class="w-full p-2 rounded border border-primary-200" required x-model.number="item.quantity_actual">
                                </td>
                                <td class="p-2">
                                    <input type="text" :name="'products[' + index + '][notes]'" class="w-full p-2 rounded border border-primary-200" x-model="item.notes" placeholder="Notes">
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
            
            <button type="button" @click="items.push({ product_id: '', quantity_in_system: 0, quantity_actual: 0, notes: '' })" class="mb-4 bg-primary-100 hover:bg-primary-200 text-primary-700 px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>Add Product
            </button>
            
            <div class="flex gap-3">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                    <i class="fas fa-save mr-2"></i>Save Stock Count
                </button>
                <a href="{{ route('inventory.count') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-6 py-3 rounded-lg font-medium transition-colors">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
function updateSystemStock(event, index) {
    const selectedOption = event.target.options[event.target.selectedIndex];
    const quantity = selectedOption ? parseFloat(selectedOption.dataset.quantity) : 0;
    document.querySelectorAll('[x-data]')[0].__x.$data.items[index].quantity_in_system = quantity;
}
</script>
@endsection