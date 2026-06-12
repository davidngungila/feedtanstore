@extends('layouts.app')

@section('page-title', 'New Sale')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Product Selection -->
        <div class="lg:col-span-2">
            <div class="card rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4 flex-wrap gap-2">
                    <h2 class="text-xl font-bold text-primary-900">Products</h2>
                    <div class="flex-1 max-w-md">
                        <div class="relative">
                            <input type="text" id="productSearch" placeholder="Search products..." class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        </div>
                    </div>
                </div>
                <div id="productGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-6">
                    @foreach($products as $product)
                    <div class="product-card border border-gray-200 rounded-xl p-4 {{ $product->quantity > 0 ? 'cursor-pointer hover:border-primary-500' : 'opacity-50 cursor-not-allowed border-gray-300 bg-gray-50' }}" data-name="{{ strtolower($product->name) }}" onclick="{{ $product->quantity > 0 ? "addToCart({$product->id}, '" . addslashes($product->name) . "', {$product->selling_price})" : '' }}">
                        <p class="font-semibold text-primary-900">{{ $product->name }}</p>
                        <p class="text-sm text-gray-600">TZS {{ number_format($product->selling_price, 2) }}</p>
                        <p class="text-xs {{ $product->quantity > 0 ? 'text-gray-500' : 'text-red-500 font-bold' }}">
                            Stock: {{ $product->quantity }}
                            @if($product->quantity <= 0)
                                (Out of Stock)
                            @endif
                        </p>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Cart & Payment -->
        <div class="lg:col-span-1">
            <div class="card rounded-2xl p-6 sticky top-6">
                <h2 class="text-xl font-bold text-primary-900 mb-4">Cart</h2>
                
                <form id="saleForm" action="{{ route('sales.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Customer</label>
                        <select name="customer_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="">Walk-in Customer</option>
                            @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Discount</label>
                        <select name="discount_id" id="discountSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg" onchange="handleDiscountChange()">
                            <option value="">No Discount</option>
                            @foreach($discounts as $discount)
                            <option value="{{ $discount->id }}" data-type="{{ $discount->type }}" data-value="{{ $discount->value }}" data-min="{{ $discount->min_amount }}" data-max="{{ $discount->max_amount }}">
                                {{ $discount->name }} ({{ $discount->type == 'percentage' ? $discount->value . '%' : 'TZS ' . number_format($discount->value, 2) }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="cartItems" class="mb-4 max-h-96 overflow-y-auto">
                        <!-- Cart items will be added here -->
                    </div>

                    <div class="border-t pt-4 mb-4">
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Subtotal:</span>
                            <span id="subtotal" class="font-semibold">TZS 0.00</span>
                        </div>
                        <div class="flex justify-between mb-2">
                            <span class="text-gray-600">Discount:</span>
                            <span id="discountAmount" class="font-semibold text-red-600">-TZS 0.00</span>
                        </div>
                        <div class="flex justify-between mb-2 text-lg font-bold">
                            <span>Total:</span>
                            <span id="total">TZS 0.00</span>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                        <select name="payment_method" id="paymentMethod" class="w-full px-4 py-2 border border-gray-300 rounded-lg">
                            <option value="cash">Cash</option>
                            <option value="card">Card</option>
                            <option value="mobile">Mobile Money</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Sale Type</label>
                        <select name="type" id="saleType" class="w-full px-4 py-2 border border-gray-300 rounded-lg" onchange="handleSaleTypeChange()">
                            <option value="cash">Cash Sale</option>
                            <option value="credit">Credit Sale</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Paid Amount</label>
                        <input type="number" name="paid" id="paidAmount" value="0" min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg" onchange="handlePaidChange()">
                    </div>

                    <div id="paidNoteContainer" class="mb-4 hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reason for Paid Amount Change</label>
                        <textarea name="paid_note" id="paidNote" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg" placeholder="Explain why you're changing the paid amount..."></textarea>
                    </div>

                    <div class="flex justify-between mb-4 text-lg font-bold">
                        <span>Change:</span>
                        <span id="change">TZS 0.00</span>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="flex-1 bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded-lg transition-colors">
                            Complete Sale
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
let cart = [];
let originalPaidAmount = 0;
let lastCalculatedTotal = 0;
let currentDiscount = null;

// Product Search
document.getElementById('productSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const cards = document.querySelectorAll('.product-card');
    
    cards.forEach(card => {
        const productName = card.getAttribute('data-name');
        if (productName.includes(searchTerm)) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});

function addToCart(productId, productName, price) {
    const existingItem = cart.find(item => item.product_id === productId);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            product_id: productId,
            name: productName,
            quantity: 1,
            unit_price: price
        });
    }
    
    renderCart();
    updateTotals();
}

