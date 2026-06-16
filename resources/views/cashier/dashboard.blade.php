@extends('layouts.app')

@section('page-title', 'Cashier Dashboard')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Scan, Search & Cart -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Scan & Search -->
            <div class="card rounded-2xl p-6">
                <h2 class="text-xl font-bold text-primary-900 mb-4">Scan & Search Products</h2>
                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-700"><i class="fas fa-barcode mr-2"></i> Scan barcode anywhere on this page to add product to cart automatically!</p>
                </div>
                <div class="flex gap-2 items-center mb-4">
                    <div class="flex-1 relative">
                        <i class="fas fa-barcode absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                        <input type="text" id="barcodeInput" placeholder="Scan Barcode..." class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500 text-lg" autofocus>
                    </div>
                    <div class="text-green-600 font-medium scan-indicator">
                        <i class="fas fa-circle mr-2"></i>Scan Ready
                    </div>
                </div>
                <div class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="searchProduct" placeholder="Search Products..." class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500">
                    <div id="searchResults" class="absolute w-full bg-white border border-gray-300 rounded-xl mt-1 shadow-lg max-h-96 overflow-y-auto hidden z-50"></div>
                </div>
            </div>

            <!-- Cart -->
            <div class="card rounded-2xl p-6">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xl font-bold text-primary-900">Cart</h2>
                    <button type="button" onclick="clearCart()" class="px-4 py-2 text-red-600 hover:bg-red-50 rounded-lg font-medium">
                        <i class="fas fa-trash mr-2"></i>Clear
                    </button>
                </div>
                <div id="cartItems" class="mb-4 max-h-96 overflow-y-auto">
                    <!-- Cart items will be added here -->
                </div>
                <div class="border-t pt-4">
                    <div class="flex justify-between mb-2">
                        <span class="text-gray-600">Subtotal:</span>
                        <span id="subtotal" class="font-semibold">TZS 0.00</span>
                    </div>
                    <div class="flex justify-between mb-2 items-center">
                        <label class="text-gray-600">Discount:</label>
                        <div class="flex items-center gap-2">
                            <input type="number" id="discountInput" placeholder="0" class="w-32 px-3 py-1 border border-gray-300 rounded-lg" onchange="updateTotals()">
                            <span id="discountAmount" class="font-semibold text-red-600">-TZS 0.00</span>
                        </div>
                    </div>
                    <div class="flex justify-between mb-2 text-lg font-bold">
                        <span>Total:</span>
                        <span id="total">TZS 0.00</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment & Quick Actions -->
        <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="card rounded-2xl p-6">
                <h2 class="text-xl font-bold text-primary-900 mb-4">Quick Actions</h2>
                <div class="grid grid-cols-2 gap-2">
                    <button type="button" class="py-3 border border-gray-300 rounded-xl hover:bg-gray-50 text-gray-700 font-medium">
                        <i class="fas fa-pause mr-2"></i>Hold
                    </button>
                    <button type="button" class="py-3 border border-gray-300 rounded-xl hover:bg-gray-50 text-gray-700 font-medium" onclick="clearCart()">
                        <i class="fas fa-times mr-2"></i>Cancel
                    </button>
                </div>
            </div>

            <!-- Payment Panel -->
            <div class="card rounded-2xl p-6 sticky top-6">
                <h2 class="text-xl font-bold text-primary-900 mb-4">Payment</h2>
                <div class="mb-4 p-4 bg-primary-50 rounded-xl text-center">
                    <p class="text-sm text-primary-600">TOTAL</p>
                    <p class="text-3xl font-bold text-primary-800" id="paymentTotal">TZS 0.00</p>
                </div>
                <div class="space-y-3 mb-4">
                    <label class="block">
                        <span class="text-gray-700 font-medium mb-1">Paid Amount</span>
                        <input type="number" id="paidAmount" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500" oninput="calculateChange()" placeholder="Enter amount paid">
                    </label>
                    <div class="grid grid-cols-3 gap-2">
                        <button type="button" class="py-2 border border-gray-300 rounded-xl hover:bg-gray-50" onclick="setPaidAmount(5000)">5k</button>
                        <button type="button" class="py-2 border border-gray-300 rounded-xl hover:bg-gray-50" onclick="setPaidAmount(10000)">10k</button>
                        <button type="button" class="py-2 border border-gray-300 rounded-xl hover:bg-gray-50" onclick="setPaidAmount(20000)">20k</button>
                    </div>
                    <div class="mt-2">
                        <div class="flex justify-between text-lg font-bold">
                            <span class="text-gray-600">Change:</span>
                            <span class="text-green-600" id="changeAmount">TZS 0.00</span>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <p class="text-gray-700 font-medium mb-2">Payment Method</p>
                    <div class="flex gap-2">
                        <button type="button" class="flex-1 py-3 border-2 border-primary-600 bg-primary-600 text-white rounded-xl font-medium" id="methodCash" onclick="selectPaymentMethod('cash')">
                            <i class="fas fa-money-bill mr-2"></i>Cash
                        </button>
                        <button type="button" class="flex-1 py-3 border-2 border-gray-300 text-gray-700 hover:border-primary-500 rounded-xl font-medium" id="methodCard" onclick="selectPaymentMethod('card')">
                            <i class="fas fa-credit-card mr-2"></i>Card
                        </button>
                        <button type="button" class="flex-1 py-3 border-2 border-gray-300 text-gray-700 hover:border-primary-500 rounded-xl font-medium" id="methodMobile" onclick="selectPaymentMethod('mobile')">
                            <i class="fas fa-mobile-alt mr-2"></i>Mobile
                        </button>
                    </div>
                </div>
                <div id="transactionIdDiv" class="mb-4 hidden">
                    <label class="block text-gray-700 font-medium mb-1">Transaction ID <span class="text-red-500">*</span></label>
                    <input type="text" id="transactionIdInput" class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-500" placeholder="Enter transaction ID">
                </div>
                <button type="button" onclick="completeSale()" class="w-full py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl font-semibold text-lg mb-2">
                    <i class="fas fa-check mr-2"></i>Complete Sale
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-8 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-green-600 text-4xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-primary-900 mb-4">Payment Successful!</h2>
            <div class="space-y-2 text-lg mb-6">
                <div class="flex justify-between">
                    <span class="text-gray-600">Total:</span>
                    <span class="font-semibold" id="modalTotal">TZS 0.00</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Paid:</span>
                    <span class="font-semibold" id="modalPaid">TZS 0.00</span>
                </div>
                <div class="flex justify-between text-green-600 font-bold">
                    <span>Change:</span>
                    <span id="modalChange">TZS 0.00</span>
                </div>
            </div>
            <div class="flex gap-3">
                <button onclick="printReceipt()" class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold text-lg">
                    <i class="fas fa-print mr-2"></i>Print Receipt
                </button>
                <button onclick="newSale()" class="flex-1 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-semibold text-lg">
                    <i class="fas fa-plus mr-2"></i>New Sale
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let cart = [];
let selectedPaymentMethod = 'cash';
let productsData = @json($products).map(p => ({...p, selling_price: parseFloat(p.selling_price)}));
let currentSaleId = null;

