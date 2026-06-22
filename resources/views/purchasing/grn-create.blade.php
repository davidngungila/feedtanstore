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
                <input type="hidden" name="supplier_id" id="supplier_id_input" value="{{ old('supplier_id') ?? ($selectedPurchaseOrder ? $selectedPurchaseOrder->supplier_id : '') }}">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Order *</label>
                    <select name="purchase_order_id" id="purchase_order_select" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 {{ $selectedPurchaseOrder ? 'bg-gray-100 cursor-not-allowed' : '' }}" {{ $selectedPurchaseOrder ? 'disabled' : '' }}>
                        <option value="">Select Purchase Order</option>
                        @foreach($purchaseOrders as $po)
                            <option value="{{ $po->id }}" data-po="{{ json_encode($po) }}" {{ old('purchase_order_id') == $po->id || ($selectedPurchaseOrder && $selectedPurchaseOrder->id == $po->id) ? 'selected' : '' }}>{{ $po->po_number }} - {{ $po->supplier->name ?? 'N/A' }}</option>
                        @endforeach
                    </select>
                    @if($selectedPurchaseOrder)
                        <input type="hidden" name="purchase_order_id" value="{{ $selectedPurchaseOrder->id }}">
                    @endif
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                    <input type="text" id="supplier_name_input" readonly value="{{ old('supplier_id') ? $suppliers->where('id', old('supplier_id'))->first()->name : ($selectedPurchaseOrder ? $selectedPurchaseOrder->supplier->name : '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 focus:ring-2 focus:ring-primary-500 focus:border-primary-500 cursor-not-allowed">
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
                    @if(!$selectedPurchaseOrder)
                    <div class="product_item mb-6 p-4 border border-gray-200 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
                            <div class="md:col-span-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                                <select name="products[0][product_id]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_select">
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->cost_price }}" data-selling-price="{{ $product->selling_price }}">{{ $product->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                                <input type="number" name="products[0][quantity]" value="1" min="1" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_quantity">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Cost Price *</label>
                                <input type="number" step="0.01" name="products[0][unit_price]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_cost_price">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Pricing Method</label>
                                <select name="products[0][pricing_method]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_pricing_method">
                                    <option value="percentage">Percentage (%)</option>
                                    <option value="flat">Flat Amount</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="button" class="remove_product text-red-600 hover:text-red-800 px-4 py-2 border border-red-300 rounded-lg">Remove</button>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Profit Value</label>
                                <input type="number" step="0.01" name="products[0][profit_value]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_profit_value">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Selling Price *</label>
                                <input type="number" step="0.01" name="products[0][selling_price]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_selling_price">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                                <input type="date" name="products[0][expiry_date]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Profit per Unit</label>
                                <div class="px-4 py-2 bg-green-50 border border-green-200 rounded-lg text-green-800 font-medium product_profit_per_unit">
                                    0.00
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Profit Margin (%)</label>
                                <div class="px-4 py-2 bg-blue-50 border border-blue-200 rounded-lg text-blue-800 font-medium product_profit_percentage">
                                    0.00
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                @if(!$selectedPurchaseOrder)
                <button type="button" id="add_product" class="mt-2 text-primary-600 hover:text-primary-800">
                    <i class="fas fa-plus mr-2"></i>Add Product
                </button>
                @endif
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
const isPoSelected = {{ $selectedPurchaseOrder ? 'true' : 'false' }};

@if($selectedPurchaseOrder)
let selectedPurchaseOrderData = @json($selectedPurchaseOrder);
@endif

function calculateProfit(item) {
    const costPrice = parseFloat(item.querySelector('.product_cost_price').value) || 0;
    const sellingPrice = parseFloat(item.querySelector('.product_selling_price').value) || 0;
    const profitPerUnit = sellingPrice - costPrice;
    const profitPercentage = costPrice > 0 ? ((profitPerUnit / costPrice) * 100) : 0;
    
    item.querySelector('.product_profit_per_unit').textContent = profitPerUnit.toFixed(2);
    item.querySelector('.product_profit_percentage').textContent = profitPercentage.toFixed(2) + '%';
}

function calculateSellingPrice(item) {
    const costPrice = parseFloat(item.querySelector('.product_cost_price').value) || 0;
    const pricingMethod = item.querySelector('.product_pricing_method').value;
    const profitValue = parseFloat(item.querySelector('.product_profit_value').value) || 0;
    
    let sellingPrice;
    if (pricingMethod === 'percentage') {
        sellingPrice = costPrice * (1 + profitValue / 100);
    } else {
        sellingPrice = costPrice + profitValue;
    }
    
    item.querySelector('.product_selling_price').value = sellingPrice.toFixed(2);
    calculateProfit(item);
}

