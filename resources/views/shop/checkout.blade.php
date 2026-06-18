<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Feedtan Store</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Manrope', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Top Header -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 text-white py-2">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 flex items-center justify-between">
            <div class="flex items-center gap-4 text-sm">
                <span class="flex items-center gap-2">
                    <i class="fas fa-phone"></i>
                    +255 700 000 000
                </span>
            </div>
        </div>
    </div>
    
    <!-- Main Header -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex items-center justify-between">
            <a href="{{ route('shop.index') }}" class="flex items-center gap-3">
                <img src="{{ asset('feedtanstorelogo.png') }}" alt="Feedtan Store" class="h-12">
            </a>
            <a href="{{ route('shop.index') }}" class="text-gray-600 hover:text-gray-900 font-medium flex items-center gap-2">
                <i class="fas fa-arrow-left"></i>
                <span class="hidden sm:inline">Back to Shop</span>
            </a>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h1 class="text-2xl font-bold text-gray-900">Checkout</h1>
            </div>
            <form id="checkoutForm" class="p-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
                    <input type="text" id="customerName" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number</label>
                    <input type="tel" id="customerPhone" required class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email (optional)</label>
                    <input type="email" id="customerEmail" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Address</label>
                    <textarea id="deliveryAddress" required rows="3" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Location</label>
                    <div class="mt-1 p-4 bg-gray-50 border border-gray-200 rounded-lg flex items-center gap-3">
                        <i id="locationIcon" class="fas fa-spinner fa-spin text-blue-600 text-xl"></i>
                        <div class="flex-1">
                            <p id="locationStatus" class="text-sm font-medium text-gray-600">Acquiring your location...</p>
                            <p id="locationCoords" class="text-xs text-gray-500 mt-1 hidden"></p>
                        </div>
                    </div>
                    <input type="hidden" id="deliveryLatitude">
                    <input type="hidden" id="deliveryLongitude">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Fee</label>
                    <input type="number" id="deliveryFee" value="0" min="0" step="0.01" class="w-full border border-gray-300 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
                
                <!-- Order Summary -->
                <div class="pt-4 border-t border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Summary</h2>
                    <div id="checkoutItems" class="space-y-3 mb-4"></div>
                    <div class="flex items-center justify-between py-2 border-t border-gray-100">
                        <span class="font-semibold text-gray-900">Order Total:</span>
                        <span id="checkoutTotal" class="text-2xl font-bold text-green-700">TZS 0.00</span>
                    </div>
                </div>
                
                <div class="pt-4">
                    <button type="submit" id="placeOrderBtn" class="w-full bg-green-600 text-white py-3 rounded-lg font-semibold hover:bg-green-700 transition">
                        <i class="fas fa-check mr-2"></i> Place Order
                    </button>
                </div>
            </form>
        </div>
    </main>

    <footer class="bg-gray-900 text-white py-6 mt-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-gray-400">&copy; 2024 Feedtan Store. All rights reserved.</p>
        </div>
    </footer>

    <script>
        let cart = [];

        // Initialize cart from localStorage
        function initCart() {
            const saved = localStorage.getItem('shopCart');
            if (saved) {
                cart = JSON.parse(saved);
                renderCheckoutItems();
                document.getElementById('checkoutTotal').textContent = 'TZS ' + calculateTotal().toLocaleString('en-US', { minimumFractionDigits: 2 });
            } else {
                // If cart is empty, redirect back to shop
                window.location.href = '/shop';
            }
        }

        function calculateTotal() {
            return cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
        }

        function renderCheckoutItems() {
            const container = document.getElementById('checkoutItems');
            let html = '';
            cart.forEach(item => {
                const total = item.price * item.quantity;
                html += `
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-box text-gray-400 text-sm"></i>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">${item.name}</p>
                                <p class="text-sm text-gray-500">${item.quantity} × TZS ${item.price.toLocaleString('en-US', { minimumFractionDigits: 2 })}</p>
                            </div>
                        </div>
                        <p class="font-semibold text-gray-900">TZS ${total.toLocaleString('en-US', { minimumFractionDigits: 2 })}</p>
                    </div>
                `;
            });
            container.innerHTML = html;
        }

        function getUserLocation() {
            const statusEl = document.getElementById('locationStatus');
            const coordsEl = document.getElementById('locationCoords');
            const iconEl = document.getElementById('locationIcon');
            const container = iconEl.closest('div');
            
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        
                        document.getElementById('deliveryLatitude').value = lat;
                        document.getElementById('deliveryLongitude').value = lng;
                        
                        statusEl.textContent = 'Location acquired successfully!';
                        statusEl.className = 'text-sm font-medium text-green-700';
                        coordsEl.textContent = `${lat.toFixed(6)}, ${lng.toFixed(6)}`;
                        coordsEl.className = 'text-xs text-gray-500 mt-1';
                        iconEl.className = 'fas fa-check-circle text-green-600 text-xl';
                        container.className = 'mt-1 p-4 bg-green-50 border border-green-200 rounded-lg flex items-center gap-3';
                    },
                    (error) => {
                        let errorMsg = 'Unable to get location.';
                        switch (error.code) {
                            case error.PERMISSION_DENIED:
                                errorMsg = 'Permission denied. Please enable location services.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMsg = 'Location information unavailable.';
                                break;
                            case error.TIMEOUT:
                                errorMsg = 'Location request timed out.';
                                break;
                        }
                        statusEl.textContent = errorMsg;
                        statusEl.className = 'text-sm font-medium text-red-700';
                        iconEl.className = 'fas fa-exclamation-circle text-red-600 text-xl';
                        container.className = 'mt-1 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3';
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 10000,
                        maximumAge: 300000
                    }
                );
            } else {
                statusEl.textContent = 'Geolocation is not supported by your browser.';
                statusEl.className = 'text-sm font-medium text-red-700';
                iconEl.className = 'fas fa-exclamation-circle text-red-600 text-xl';
                container.className = 'mt-1 p-4 bg-red-50 border border-red-200 rounded-lg flex items-center gap-3';
            }
        }

        document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const placeOrderBtn = document.getElementById('placeOrderBtn');
            placeOrderBtn.disabled = true;
            placeOrderBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';

            const items = cart.map(item => ({ product_id: item.id, quantity: item.quantity }));
            const deliveryFee = parseFloat(document.getElementById('deliveryFee').value || 0);

            const orderData = {
                customer_name: document.getElementById('customerName').value,
                customer_phone: document.getElementById('customerPhone').value,
                customer_email: document.getElementById('customerEmail').value,
                delivery_address: document.getElementById('deliveryAddress').value,
                delivery_latitude: document.getElementById('deliveryLatitude').value || null,
                delivery_longitude: document.getElementById('deliveryLongitude').value || null,
                delivery_fee: deliveryFee,
                items: items
            };

            try {
                const response = await fetch('/api/shop/orders', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(orderData)
                });

                const data = await response.json();

                if (data.success) {
                    // Clear cart
                    cart = [];
                    localStorage.setItem('shopCart', JSON.stringify(cart));
                    
                    // Redirect to tracking page
                    window.location.href = `/shop/tracking/${data.order_number}`;
                }
            } catch (err) {
                console.error(err);
                // Fallback: even if error, if we have order number, redirect
                if (data && data.order_number) {
                    window.location.href = `/shop/tracking/${data.order_number}`;
                } else {
                    placeOrderBtn.disabled = false;
                    placeOrderBtn.innerHTML = '<i class="fas fa-check mr-2"></i> Place Order';
                }
            }
        });

        initCart();
        getUserLocation();
    </script>
</body>
</html>
