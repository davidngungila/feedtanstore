@extends('layouts.app')

@section('page-title', 'Cashier Dashboard')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <!-- Header with Logo -->
    <div class="bg-white rounded-2xl shadow-sm p-4 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                @if($storeSetting->logo)
                    <img src="{{ Storage::url($storeSetting->logo) }}" alt="{{ $storeSetting->store_name }}" class="h-16 w-auto object-contain">
                @else
                    <div class="h-16 w-16 bg-gradient-to-br from-primary-400 to-primary-700 rounded-xl flex items-center justify-center text-white font-bold text-2xl">
                        {{ strtoupper(substr($storeSetting->store_name, 0, 1)) }}
                    </div>
                @endif
                <div>
                    <h1 class="text-2xl font-bold text-primary-900">{{ $storeSetting->store_name }}</h1>
                    <p class="text-sm text-gray-500">Point of Sale</p>
                </div>
            </div>
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-xs text-gray-500">Date & Time</p>
                    <p class="font-semibold text-primary-800" id="currentDateTime"></p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Products & Cart -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Product Search & Quick Grid -->
            <div class="card rounded-2xl p-6">
                <h2 class="text-xl font-bold text-primary-900 mb-4">
                    <i class="fas fa-boxes mr-2"></i>Products
                </h2>
                
                <!-- Search & Scan -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div class="relative">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="productSearch" placeholder="Search products by name, barcode, or SKU..." 
                               class="w-full pl-10 pr-4 py-3 border border-primary-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 bg-primary-50/50">
                    </div>
                    <div class="relative">
                        <i class="fas fa-barcode absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="barcodeScanner" placeholder="Scan barcode here..." 
                               class="w-full pl-10 pr-4 py-3 border border-green-400 rounded-xl focus:outline-none focus:ring-2 focus:ring-green-500 bg-green-50/50" autofocus>
                    </div>
                </div>

                <!-- Search Results -->
                <div id="searchResults" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 mb-4"></div>

                <!-- Product Grid -->
                <div id="productGrid" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 max-h-64 overflow-y-auto">
                    @foreach($products as $product)
                    <div class="border border-gray-200 rounded-xl p-3 hover:border-primary-500 hover:shadow-md cursor-pointer transition-all bg-white" 
                         onclick="addToCart({{ $product->id }}, '{{ addslashes($product->name) }}', {{ $product->selling_price }})">
                        <h3 class="font-semibold text-primary-900 text-sm truncate">{{ $product->name }}</h3>
                        <p class="text-xs text-gray-500 mt-1">
                            Stock: {{ $product->quantity }}
                        </p>
                        <p class="text-lg font-bold text-primary-700 mt-2">TZS {{ number_format($product->selling_price, 2) }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Cart -->
            <div class="card rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-primary-900">
                        <i class="fas fa-shopping-cart mr-2"></i>Cart
                    </h2>
                    <button onclick="clearCart()" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-lg text-sm font-medium transition-colors">
                        <i class="fas fa-trash mr-1"></i>Clear
                    </button>
                </div>

                <!-- Cart Items -->
                <div id="cartItems" class="space-y-2 max-h-64 overflow-y-auto mb-4"></div>

                <!-- Customer Selection -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Optional Customer</label>
                    <select id="customerSelect" class="w-full px-3 py-2 border border-gray-300 rounded-lg">
                        <option value="">Walk-in Customer</option>
                        @foreach($customers as $customer)
                            <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone ?? 'No phone' }})</option>
                        @endforeach
                    </select>
                </div>

                <!-- Totals -->
                <div class="space-y-2 border-t pt-4">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span class="font-semibold" id="subtotal">TZS 0.00</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <label class="text-gray-600">Discount</label>
                        <div class="flex items-center gap-2">
                            <input type="number" id="discountInput" placeholder="0" min="0" step="0.01" 
                                   class="w-28 px-3 py-1 border border-gray-300 rounded-lg text-right" 
                                   onchange="updateTotals()">
                            <span id="discountAmount" class="font-semibold text-red-600">-TZS 0.00</span>
                        </div>
                    </div>
                    <div class="flex items-center justify-between text-2xl font-bold text-primary-800">
                        <span>Total</span>
                        <span id="total">TZS 0.00</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Payment & Quick Actions -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="card rounded-2xl p-6">
                <h2 class="text-lg font-bold text-primary-900 mb-4">
                    <i class="fas fa-bolt mr-2"></i>Quick Actions
                </h2>
                <div class="grid grid-cols-2 gap-3">
                    <button onclick="holdSale()" class="px-4 py-3 border border-orange-300 bg-orange-50 hover:bg-orange-100 text-orange-800 rounded-xl font-medium transition-colors">
                        <i class="fas fa-pause mr-1"></i>Hold
                    </button>
                    <button onclick="voidSale()" class="px-4 py-3 border border-red-300 bg-red-50 hover:bg-red-100 text-red-800 rounded-xl font-medium transition-colors">
                        <i class="fas fa-times mr-1"></i>Void
                    </button>
                </div>
            </div>

            <!-- Payment Panel -->
            <div class="card rounded-2xl p-6">
                <h2 class="text-xl font-bold text-primary-900 mb-4">
                    <i class="fas fa-credit-card mr-2"></i>Payment
                </h2>
                
                <!-- Total Display -->
                <div class="bg-gradient-to-r from-primary-500 to-primary-700 rounded-xl p-6 text-center mb-6 text-white">
                    <p class="text-sm opacity-90 mb-1">TOTAL AMOUNT</p>
                    <p class="text-4xl font-bold" id="paymentTotal">TZS 0.00</p>
                </div>

                <!-- Paid Amount -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Paid Amount</label>
                    <input type="number" id="paidAmount" step="0.01" min="0" 
                           class="w-full px-4 py-3 border border-primary-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 text-xl font-bold text-center"
                           oninput="calculateChange()">
                </div>

                <!-- Quick Cash Buttons -->
                <div class="grid grid-cols-4 gap-2 mb-4">
                    <button onclick="setPaid(5000)" class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 font-medium text-sm">5k</button>
                    <button onclick="setPaid(10000)" class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 font-medium text-sm">10k</button>
                    <button onclick="setPaid(20000)" class="px-3 py-2 border border-gray-300 rounded-lg hover:bg-gray-50 font-medium text-sm">20k</button>
                    <button onclick="setPaidAmountToTotal()" class="px-3 py-2 border border-primary-600 bg-primary-50 text-primary-800 rounded-lg hover:bg-primary-100 font-medium text-sm">Exact</button>
                </div>

                <!-- Change -->
                <div class="mb-4 p-4 bg-green-50 rounded-xl">
                    <div class="flex items-center justify-between">
                        <span class="text-green-800 font-medium">Change</span>
                        <span class="text-2xl font-bold text-green-700" id="changeAmount">TZS 0.00</span>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                    <div class="grid grid-cols-3 gap-2">
                        <button onclick="selectPayment('cash')" 
                                id="method-cash"
                                class="payment-method py-3 border-2 border-primary-600 bg-primary-600 text-white rounded-xl font-medium transition-colors">
                            <i class="fas fa-money-bill mr-1"></i>Cash
                        </button>
                        <button onclick="selectPayment('card')" 
                                id="method-card"
                                class="payment-method py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:border-primary-500 transition-colors">
                            <i class="fas fa-credit-card mr-1"></i>Card
                        </button>
                        <button onclick="selectPayment('mobile')" 
                                id="method-mobile"
                                class="payment-method py-3 border-2 border-gray-300 text-gray-700 rounded-xl font-medium hover:border-primary-500 transition-colors">
                            <i class="fas fa-mobile-alt mr-1"></i>Mobile
                        </button>
                    </div>
                </div>

                <!-- Transaction ID for non-cash -->
                <div id="transactionIdDiv" class="mb-4 hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Transaction ID <span class="text-red-500">*</span></label>
                    <input type="text" id="transactionIdInput" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                </div>

                <!-- Complete Sale Button -->
                <button onclick="completeSale()" 
                        id="completeSaleBtn"
                        class="w-full py-4 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold text-lg transition-colors shadow-lg">
                    <i class="fas fa-check mr-2"></i>Complete Sale
                </button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div id="successModal" class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center hidden z-50">
        <div class="bg-white rounded-3xl p-8 max-w-md w-full mx-4 text-center">
            <div class="w-24 h-24 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <i class="fas fa-check-circle text-6xl text-green-600"></i>
            </div>
            <h2 class="text-3xl font-bold text-primary-900 mb-2">Sale Complete!</h2>
            <p class="text-gray-600 mb-6" id="saleNumberText">Sale #SAL-123456789</p>
            <div class="grid grid-cols-2 gap-4 text-left mb-6">
                <div class="p-4 bg-gray-50 rounded-xl">
                    <p class="text-sm text-gray-500">Total</p>
                    <p class="text-xl font-bold text-primary-800" id="modalTotal">TZS 0.00</p>
                </div>
                <div class="p-4 bg-gray-50 rounded-xl">
                    <p class="text-sm text-gray-500">Change</p>
                    <p class="text-xl font-bold text-green-700" id="modalChange">TZS 0.00</p>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <button onclick="printReceipt()" class="py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-semibold transition-colors">
                    <i class="fas fa-print mr-1"></i>Print Receipt
                </button>
                <button onclick="newSale()" class="py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 rounded-xl font-semibold transition-colors">
                    <i class="fas fa-plus mr-1"></i>New Sale
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let cart = [];
let selectedPaymentMethod = 'cash';
let currentSaleId = null;
let productsData = @json($products);

