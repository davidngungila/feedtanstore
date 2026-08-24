@extends('layouts.app')

@section('page-title', 'Create Stock Request')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-primary-900">Create Stock Request</h1>
        <p class="text-gray-600">Request new stock purchase — you can suggest a supplier and price for each product</p>
    </div>

    <div class="card rounded-2xl p-6">
        <form action="{{ route('storekeeper.purchase-order-requests.store') }}" method="POST" id="request-form">
            @csrf
            
            <!-- Products Section -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-lg font-semibold text-gray-900">Products</h2>
                    <button type="button" id="add-product" class="px-3 py-1.5 text-sm bg-primary-100 text-primary-700 rounded-lg hover:bg-primary-200 transition-colors">
                        <i class="fas fa-plus mr-1"></i> Add Product
                    </button>
                </div>
                
                <div id="products-container" class="space-y-4">
                    <!-- Product rows will be added here -->
                </div>
                
                <p class="text-xs text-gray-500 mt-2">Add multiple products to this single request.</p>
            </div>
            
            <!-- Reason Section -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Reason *</label>
                    <textarea name="reason" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" rows="3" placeholder="Why do you need this stock?" required></textarea>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end gap-4">
                <a href="{{ route('storekeeper.purchase-order-requests') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">Cancel</a>
                <button type="submit" class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">Submit Request</button>
            </div>
        </form>
    </div>
</div>

<script>
let productIndex = 0;
const productsData = @json($products);
const suppliersData = @json($suppliers);

function supplierOptionsHtml(selectedId = '') {
    let html = '<option value="">— No preference —</option>';
    suppliersData.forEach(supplier => {
        const selected = supplier.id == selectedId ? 'selected' : '';
        html += `<option value="${supplier.id}" ${selected}>${supplier.name}</option>`;
    });
    return html;
}

function addProductRow(productId = '', quantity = 1) {
    const container = document.getElementById('products-container');

    let optionsHtml = '<option value="">Select Product</option>';
    productsData.forEach(product => {
        const selected = product.id == productId ? 'selected' : '';
        optionsHtml += `<option value="${product.id}" ${selected}>${product.name} (${product.sku || 'N/A'})</option>`;
    });

    const template = `
        <div class="product-row p-4 border border-gray-200 rounded-lg bg-gray-50" data-index="${productIndex}">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-4 mb-3">
                <div class="md:col-span-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                    <select name="products[${productIndex}][product_id]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 product-select">
                        ${optionsHtml}
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                    <input type="number" name="products[${productIndex}][requested_quantity]" value="${quantity}" min="1" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>
                <div class="md:col-span-3">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Preferred Supplier</label>
                    <select name="products[${productIndex}][supplier_id]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                        ${supplierOptionsHtml()}
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Est. Unit Price</label>
                    <input type="number" step="0.01" min="0" name="products[${productIndex}][estimated_unit_price]" placeholder="0.00" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 unit-price-input">
                </div>
                <div class="md:col-span-1 flex items-end">
                    <button type="button" class="remove-product w-full text-red-600 hover:text-red-800 px-3 py-2 border border-red-300 rounded-lg text-sm" title="Remove">
                        <i class="fas fa-trash"></i> Remove
                    </button>
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Reason for this item</label>
                <textarea name="products[${productIndex}][reason]" rows="2" placeholder="Specific reason for this product (optional)" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm"></textarea>
            </div>
        </div>
    `;

    container.insertAdjacentHTML('beforeend', template);
    productIndex++;
}

function removeProductRow(button) {
    button.closest('.product-row').remove();
    validateForm();
}

function validateForm() {
    const submitBtn = document.querySelector('button[type="submit"]');
    const rows = document.querySelectorAll('.product-row');
    submitBtn.disabled = rows.length === 0;
}

// Initialize with one empty row
document.addEventListener('DOMContentLoaded', function() {
    addProductRow();
    
    document.getElementById('add-product').addEventListener('click', function() {
        addProductRow();
        validateForm();
    });
    
    // Event delegation for remove buttons
    document.getElementById('products-container').addEventListener('click', function(e) {
        if (e.target.closest('.remove-product')) {
            removeProductRow(e.target.closest('.remove-product'));
        }
    });

    // Prefill estimated unit price from product cost price
    document.getElementById('products-container').addEventListener('change', function(e) {
        if (!e.target.classList.contains('product-select')) return;
        const row = e.target.closest('.product-row');
        const priceInput = row.querySelector('.unit-price-input');
        const product = productsData.find(p => p.id == e.target.value);
        if (priceInput && product && product.cost_price != null) {
            priceInput.value = parseFloat(product.cost_price).toFixed(2);
        }
    });

    validateForm();
});
</script>
@endsection