document.addEventListener('DOMContentLoaded', function() {
    updateTime();
    setInterval(updateTime, 1000);
    setupBarcodeScanner();
    setupProductSearch();
    renderCart();

    // Kiosk Mode
    const kioskModeEnabled = {{ $storeSetting->kiosk_mode_enabled ? 'true' : 'false' }};
    const kioskForceFullscreen = {{ $storeSetting->kiosk_force_fullscreen ? 'true' : 'false' }};
    const kioskBlockRightClick = {{ $storeSetting->kiosk_block_right_click ? 'true' : 'false' }};
    const kioskPreventTabSwitch = {{ $storeSetting->kiosk_prevent_tab_switch ? 'true' : 'false' }};
    const kioskLockKeyboardShortcuts = {{ $storeSetting->kiosk_lock_keyboard_shortcuts ? 'true' : 'false' }};
    const kioskAutoFocusCashier = {{ $storeSetting->kiosk_auto_focus_cashier ? 'true' : 'false' }};

    if (kioskModeEnabled) {
        // Auto-focus cashier
        if (kioskAutoFocusCashier) {
            const barcodeInput = document.getElementById('barcodeInput');
            if (barcodeInput) {
                barcodeInput.focus();
                // Re-focus if user clicks away
                document.addEventListener('click', function() {
                    setTimeout(() => barcodeInput.focus(), 100);
                });
            }
        }

        // Force fullscreen
        if (kioskForceFullscreen) {
            const enterFullscreen = () => {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch(err => {
                        console.log('Fullscreen request failed:', err);
                    });
                }
            };

            enterFullscreen();
            document.addEventListener('fullscreenchange', function() {
                if (!document.fullscreenElement) {
                    setTimeout(enterFullscreen, 100);
                }
            });
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    enterFullscreen();
                }
            });
        }

        // Block right-click
        if (kioskBlockRightClick) {
            document.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                return false;
            });
        }

        // Prevent tab switching
        if (kioskPreventTabSwitch) {
            window.addEventListener('beforeunload', function(e) {
                e.preventDefault();
                e.returnValue = '';
                return '';
            });
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    // Try to focus the tab back (limited by browser security)
                    window.focus();
                }
            });
        }

        // Lock keyboard shortcuts
        if (kioskLockKeyboardShortcuts) {
            document.addEventListener('keydown', function(e) {
                // Disable F1-F12 except maybe F5, but let's lock all
                if (e.key.startsWith('F') && !isNaN(e.key.slice(1))) {
                    e.preventDefault();
                    return false;
                }
                // Ctrl+W, Ctrl+N, Ctrl+Shift+N, etc.
                if (e.ctrlKey || e.metaKey) {
                    if (['w', 'n', 't', 'r', 'q', 'Tab'].includes(e.key.toLowerCase())) {
                        e.preventDefault();
                        return false;
                    }
                    // Ctrl+Shift+...
                    if (e.shiftKey && ['i', 'j', 'c', 't', 'n'].includes(e.key.toLowerCase())) {
                        e.preventDefault();
                        return false;
                    }
                }
                // Alt+F4, Alt+Tab
                if (e.altKey) {
                    if (['F4', 'Tab'].includes(e.key)) {
                        e.preventDefault();
                        return false;
                    }
                }
            });
        }
    }
});

