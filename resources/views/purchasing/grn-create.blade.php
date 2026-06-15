@extends('layouts.app')

@section('page-title', 'New Goods Received Note')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">New Goods Received Note</h2>
            <a href="{{ route('purchasing.grn') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to GRNs
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

        <form action="{{ route('purchasing.grn.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Order (Optional)</label>
                    <select name="purchase_order_id" id="purchase_order_select" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Select Purchase Order</option>
                        @foreach($purchaseOrders as $po)
                            <option value="{{ $po->id }}" data-po="{{ json_encode($po) }}" {{ old('purchase_order_id') == $po->id || ($selectedPurchaseOrder && $selectedPurchaseOrder->id == $po->id) ? 'selected' : '' }}>{{ $po->po_number }} - {{ $po->supplier->name ?? 'N/A' }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Received Date *</label>
                    <input type="date" name="received_date" value="{{ old('received_date', date('Y-m-d')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="mb-6">
                <h3 class="text-lg font-semibold text-primary-900 mb-4">Products</h3>
                <div id="products_container">
                    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4 product_item">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                            <select name="products[0][product_id]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_select">
                                <option value="">Select Product</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->cost_price }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                            <input type="number" name="products[0][quantity]" value="1" min="1" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_quantity">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price *</label>
                            <input type="number" step="0.01" name="products[0][unit_price]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_price">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                            <input type="date" name="products[0][expiry_date]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div class="flex items-end">
                            <button type="button" class="remove_product text-red-600 hover:text-red-800 px-4 py-2 border border-red-300 rounded-lg">Remove</button>
                        </div>
                    </div>
                </div>
                <button type="button" id="add_product" class="mt-2 text-primary-600 hover:text-primary-800">
                    <i class="fas fa-plus mr-2"></i>Add Product
                </button>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('purchasing.grn') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Create GRN
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let productIndex = 1;
const productsData = @json($products);

@if($selectedPurchaseOrder)
let selectedPurchaseOrderData = @json($selectedPurchaseOrder);
@endif

document.addEventListener('DOMContentLoaded', function() {
    // If we have a selected purchase order, auto-fill on page load
    if (typeof selectedPurchaseOrderData !== 'undefined') {
        // Set supplier
        if (selectedPurchaseOrderData.supplier_id) {
            document.querySelector('select[name="supplier_id"]').value = selectedPurchaseOrderData.supplier_id;
        }
        
        // Clear existing products
        const container = document.getElementById('products_container');
        container.innerHTML = '';
        productIndex = 0;
        
        // Add products from PO
        selectedPurchaseOrderData.items.forEach((item, index) => {
            addProductItem(item.product_id, item.quantity, item.unit_price);
        });
    }

    document.getElementById('add_product').addEventListener('click', function() {
        addProductItem('', 1, 0);
    });

    document.querySelectorAll('.remove_product').forEach(btn => {
        btn.addEventListener('click', function() {
            this.closest('.product_item').remove();
        });
    });

    document.querySelectorAll('.product_select').forEach(select => {
        select.addEventListener('change', function() {
            const price = this.options[this.selectedIndex].dataset.price || 0;
            this.closest('.product_item').querySelector('.product_price').value = price;
        });
    });

    // Auto-fill from purchase order
    document.getElementById('purchase_order_select').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const poData = JSON.parse(selectedOption.dataset.po);
            
            // Set supplier
            if (poData.supplier_id) {
                document.querySelector('select[name="supplier_id"]').value = poData.supplier_id;
            }
            
            // Clear existing products
            const container = document.getElementById('products_container');
            container.innerHTML = '';
            productIndex = 0;
            
            // Add products from PO
            poData.items.forEach((item, index) => {
                addProductItem(item.product_id, item.quantity, item.unit_price);
            });
        }
    });
});

function addProductItem(productId, quantity, unitPrice) {
    const container = document.getElementById('products_container');
    
    let optionsHtml = '<option value="">Select Product</option>';
    productsData.forEach(product => {
        const selected = product.id == productId ? 'selected' : '';
        optionsHtml += `<option value="${product.id}" data-price="${product.cost_price}" ${selected}>${product.name}</option>`;
    });
    
    const template = `
        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4 product_item">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                <select name="products[${productIndex}][product_id]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_select">
                    ${optionsHtml}
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                <input type="number" name="products[${productIndex}][quantity]" value="${quantity}" min="1" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_quantity">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Unit Price *</label>
                <input type="number" step="0.01" name="products[${productIndex}][unit_price]" value="${unitPrice}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_price">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                <input type="date" name="products[${productIndex}][expiry_date]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            </div>
            <div class="flex items-end">
                <button type="button" class="remove_product text-red-600 hover:text-red-800 px-4 py-2 border border-red-300 rounded-lg">Remove</button>
            </div>
        </div>
    `;
    
    container.innerHTML += template;
    
    // Add event listeners to the new item
    const lastItem = container.lastElementChild;
    
    lastItem.querySelector('.remove_product').addEventListener('click', function() {
        lastItem.remove();
    });
    
    lastItem.querySelector('.product_select').addEventListener('change', function() {
        const price = this.options[this.selectedIndex].dataset.price || 0;
        lastItem.querySelector('.product_price').value = price;
    });
    
    productIndex++;
}

// Event listeners for dynamically added elements
document.getElementById('products_container').addEventListener('click', function(e) {
    if (e.target.classList.contains('remove_product')) {
        e.target.closest('.product_item').remove();
    }
});

document.getElementById('products_container').addEventListener('change', function(e) {
    if (e.target.classList.contains('product_select')) {
        const price = e.target.options[e.target.selectedIndex].dataset.price || 0;
        e.target.closest('.product_item').querySelector('.product_price').value = price;
    }
});
</script>
@endsection