document.addEventListener('DOMContentLoaded', function() {
    // If we have a selected purchase order, auto-fill on page load
    if (typeof selectedPurchaseOrderData !== 'undefined') {
        // Set supplier fields
        const supplierIdInput = document.getElementById('supplier_id_input');
        const supplierNameInput = document.getElementById('supplier_name_input');
        supplierIdInput.value = selectedPurchaseOrderData.supplier_id;
        supplierNameInput.value = selectedPurchaseOrderData.supplier.name;
        
        // Hide add product button
        document.getElementById('add_product').classList.add('hidden');
        
        // Clear existing products
        const container = document.getElementById('products_container');
        container.innerHTML = '';
        productIndex = 0;
        
        // Add products from PO
        selectedPurchaseOrderData.items.forEach((item, index) => {
            addProductItemFromPo(item.product_id, item.quantity, item.unit_price);
        });
    }

    document.getElementById('add_product').addEventListener('click', function() {
        addProductItem('', 1, 0);
    });

    // Event listeners for initial elements
    document.querySelectorAll('.product_item').forEach(item => {
        addProductItemListeners(item);
    });

    // Auto-fill from purchase order
    document.getElementById('purchase_order_select').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        if (selectedOption.value) {
            const poData = JSON.parse(selectedOption.dataset.po);
            
            // Set supplier
            const supplierIdInput = document.getElementById('supplier_id_input');
            const supplierNameInput = document.getElementById('supplier_name_input');
            supplierIdInput.value = poData.supplier_id;
            supplierNameInput.value = poData.supplier.name;
            
            // Clear existing products
            const container = document.getElementById('products_container');
            container.innerHTML = '';
            productIndex = 0;
            
            // Hide add product button
            document.getElementById('add_product').classList.add('hidden');
            
            // Add products from PO
            poData.items.forEach((item, index) => {
                addProductItemFromPo(item.product_id, item.quantity, item.unit_price);
            });
        } else {
            // If no PO selected, reset supplier, show add product button, reset container
            const supplierIdInput = document.getElementById('supplier_id_input');
            const supplierNameInput = document.getElementById('supplier_name_input');
            supplierIdInput.value = '';
            supplierNameInput.value = '';
            
            document.getElementById('add_product').classList.remove('hidden');
            const container = document.getElementById('products_container');
            container.innerHTML = '';
            productIndex = 0;
            addProductItem('', 1, 0);
        }
    });
});

function addProductItemListeners(item) {
    const removeBtn = item.querySelector('.remove_product');
    if (removeBtn) {
        removeBtn.addEventListener('click', function() {
            item.remove();
        });
    }
    
    const productSelect = item.querySelector('.product_select');
    if (productSelect && !productSelect.disabled) {
        productSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const costPrice = selectedOption.dataset.price || 0;
            const sellingPrice = selectedOption.dataset.sellingPrice || 0;
            
            item.querySelector('.product_cost_price').value = costPrice;
            item.querySelector('.product_selling_price').value = sellingPrice;
            calculateProfit(item);
        });
    }
    
    const costPriceInput = item.querySelector('.product_cost_price');
    if (costPriceInput && !costPriceInput.disabled) {
        costPriceInput.addEventListener('input', function() {
            calculateSellingPrice(item);
        });
    }
    
    const pricingMethodSelect = item.querySelector('.product_pricing_method');
    if (pricingMethodSelect && !pricingMethodSelect.disabled) {
        pricingMethodSelect.addEventListener('change', function() {
            calculateSellingPrice(item);
        });
    }
    
    const profitValueInput = item.querySelector('.product_profit_value');
    if (profitValueInput && !profitValueInput.disabled) {
        profitValueInput.addEventListener('input', function() {
            calculateSellingPrice(item);
        });
    }
    
    const sellingPriceInput = item.querySelector('.product_selling_price');
    if (sellingPriceInput && !sellingPriceInput.disabled) {
        sellingPriceInput.addEventListener('input', function() {
            calculateProfit(item);
        });
    }
}