document.addEventListener('DOMContentLoaded', () => {
    updateDateTime();
    setInterval(updateDateTime, 1000);
    setupBarcodeScanner();
    setupProductSearch();
    renderProductGrid();
});

function updateDateTime() {
    const now = new Date();
    const options = { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric', 
        hour: '2-digit', 
        minute: '2-digit', 
        second: '2-digit' 
    };
    document.getElementById('currentDateTime').textContent = now.toLocaleDateString('en-US', options);
}

function setupBarcodeScanner() {
    let buffer = '';
    let lastTime = 0;
    
    document.addEventListener('keypress', (e) => {
        const now = Date.now();
        if (now - lastTime > 100) buffer = '';
        lastTime = now;
        
        if (e.key === 'Enter') {
            if (buffer.length > 0) {
                scanProduct(buffer.trim());
            } else {
                const el = document.getElementById('barcodeScanner');
                if (el && el.value) {
                    scanProduct(el.value.trim());
                    el.value = '';
                }
            }
            buffer = '';
        } else {
            buffer += e.key;
        }
    });
    
    document.getElementById('barcodeScanner').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            e.preventDefault();
            scanProduct(e.target.value.trim());
            e.target.value = '';
        }
    });
}

function setupProductSearch() {
    const input = document.getElementById('productSearch');
    const resultsDiv = document.getElementById('searchResults');
    
    input.addEventListener('input', () => {
        const term = input.value.toLowerCase();
        if (term.length < 2) {
            resultsDiv.innerHTML = '';
            resultsDiv.classList.add('hidden');
            return;
        }
        
        const filtered = productsData.filter(p => 
            p.name.toLowerCase().includes(term) || 
            (p.barcode && p.barcode.includes(term)) || 
            (p.sku && p.sku.includes(term))
        );
        
        if (filtered.length > 0) {
            resultsDiv.innerHTML = filtered.map(p => `
                <div class="border border-gray-200 rounded-xl p-3 hover:border-primary-500 hover:shadow-md cursor-pointer transition-all bg-white" 
                     onclick="addToCart(${p.id}, '${p.name.replace(/'/g, "\\'")}', ${p.selling_price})">
                    <h3 class="font-semibold text-primary-900 text-sm">${p.name}</h3>
                    <p class="text-xs text-gray-500">Stock: ${p.quantity}</p>
                    <p class="font-bold text-primary-700">TZS ${p.selling_price.toFixed(2)}</p>
                </div>
            `).join('');
            resultsDiv.classList.remove('hidden');
        } else {
            resultsDiv.innerHTML = '<p class="text-gray-500 col-span-full text-center py-4">No products found</p>';
            resultsDiv.classList.remove('hidden');
        }
    });
}

