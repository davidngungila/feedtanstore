@extends('layouts.app')

@section('page-title', 'Create Marketing Request')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <a href="{{ route('marketing-requests.index') }}" class="text-primary-600 hover:text-primary-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Marketing Requests
        </a>
    </div>

    <div class="card rounded-2xl p-6">
        <h1 class="text-2xl font-bold text-primary-900 mb-6">Create Marketing Request</h1>

        <form action="{{ route('marketing-requests.store') }}" method="POST">
            @csrf
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Title *</label>
                    <input type="text" name="title" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="e.g. Promotional items for campaign">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Describe what you need..."></textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Products *</label>
                    <div id="products_container">
                        <div class="product-item mb-4 p-4 border border-gray-200 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                                    <select name="products[0][product_id]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}">{{ $product->name }} (Available: {{ $product->quantity }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
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

                <div class="flex justify-end gap-3">
                    <a href="{{ route('marketing-requests.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</a>
                    <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">Submit Request</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let productIndex = 1;
document.getElementById('add_product').addEventListener('click', function() {
    const container = document.getElementById('products_container');
    const optionsHtml = `{!! $products->map(fn($p) => '<option value="'.$p->id.'">'.$p->name.' (Available: '.$p->quantity.')</option>')->join('\n') !!}`;
    const template = `
        <div class="product-item mb-4 p-4 border border-gray-200 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                    <select name="products[${productIndex}][product_id]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Select Product</option>
                        ${optionsHtml}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                    <input type="number" name="products[${productIndex}][quantity_requested]" required min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <input type="text" name="products[${productIndex}][notes]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
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