function removeFromCart(index) {
    cart.splice(index, 1);
    renderCart();
    updateTotals();
}

function updateQuantity(index, quantity) {
    cart[index].quantity = Math.max(1, quantity);
    renderCart();
    updateTotals();
}

function renderCart() {
    const container = document.getElementById('cartItems');
    container.innerHTML = cart.map((item, index) => `
        <div class="flex items-center justify-between py-2 border-b border-gray-100">
            <div class="flex-1">
                <p class="font-medium text-primary-900">${item.name}</p>
                <p class="text-sm text-gray-600">TZS ${item.unit_price.toFixed(2)}</p>
            </div>
            <div class="flex items-center gap-2">
                <input type="number" value="${item.quantity}" min="1" 
                    onchange="updateQuantity(${index}, this.value)"
                    class="w-16 px-2 py-1 border border-gray-300 rounded text-center">
                <input type="hidden" name="items[${index}][product_id]" value="${item.product_id}">
                <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
                <input type="hidden" name="items[${index}][unit_price]" value="${item.unit_price}">
                <button type="button" onclick="removeFromCart(${index})" class="text-red-500 hover:text-red-700">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        </div>
    `).join('');
}

function handleDiscountChange() {
    const select = document.getElementById('discountSelect');
    const selectedOption = select.options[select.selectedIndex];
    
    if (selectedOption.value) {
        currentDiscount = {
            type: selectedOption.dataset.type,
            value: parseFloat(selectedOption.dataset.value),
            minAmount: selectedOption.dataset.min ? parseFloat(selectedOption.dataset.min) : null,
            maxAmount: selectedOption.dataset.max ? parseFloat(selectedOption.dataset.max) : null
        };
    } else {
        currentDiscount = null;
    }
    
    updateTotals();
}

function calculateDiscount(subtotal) {
    if (!currentDiscount) {
        return 0;
    }
    
    if ((currentDiscount.minAmount && subtotal < currentDiscount.minAmount) ||
        (currentDiscount.maxAmount && subtotal > currentDiscount.maxAmount)) {
        return 0;
    }
    
    if (currentDiscount.type === 'percentage') {
        return subtotal * (currentDiscount.value / 100);
    } else {
        return currentDiscount.value;
    }
}

function updateTotals() {
    let subtotal = 0;
    cart.forEach(item => {
        subtotal += item.quantity * item.unit_price;
    });
    
    const discount = calculateDiscount(subtotal);
    lastCalculatedTotal = subtotal - discount;
    
    // Auto-fill paid amount for cash sales
    const saleType = document.getElementById('saleType').value;
    if (saleType === 'cash') {
        const paidInput = document.getElementById('paidAmount');
        if (parseFloat(paidInput.value) === originalPaidAmount || originalPaidAmount === 0) {
            paidInput.value = lastCalculatedTotal.toFixed(2);
            originalPaidAmount = lastCalculatedTotal;
        }
    }
    
    const paid = parseFloat(document.getElementById('paidAmount').value) || 0;
    const change = Math.max(0, paid - lastCalculatedTotal);
    
    document.getElementById('subtotal').textContent = 'TZS ' + subtotal.toFixed(2);
    document.getElementById('discountAmount').textContent = '-TZS ' + discount.toFixed(2);
    document.getElementById('total').textContent = 'TZS ' + lastCalculatedTotal.toFixed(2);
    document.getElementById('change').textContent = 'TZS ' + change.toFixed(2);
}

function handleSaleTypeChange() {
    updateTotals();
}

function handlePaidChange() {
    const currentPaid = parseFloat(document.getElementById('paidAmount').value) || 0;
    const noteContainer = document.getElementById('paidNoteContainer');
    
    if (currentPaid !== originalPaidAmount && originalPaidAmount !== 0) {
        noteContainer.classList.remove('hidden');
        document.getElementById('paidNote').required = true;
    } else {
        noteContainer.classList.add('hidden');
        document.getElementById('paidNote').required = false;
    }
}
</script>
@endsection