function scanProduct(barcode) {
    fetch(`/cashier/product/${encodeURIComponent(barcode)}`)
        .then(r => r.json())
        .then(product => {
            if (product.error) {
                showNotification(product.error, 'error');
            } else {
                addToCart(product.id, product.name, product.selling_price);
            }
        })
        .catch(err => {
            showNotification('Error scanning product', 'error');
        });
}

function addToCart(id, name, price) {
    const existing = cart.find(item => item.id === id);
    if (existing) {
        existing.quantity += 1;
    } else {
        cart.push({ id, name, price, quantity: 1 });
    }
    renderCart();
    showNotification(`${name} added to cart!`, 'success');
}

function renderCart() {
    const container = document.getElementById('cartItems');
    if (cart.length === 0) {
        container.innerHTML = `
            <div class="text-center py-8 text-gray-400">
                <i class="fas fa-shopping-cart text-4xl mb-3"></i>
                <p>Cart is empty</p>
            </div>
        `;
    } else {
        container.innerHTML = cart.map((item, index) => `
            <div class="flex items-center gap-3 p-3 bg-gray-50 rounded-xl">
                <div class="flex-1">
                    <p class="font-semibold text-primary-900">${item.name}</p>
                    <p class="text-sm text-gray-600">TZS ${item.price.toFixed(2)} each</p>
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="updateQty(${index}, -1)" 
                            class="w-8 h-8 flex items-center justify-center bg-gray-200 hover:bg-gray-300 rounded-full font-medium">
                        <i class="fas fa-minus text-xs"></i>
                    </button>
                    <span class="font-bold w-8 text-center">${item.quantity}</span>
                    <button onclick="updateQty(${index}, 1)" 
                            class="w-8 h-8 flex items-center justify-center bg-gray-200 hover:bg-gray-300 rounded-full font-medium">
                        <i class="fas fa-plus text-xs"></i>
                    </button>
                </div>
                <div class="text-right">
                    <p class="font-bold text-primary-900">TZS ${(item.price * item.quantity).toFixed(2)}</p>
                    <button onclick="removeItem(${index})" class="text-xs text-red-600 hover:text-red-800">Remove</button>
                </div>
            </div>
        `).join('');
    }
    updateTotals();
}