function updateTime() {
    const now = new Date();
}

function setupBarcodeScanner() {
    let barcodeBuffer = '';
    let lastInputTime = 0;

    document.addEventListener('keypress', function(e) {
        const now = Date.now();
        if (now - lastInputTime > 100) {
            barcodeBuffer = '';
        }
        lastInputTime = now;

        if (e.key === 'Enter') {
            if (barcodeBuffer.length > 0) {
                addProductByBarcode(barcodeBuffer);
            } else {
                addProductByBarcode(document.getElementById('barcodeInput').value);
            }
            barcodeBuffer = '';
            return;
        }
        barcodeBuffer += e.key;
    });

    document.getElementById('barcodeInput').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            addProductByBarcode(this.value);
        }
    });
}

function setupProductSearch() {
    const searchInput = document.getElementById('searchProduct');
    const searchResults = document.getElementById('searchResults');

    function renderProducts(filtered) {
        if (filtered.length > 0) {
            searchResults.innerHTML = filtered.map(p => `
                <div class="px-4 py-3 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-0" onclick="addProductToCart(${p.id}, '${p.name.replace(/'/g, "\\'")}', ${parseFloat(p.selling_price)})">
                    <p class="font-semibold text-primary-900">${p.name}</p>
                    <p class="text-sm text-gray-600">TZS ${parseFloat(p.selling_price).toFixed(2)}</p>
                </div>
            `).join('');
        } else {
            searchResults.innerHTML = '<div class="px-4 py-3 text-gray-500">No products found</div>';
        }
        searchResults.classList.remove('hidden');
    }

    searchInput.addEventListener('input', function() {
        const term = this.value.toLowerCase();
        if (term.length < 2) {
            renderProducts(productsData);
            return;
        }

        const filtered = productsData.filter(p => 
            p.name.toLowerCase().includes(term) || 
            (p.barcode && p.barcode.includes(term))
        );
        renderProducts(filtered);
    });

    searchInput.addEventListener('focus', function() {
        if (this.value.length < 2) {
            renderProducts(productsData);
        }
    });

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });
}

function addProductByBarcode(barcode) {
    const product = productsData.find(p => p.barcode === barcode);
    if (product) {
        addProductToCart(product.id, product.name, parseFloat(product.selling_price));
    }
    document.getElementById('barcodeInput').value = '';
}

function addProductToCart(id, name, price) {
    const existing = cart.find(item => item.id === id);
    if (existing) {
        existing.quantity += 1;
    } else {
        cart.push({id, name, price: parseFloat(price), quantity: 1});
    }
    renderCart();
    showNotification('Added ' + name + ' to cart', 'success');
    document.getElementById('barcodeInput').focus();
}

