@extends('layouts.app')

@section('page-title', 'New Sale')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Product Selection -->
        <div class="lg:col-span-2">
            <div class="card rounded-2xl p-6">
                <!-- Mode Tabs -->
                <div class="mb-6">
                    <div class="flex gap-4 mb-4 border-b border-gray-200">
                        <button id="manualModeBtn" class="px-6 py-2 text-primary-600 border-b-2 border-primary-600 font-semibold">Manual Mode</button>
                        <button id="automaticModeBtn" class="px-6 py-2 text-gray-500 hover:text-primary-600">Automatic (QR Scan)</button>
                    </div>
                </div>

                <!-- Manual Mode Content -->
                <div id="manualModeContent">
                    <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                        <p class="text-sm text-green-700"><i class="fas fa-barcode mr-2"></i> Scan barcode anywhere on this page to add product to cart automatically!</p>
                    </div>
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

                <!-- Automatic Mode Content -->
                <div id="automaticModeContent" class="hidden">
                    <h2 class="text-xl font-bold text-primary-900 mb-4">Scan Barcodes</h2>
                    <div class="mb-4">
                        <video id="qrScanner" class="w-full rounded-lg border border-gray-300" playsinline></video>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Or enter barcode/SKU manually:</label>
                        <input type="text" id="manualBarcode" placeholder="Enter barcode or SKU and press Enter..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" onkeydown="handleManualBarcodeKeydown(event)">
                        <button type="button" onclick="addProductByBarcode()" class="mt-2 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg">Add Product</button>
                    </div>
                    <div id="scannedItems" class="mb-4">
                        <h3 class="text-lg font-semibold text-gray-700 mb-2">Scanned/Added Items:</h3>
                        <div id="scannedItemsList"></div>
                    </div>
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

<!-- Include QR Scanner library -->
<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>

<script>
let cart = [];
let originalPaidAmount = 0;
let lastCalculatedTotal = 0;
let currentDiscount = null;
let html5QrCodeScanner = null;
let barcodeBuffer = '';
let lastKeyTime = 0;

// Product data for quick lookup
const productsData = @json($productsData);

// Mode switching
document.getElementById('manualModeBtn').addEventListener('click', function() {
    document.getElementById('manualModeBtn').classList.add('text-primary-600', 'border-b-2', 'border-primary-600', 'font-semibold');
    document.getElementById('manualModeBtn').classList.remove('text-gray-500');
    document.getElementById('automaticModeBtn').classList.add('text-gray-500');
    document.getElementById('automaticModeBtn').classList.remove('text-primary-600', 'border-b-2', 'border-primary-600', 'font-semibold');
    document.getElementById('manualModeContent').classList.remove('hidden');
    document.getElementById('automaticModeContent').classList.add('hidden');
    stopScanner();
});

document.getElementById('automaticModeBtn').addEventListener('click', function() {
    document.getElementById('automaticModeBtn').classList.add('text-primary-600', 'border-b-2', 'border-primary-600', 'font-semibold');
    document.getElementById('automaticModeBtn').classList.remove('text-gray-500');
    document.getElementById('manualModeBtn').classList.add('text-gray-500');
    document.getElementById('manualModeBtn').classList.remove('text-primary-600', 'border-b-2', 'border-primary-600', 'font-semibold');
    document.getElementById('automaticModeContent').classList.remove('hidden');
    document.getElementById('manualModeContent').classList.add('hidden');
    startScanner();
});

// QR Scanner functions
function startScanner() {
    if (!html5QrCodeScanner) {
        html5QrCodeScanner = new Html5QrcodeScanner(
            "qrScanner",
            { fps: 10, qrbox: { width: 250, height: 250 } },
            false
        );
        html5QrCodeScanner.render(onScanSuccess, onScanFailure);
    }
}

function stopScanner() {
    if (html5QrCodeScanner) {
        html5QrCodeScanner.clear().catch(error => {
            console.error("Failed to clear scanner", error);
        });
        html5QrCodeScanner = null;
    }
}

function onScanSuccess(decodedText) {
    console.log(`Scanned: ${decodedText}`);
    findProductByCode(decodedText);
}

function onScanFailure(error) {
    // Ignore scan failures - they're normal when no QR code is in view
}

function handleManualBarcodeKeydown(event) {
    if (event.key === 'Enter') {
        event.preventDefault();
        addProductByBarcode();
    }
}

function addProductByBarcode() {
    const barcode = document.getElementById('manualBarcode').value.trim();
    if (barcode) {
        findProductByCode(barcode);
        document.getElementById('manualBarcode').value = '';
        // Focus back on the input for next scan/entry
        document.getElementById('manualBarcode').focus();
    }
}

function findProductByCode(code) {
    // Search by barcode, then by SKU
    const product = productsData.find(p => p.barcode === code || p.sku === code);
    if (product) {
        if (product.quantity > 0) {
            addToCart(product.id, product.name, product.selling_price);
            addToScannedList(product.name);
        } else {
            alert(`Product "${product.name}" is out of stock!`);
        }
    } else {
        alert(`No product found with barcode/SKU: ${code}`);
    }
}