function updateQty(index, delta) {
    cart[index].quantity += delta;
    if (cart[index].quantity <= 0) {
        cart.splice(index, 1);
    }
    renderCart();
}

function removeItem(index) {
    cart.splice(index, 1);
    renderCart();
}

function clearCart() {
    if (cart.length === 0 || confirm('Are you sure you want to clear the cart?')) {
        cart = [];
        renderCart();
    }
}

function updateTotals() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const discount = parseFloat(document.getElementById('discountInput').value) || 0;
    const total = subtotal - discount;
    document.getElementById('subtotal').textContent = `TZS ${subtotal.toFixed(2)}`;
    document.getElementById('discountAmount').textContent = `-TZS ${discount.toFixed(2)}`;
    document.getElementById('total').textContent = `TZS ${total.toFixed(2)}`;
    document.getElementById('paymentTotal').textContent = `TZS ${total.toFixed(2)}`;
    calculateChange();
}

function setPaid(amount) {
    document.getElementById('paidAmount').value = amount;
    calculateChange();
}

function setPaidAmountToTotal() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const discount = parseFloat(document.getElementById('discountInput').value) || 0;
    document.getElementById('paidAmount').value = (subtotal - discount).toFixed(2);
    calculateChange();
}

function calculateChange() {
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const discount = parseFloat(document.getElementById('discountInput').value) || 0;
    const total = subtotal - discount;
    const paid = parseFloat(document.getElementById('paidAmount').value) || 0;
    const change = Math.max(0, paid - total);
    document.getElementById('changeAmount').textContent = `TZS ${change.toFixed(2)}`;
}

function selectPayment(method) {
    selectedPaymentMethod = method;
    document.querySelectorAll('.payment-method').forEach(btn => {
        btn.classList.remove('border-primary-600', 'bg-primary-600', 'text-white');
        btn.classList.add('border-gray-300', 'text-gray-700');
    });
    document.getElementById(`method-${method}`).classList.add('border-primary-600', 'bg-primary-600', 'text-white');
    document.getElementById(`method-${method}`).classList.remove('border-gray-300', 'text-gray-700');
    
    if (method === 'cash') {
        document.getElementById('transactionIdDiv').classList.add('hidden');
    } else {
        document.getElementById('transactionIdDiv').classList.remove('hidden');
    }
}