function renderCart() {
    const container = document.getElementById('cartItems');
    if (cart.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center py-8">Cart is empty</p>';
    } else {
        container.innerHTML = cart.map((item, index) => `
            <div class="flex items-center justify-between py-3 border-b border-gray-100">
                <div class="flex-1">
                    <p class="font-medium text-primary-900">${item.name}</p>
                    <p class="text-sm text-gray-600">TZS ${parseFloat(item.price).toFixed(2)}</p>
                </div>
                <div class="flex items-center gap-2">
                    <button type="button" class="w-8 h-8 rounded-lg border border-gray-300 flex items-center justify-center" onclick="updateQuantity(${index}, -1)">-</button>
                    <span class="font-semibold text-gray-800 w-8 text-center">${item.quantity}</span>
                    <button type="button" class="w-8 h-8 rounded-lg border border-gray-300 flex items-center justify-center" onclick="updateQuantity(${index}, 1)">+</button>
                    <button type="button" onclick="removeFromCart(${index})" class="text-red-500 hover:text-red-700 ml-2">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }
    updateTotals();
}

function updateQuantity(index, delta) {
    cart[index].quantity += delta;
    if (cart[index].quantity <= 0) {
        cart.splice(index, 1);
    }
    renderCart();
}

function removeFromCart(index) {
    cart.splice(index, 1);
    renderCart();
}

function clearCart() {
    if (confirm('Clear entire cart?')) {
        cart = [];
        renderCart();
    }
}

function updateTotals() {
    const subtotal = cart.reduce((sum, item) => sum + (parseFloat(item.price) * item.quantity), 0);
    const discount = parseFloat(document.getElementById('discountInput').value) || 0;
    const total = subtotal - discount;

    document.getElementById('subtotal').textContent = 'TZS ' + subtotal.toFixed(2);
    document.getElementById('discountAmount').textContent = '-TZS ' + discount.toFixed(2);
    document.getElementById('total').textContent = 'TZS ' + total.toFixed(2);
    document.getElementById('paymentTotal').textContent = 'TZS ' + total.toFixed(2);
}

function selectPaymentMethod(method) {
    selectedPaymentMethod = method;
    document.querySelectorAll('[id^="method"]').forEach(btn => {
        btn.classList.remove('border-primary-600', 'bg-primary-600', 'text-white');
        btn.classList.add('border-gray-300', 'text-gray-700');
    });

    document.getElementById('method' + method.charAt(0).toUpperCase() + method.slice(1)).classList.remove('border-gray-300', 'text-gray-700');
    document.getElementById('method' + method.charAt(0).toUpperCase() + method.slice(1)).classList.add('border-primary-600', 'bg-primary-600', 'text-white');

    if (method === 'card' || method === 'mobile') {
        document.getElementById('transactionIdDiv').classList.remove('hidden');
    } else {
        document.getElementById('transactionIdDiv').classList.add('hidden');
    }
}

function setPaidAmount(amount) {
    document.getElementById('paidAmount').value = amount;
    calculateChange();
}

function calculateChange() {
    const total = cart.reduce((sum, item) => sum + (parseFloat(item.price) * item.quantity), 0) - (parseFloat(document.getElementById('discountInput').value) || 0);
    const paid = parseFloat(document.getElementById('paidAmount').value) || 0;
    const change = paid - total;
    document.getElementById('changeAmount').textContent = 'TZS ' + change.toFixed(2);
}

function completeSale() {
    if (cart.length === 0) {
        showNotification('Cart is empty!', 'error');
        return;
    }
    const paid = parseFloat(document.getElementById('paidAmount').value) || 0;
    const total = cart.reduce((sum, item) => sum + (parseFloat(item.price) * item.quantity), 0) - (parseFloat(document.getElementById('discountInput').value) || 0);

    if (paid < total) {
        showNotification('Insufficient payment!', 'error');
        return;
    }

    if ((selectedPaymentMethod === 'card' || selectedPaymentMethod === 'mobile') && !document.getElementById('transactionIdInput').value) {
        showNotification('Transaction ID required!', 'error');
        return;
    }

    // Convert cart items to ensure prices are numbers
    const formattedCart = cart.map(item => ({
        id: item.id,
        name: item.name,
        price: parseFloat(item.price),
        quantity: item.quantity
    }));

    fetch('/cashier/sale', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            items: formattedCart,
            total,
            discount: parseFloat(document.getElementById('discountInput').value) || 0,
            paid,
            payment_method: selectedPaymentMethod,
            transaction_id: document.getElementById('transactionIdInput').value
        })
    })
    .then(r => {
        if (!r.ok) {
            return r.json().then(err => Promise.reject(err));
        }
        return r.json();
    })
    .then(data => {
        currentSaleId = data.sale_id;
        document.getElementById('modalTotal').textContent = 'TZS ' + total.toFixed(2);
        document.getElementById('modalPaid').textContent = 'TZS ' + paid.toFixed(2);
        document.getElementById('modalChange').textContent = 'TZS ' + (paid - total).toFixed(2);
        document.getElementById('successModal').classList.remove('hidden');
    })
    .catch(e => {
        console.error(e);
        showNotification(e.error || 'Error completing sale', 'error');
    });
}

function printReceipt() {
    if (currentSaleId) {
        window.open('/sales/receipts/' + currentSaleId + '/print', '_blank');
    }
}

function newSale() {
    cart = [];
    currentSaleId = null;
    document.getElementById('successModal').classList.add('hidden');
    document.getElementById('paidAmount').value = '';
    document.getElementById('discountInput').value = '';
    document.getElementById('transactionIdInput').value = '';
    selectPaymentMethod('cash');
    renderCart();
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = 'fixed bottom-4 right-4 px-6 py-4 rounded-lg shadow-lg z-50 ' + 
        (type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white');
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}
</script>
@endsection
