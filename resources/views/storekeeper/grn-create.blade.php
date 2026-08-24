@extends('layouts.app')

@section('page-title', 'New Goods Received Note')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <a href="{{ route('storekeeper.grn') }}" class="text-primary-600 hover:text-primary-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to GRNs
        </a>
    </div>

    <div class="card rounded-2xl p-6">
        <h2 class="text-xl font-bold text-primary-900 mb-6">New Goods Received Note</h2>

        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-800 rounded-lg">{{ session('error') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-800 rounded-lg">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('storekeeper.grn.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier *</label>
                    <select name="supplier_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Received Date *</label>
                    <input type="date" name="received_date" value="{{ old('received_date', date('Y-m-d')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <input type="text" name="notes" value="{{ old('notes') }}" maxlength="1000" placeholder="Optional notes" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-primary-900 mb-4">Products</h3>
                <div id="products_container">
                    <div class="product_item mb-6 p-4 border border-gray-200 rounded-lg" data-index="0">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                                <select name="products[0][product_id]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_select">
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-cost="{{ $product->cost_price }}" data-selling="{{ $product->selling_price }}" data-requires-expiry="{{ $product->category && $product->category->requires_expiry_date ? 'true' : 'false' }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                                <input type="number" step="0.01" min="1" name="products[0][quantity]" value="1" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Unit (Cost) Price *</label>
                                <input type="number" step="0.01" min="0" name="products[0][unit_price]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_cost">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Selling Price</label>
                                <input type="number" step="0.01" min="0" name="products[0][selling_price]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_selling">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Batch Number</label>
                                <input type="text" name="products[0][batch_number]" maxlength="100" placeholder="Auto-generated if left blank" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date <span class="expiry-required text-red-500 hidden">*</span></label>
                                <input type="date" name="products[0][expiry_date]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_expiry">
                                <span class="expiry-hint text-xs text-gray-500 hidden">Required for this product category</span>
                            </div>
                            <div class="flex items-end justify-end">
                                <button type="button" class="remove_product text-red-600 hover:text-red-800 px-4 py-2 border border-red-300 rounded-lg">Remove</button>
                            </div>
                        </div>
                    </div>
                </div>
                <button type="button" id="add_product" class="mt-2 text-primary-600 hover:text-primary-800 font-medium">
                    <i class="fas fa-plus mr-2"></i>Add Product
                </button>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('storekeeper.grn') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Cancel</a>
                <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-check mr-2"></i>Create GRN
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let productIndex = 1;
const productsData = @json($products);

document.getElementById('add_product').addEventListener('click', function() {
    const container = document.getElementById('products_container');

    let optionsHtml = '<option value="">Select Product</option>';
    productsData.forEach(product => {
        optionsHtml += `<option value="${product.id}" data-cost="${product.cost_price ?? 0}" data-selling="${product.selling_price ?? 0}" data-requires-expiry="${product.category && product.category.requires_expiry_date ? 'true' : 'false'}">${product.name.replace(/"/g, '&quot;')}</option>`;
    });

    const template = `
        <div class="product_item mb-6 p-4 border border-gray-200 rounded-lg" data-index="${productIndex}">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                    <select name="products[${productIndex}][product_id]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_select">
                        ${optionsHtml}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                    <input type="number" step="0.01" min="1" name="products[${productIndex}][quantity]" value="1" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit (Cost) Price *</label>
                    <input type="number" step="0.01" min="0" name="products[${productIndex}][unit_price]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_cost">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Selling Price</label>
                    <input type="number" step="0.01" min="0" name="products[${productIndex}][selling_price]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_selling">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Batch Number</label>
                    <input type="text" name="products[${productIndex}][batch_number]" maxlength="100" placeholder="Auto-generated if left blank" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date <span class="expiry-required text-red-500 hidden">*</span></label>
                    <input type="date" name="products[${productIndex}][expiry_date]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_expiry">
                    <span class="expiry-hint text-xs text-gray-500 hidden">Required for this product category</span>
                </div>
                <div class="flex items-end justify-end">
                    <button type="button" class="remove_product text-red-600 hover:text-red-800 px-4 py-2 border border-red-300 rounded-lg">Remove</button>
                </div>
            </div>
        </div>`;

    container.insertAdjacentHTML('beforeend', template);
    attachListeners(container.lastElementChild);
    productIndex++;
});

function attachListeners(item) {
    const select = item.querySelector('.product_select');
    if (select) {
        select.addEventListener('change', function() {
            const option = this.options[this.selectedIndex];
            const cost = parseFloat(option.dataset.cost) || 0;
            const selling = parseFloat(option.dataset.selling) || 0;
            const requiresExpiry = option.dataset.requiresExpiry === 'true';

            const costInput = item.querySelector('.product_cost');
            if (costInput && !costInput.value) costInput.value = cost.toFixed(2);

            const sellingInput = item.querySelector('.product_selling');
            if (sellingInput && !sellingInput.value) sellingInput.value = selling.toFixed(2);

            const expiryRequired = item.querySelector('.expiry-required');
            const expiryHint = item.querySelector('.expiry-hint');
            const expiryInput = item.querySelector('.product_expiry');

            if (requiresExpiry) {
                expiryRequired.classList.remove('hidden');
                expiryHint.classList.remove('hidden');
                expiryInput.setAttribute('required', 'required');
            } else {
                expiryRequired.classList.add('hidden');
                expiryHint.classList.add('hidden');
                expiryInput.removeAttribute('required');
            }
        });
    }
}

document.getElementById('products_container').addEventListener('click', function(e) {
    if (e.target.classList.contains('remove_product')) {
        const items = document.querySelectorAll('.product_item');
        if (items.length > 1) {
            e.target.closest('.product_item').remove();
        }
    }
});

document.querySelectorAll('.product_item').forEach(attachListeners);
</script>
@endsection