function completeSale() {
    if (cart.length === 0) {
        showNotification('Cart is empty!', 'error');
        return;
    }
    
    const subtotal = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    const discount = parseFloat(document.getElementById('discountInput').value) || 0;
    const total = subtotal - discount;
    const paid = parseFloat(document.getElementById('paidAmount').value) || 0;
    
    if (paid < total) {
        showNotification('Paid amount is insufficient!', 'error');
        return;
    }
    
    if (selectedPaymentMethod !== 'cash' && !document.getElementById('transactionIdInput').value) {
        showNotification('Please enter a transaction ID!', 'error');
        return;
    }
    
    const btn = document.getElementById('completeSaleBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
    
    fetch('/cashier/sale', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            items: cart,
            total: total,
            discount: discount,
            paid: paid,
            payment_method: selectedPaymentMethod,
            transaction_id: document.getElementById('transactionIdInput').value,
            customer_id: document.getElementById('customerSelect').value || null
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.error) {
            showNotification(data.error, 'error');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-check mr-2"></i>Complete Sale';
        } else {
            currentSaleId = data.sale.id;
            document.getElementById('saleNumberText').textContent = `Sale #${data.sale_number}`;
            document.getElementById('modalTotal').textContent = `TZS ${total.toFixed(2)}`;
            document.getElementById('modalChange').textContent = `TZS ${data.change.toFixed(2)}`;
            document.getElementById('successModal').classList.remove('hidden');
        }
    })
    .catch(err => {
        showNotification('Error completing sale', 'error');
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-check mr-2"></i>Complete Sale';
    });
}

function newSale() {
    cart = [];
    currentSaleId = null;
    document.getElementById('successModal').classList.add('hidden');
    document.getElementById('paidAmount').value = '';
    document.getElementById('discountInput').value = '';
    document.getElementById('transactionIdInput').value = '';
    document.getElementById('customerSelect').value = '';
    selectPayment('cash');
    const btn = document.getElementById('completeSaleBtn');
    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-check mr-2"></i>Complete Sale';
    renderCart();
    document.getElementById('barcodeScanner').focus();
}

function printReceipt() {
    if (currentSaleId) {
        window.open(`/cashier/receipt/${currentSaleId}`, '_blank');
    }
}

function holdSale() {
    showNotification('Hold sale feature coming soon!', 'info');
}

function voidSale() {
    if (confirm('Are you sure you want to void this sale?')) {
        clearCart();
        showNotification('Sale voided!', 'info');
    }
}

function renderProductGrid() {
    const grid = document.getElementById('productGrid');
    grid.innerHTML = productsData.map(product => `
        <div class="border border-gray-200 rounded-xl p-3 hover:border-primary-500 hover:shadow-md cursor-pointer transition-all bg-white" 
             onclick="addToCart(${product.id}, '${product.name.replace(/'/g, "\\'")}', ${product.selling_price})">
            <h3 class="font-semibold text-primary-900 text-sm truncate">${product.name}</h3>
            <p class="text-xs text-gray-500 mt-1">Stock: ${product.quantity}</p>
            <p class="text-lg font-bold text-primary-700 mt-2">TZS ${product.selling_price.toFixed(2)}</p>
        </div>
    `).join('');
}

function showNotification(msg, type = 'info') {
    const colors = {
        success: 'bg-green-600 text-white',
        error: 'bg-red-600 text-white',
        info: 'bg-blue-600 text-white'
    };
    
    const notif = document.createElement('div');
    notif.className = `fixed bottom-4 right-4 z-50 px-6 py-3 rounded-2xl shadow-2xl ${colors[type]} font-medium flex items-center gap-2`;
    notif.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        ${msg}
    `;
    document.body.appendChild(notif);
    
    setTimeout(() => {
        notif.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
        notif.style.opacity = '0';
        notif.style.transform = 'translateY(10px)';
        setTimeout(() => notif.remove(), 300);
    }, 3000);
}
</script>
@endsection
