@extends('layouts.app')

@section('page-title', 'Create Stock Transfer')

@section('scripts')
<script>
    function addTransferItem() {
        const itemsContainer = document.getElementById('transferItems');
        const itemIndex = itemsContainer.children.length;
        
        const newItem = document.createElement('div');
        newItem.className = 'transfer-item grid grid-cols-12 gap-4 items-center';
        newItem.innerHTML = `
            <div class="col-span-5">
                <select name="items[${itemIndex}][product_id]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                    <option value="">Select Product</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->quantity }} {{ $product->unit?->short_name ?? 'pcs' }} available)</option>
                    @endforeach
                </select>
            </div>
            <div class="col-span-4">
                <input type="number" name="items[${itemIndex}][quantity]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" required min="1" placeholder="Quantity">
            </div>
            <div class="col-span-2">
                <button type="button" onclick="removeTransferItem(this)" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        
        itemsContainer.appendChild(newItem);
    }

    function removeTransferItem(button) {
        const itemsContainer = document.getElementById('transferItems');
        if (itemsContainer.children.length > 1) {
            button.closest('.transfer-item').remove();
        }
    }
</script>
@endsection

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-primary-900">Create Stock Transfer</h1>
        <p class="text-gray-600">Transfer stock between locations (bulk transfer supported)</p>
    </div>

    <div class="card rounded-2xl p-6">
        <form action="{{ route('storekeeper.stock-transfers.store') }}" method="POST">
            @csrf
            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Transfer Items</label>
                    <div id="transferItems" class="space-y-4">
                        <div class="transfer-item grid grid-cols-12 gap-4 items-center">
                            <div class="col-span-5">
                                <select name="items[0][product_id]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" required>
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }} ({{ $product->quantity }} {{ $product->unit?->short_name ?? 'pcs' }} available)</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-span-4">
                                <input type="number" name="items[0][quantity]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" required min="1" placeholder="Quantity">
                            </div>
                            <div class="col-span-2">
                                <button type="button" onclick="removeTransferItem(this)" class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <button type="button" onclick="addTransferItem()" class="mt-4 w-full px-4 py-2 border-2 border-dashed border-gray-300 rounded-lg text-gray-600 hover:border-primary-500 hover:text-primary-600 transition-colors">
                        <i class="fas fa-plus mr-2"></i>Add Another Item
                    </button>
                </div>

                <div>
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