function addToScannedList(productName) {
    const list = document.getElementById('scannedItemsList');
    const item = document.createElement('div');
    item.className = 'flex items-center justify-between py-1 border-b border-gray-100';
    item.innerHTML = `
        <span class="text-sm text-gray-700">${productName}</span>
        <span class="text-xs text-gray-500">${new Date().toLocaleTimeString()}</span>
    `;
    list.insertBefore(item, list.firstChild);
}

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
    const numericPrice = parseFloat(price);
    const existingItem = cart.find(item => item.product_id === productId);
    
    if (existingItem) {
        existingItem.quantity += 1;
    } else {
        cart.push({
            product_id: productId,
            name: productName,
            quantity: 1,
            unit_price: numericPrice
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
    
    // If no discount selected, find the best applicable one
    if (!currentDiscount) {
        let bestDiscount = null;
        let maxDiscountValue = 0;
        const discountSelect = document.getElementById('discountSelect');
        
        // Iterate all discount options
        for (let i = 1; i < discountSelect.options.length; i++) {
            const option = discountSelect.options[i];
            const type = option.dataset.type;
            const value = parseFloat(option.dataset.value);
            const minAmount = option.dataset.min ? parseFloat(option.dataset.min) : null;
            const maxAmount = option.dataset.max ? parseFloat(option.dataset.max) : null;
            
            // Check if this discount is applicable
            if ((!minAmount || subtotal >= minAmount) && (!maxAmount || subtotal <= maxAmount)) {
                let thisDiscount = type === 'percentage' ? subtotal * (value / 100) : value;
                if (thisDiscount > maxDiscountValue) {
                    maxDiscountValue = thisDiscount;
                    bestDiscount = option;
                }
            }
        }
        
        if (bestDiscount) {
            discountSelect.value = bestDiscount.value;
            currentDiscount = {
                type: bestDiscount.dataset.type,
                value: parseFloat(bestDiscount.dataset.value),
                minAmount: bestDiscount.dataset.min ? parseFloat(bestDiscount.dataset.min) : null,
                maxAmount: bestDiscount.dataset.max ? parseFloat(bestDiscount.dataset.max) : null
            };
        }
    }
    
    const discount = calculateDiscount(subtotal);
    lastCalculatedTotal = subtotal - discount;
    
    // Auto-fill paid amount
    const paidInput = document.getElementById('paidAmount');
    if (parseFloat(paidInput.value) === originalPaidAmount || originalPaidAmount === 0) {
        paidInput.value = lastCalculatedTotal.toFixed(2);
        originalPaidAmount = lastCalculatedTotal;
    }
    
    const paid = parseFloat(document.getElementById('paidAmount').value) || 0;
    const change = Math.max(0, paid - lastCalculatedTotal);
    
    document.getElementById('subtotal').textContent = 'TZS ' + subtotal.toFixed(2);
    document.getElementById('discountAmount').textContent = '-TZS ' + discount.toFixed(2);
    document.getElementById('total').textContent = 'TZS ' + lastCalculatedTotal.toFixed(2);
    document.getElementById('change').textContent = 'TZS ' + change.toFixed(2);
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

// Global barcode scanner listener (works in both modes)
document.addEventListener('keydown', function(e) {
    const now = Date.now();
    // Barcode scanners type very fast - if more than 100ms since last key, reset buffer
    if (now - lastKeyTime > 100) {
        barcodeBuffer = '';
    }
    lastKeyTime = now;
    
    if (e.key === 'Enter') {
        if (barcodeBuffer.length > 0) {
            e.preventDefault();
            findProductByCode(barcodeBuffer.trim());
            barcodeBuffer = '';
        }
    } else if (e.key.length === 1) { // Only add printable characters
        barcodeBuffer += e.key;
    }
});

// VFD Functions
async function sendToVFD(endpoint, data = {}) {
    try {
        const response = await fetch(`/vfd/${endpoint}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        return await response.json();
    } catch (error) {
        console.error('VFD error:', error);
    }
}

// Override addToCart to send product to VFD
const originalAddToCart = addToCart;
window.addToCart = function(productId, productName, price) {
    originalAddToCart(productId, productName, price);
    
    // Find the item in cart to get quantity and total
    const item = cart.find(i => i.product_id === productId);
    if (item) {
        sendToVFD('product', {
            name: productName,
            quantity: item.quantity,
            price: price,
            total: item.unit_price * item.quantity
        });
    }
};

// Send payment info when sale is submitted
document.getElementById('saleForm').addEventListener('submit', async function(e) {
    const total = lastCalculatedTotal;
    const paid = parseFloat(document.getElementById('paidAmount').value);
    const change = Math.max(0, paid - total);
    const paymentMethod = document.getElementById('paymentMethod').value;
    
    await sendToVFD('payment', {
        total: total,
        paid: paid,
        change: change,
        payment_method: paymentMethod
    });
    
    await sendToVFD('thank-you');
});

// Send welcome message when page loads
document.addEventListener('DOMContentLoaded', function() {
    sendToVFD('welcome');
});
</script>
@endsection