function addProductItemFromPo(productId, orderedQuantity, unitPrice) {
    const container = document.getElementById('products_container');
    
    let optionsHtml = '';
    let selectedProduct = null;
    productsData.forEach(product => {
        const selected = product.id == productId ? 'selected' : '';
        if (product.id == productId) {
            optionsHtml = `<option value="${product.id}" data-price="${product.cost_price}" data-selling-price="${product.selling_price}" ${selected}>${product.name}</option>`;
            selectedProduct = product;
        }
    });
    
    const template = `
        <div class="product_item mb-6 p-4 border border-gray-200 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
                <div class="md:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                    <input type="hidden" name="products[${productIndex}][product_id]" value="${productId}">
                    <select required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_select bg-gray-100 cursor-not-allowed" disabled>
                        ${optionsHtml}
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Ordered Qty *</label>
                    <input type="number" value="${orderedQuantity}" min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Received Qty *</label>
                    <input type="number" name="products[${productIndex}][quantity]" value="${orderedQuantity}" min="1" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_quantity">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cost Price *</label>
                    <input type="number" step="0.01" name="products[${productIndex}][unit_price]" value="${unitPrice}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-100 cursor-not-allowed product_cost_price" readonly>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pricing Method</label>
                    <select name="products[${productIndex}][pricing_method]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_pricing_method">
                        <option value="percentage">Percentage (%)</option>
                        <option value="flat">Flat Amount</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Profit Value</label>
                    <input type="number" step="0.01" name="products[${productIndex}][profit_value]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_profit_value">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Selling Price *</label>
                    <input type="number" step="0.01" name="products[${productIndex}][selling_price]" value="${selectedProduct ? selectedProduct.selling_price : 0}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_selling_price">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                    <input type="date" name="products[${productIndex}][expiry_date]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Profit per Unit</label>
                    <div class="px-4 py-2 bg-green-50 border border-green-200 rounded-lg text-green-800 font-medium product_profit_per_unit">
                        0.00
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Profit Margin (%)</label>
                    <div class="px-4 py-2 bg-blue-50 border border-blue-200 rounded-lg text-blue-800 font-medium product_profit_percentage">
                        0.00
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.innerHTML += template;
    
    // Add event listeners to the new item
    const lastItem = container.lastElementChild;
    addProductItemListeners(lastItem);
    
    // Calculate initial profit
    calculateProfit(lastItem);
    
    productIndex++;
}

function addProductItem(productId, quantity, unitPrice) {
    const container = document.getElementById('products_container');
    
    let optionsHtml = '<option value="">Select Product</option>';
    let selectedProduct = null;
    productsData.forEach(product => {
        const selected = product.id == productId ? 'selected' : '';
        optionsHtml += `<option value="${product.id}" data-price="${product.cost_price}" data-selling-price="${product.selling_price}" ${selected}>${product.name}</option>`;
        if (product.id == productId) {
            selectedProduct = product;
        }
    });
    
    const template = `
        <div class="product_item mb-6 p-4 border border-gray-200 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-4">
                <div class="md:col-span-1">
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
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cost Price *</label>
                    <input type="number" step="0.01" name="products[${productIndex}][unit_price]" value="${unitPrice}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_cost_price">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pricing Method</label>
                    <select name="products[${productIndex}][pricing_method]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_pricing_method">
                        <option value="percentage">Percentage (%)</option>
                        <option value="flat">Flat Amount</option>
                    </select>
                </div>
                <div class="flex items-end">
                    <button type="button" class="remove_product text-red-600 hover:text-red-800 px-4 py-2 border border-red-300 rounded-lg">Remove</button>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Profit Value</label>
                    <input type="number" step="0.01" name="products[${productIndex}][profit_value]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_profit_value">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Selling Price *</label>
                    <input type="number" step="0.01" name="products[${productIndex}][selling_price]" value="${selectedProduct ? selectedProduct.selling_price : 0}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product_selling_price">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                    <input type="date" name="products[${productIndex}][expiry_date]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Profit per Unit</label>
                    <div class="px-4 py-2 bg-green-50 border border-green-200 rounded-lg text-green-800 font-medium product_profit_per_unit">
                        0.00
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Profit Margin (%)</label>
                    <div class="px-4 py-2 bg-blue-50 border border-blue-200 rounded-lg text-blue-800 font-medium product_profit_percentage">
                        0.00
                    </div>
                </div>
            </div>
        </div>
    `;
    
    container.innerHTML += template;
    
    // Add event listeners to the new item
    const lastItem = container.lastElementChild;
    addProductItemListeners(lastItem);
    
    // Calculate initial profit
    calculateProfit(lastItem);
    
    productIndex++;
}

// Event listeners for dynamically added elements
document.getElementById('products_container').addEventListener('click', function(e) {
    if (e.target.classList.contains('remove_product')) {
        e.target.closest('.product_item').remove();
    }
});
</script>
@endsection