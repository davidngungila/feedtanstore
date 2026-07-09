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
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    <style>
        * { font-family: 'Manrope', sans-serif; }
        #map { height: 300px; border-radius: 12px; }
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
            <div class="flex items-center gap-4 text-sm">
                <a href="#" class="hover:text-green-200 transition">
                    <i class="fab fa-whatsapp text-lg"></i>
                </a>
                <a href="#" class="hover:text-green-200 transition">
                    <i class="fab fa-facebook text-lg"></i>
                </a>
                <a href="#" class="hover:text-green-200 transition">
                    <i class="fab fa-instagram text-lg"></i>
                </a>
                <a href="#" class="hover:text-green-200 transition">
                    <i class="fab fa-twitter text-lg"></i>
                </a>
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

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <h1 class="text-3xl font-bold text-gray-900 mb-8">Checkout</h1>
        
        <form id="checkoutForm" class="space-y-8">
            <!-- Delivery Options -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <i class="fas fa-truck text-green-600"></i>
                    Delivery Option
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="cursor-pointer">
                        <input type="radio" name="need_delivery" value="yes" checked class="sr-only" onchange="toggleDeliveryOptions()">
                        <div id="deliveryYes" class="border-2 border-green-500 bg-green-50 p-6 rounded-xl flex items-center gap-4 transition">
                            <i class="fas fa-truck text-3xl text-green-600"></i>
                            <div>
                                <h3 class="font-bold text-lg text-gray-900">Home Delivery</h3>
                                <p class="text-sm text-gray-600">Get your order delivered to your doorstep</p>
                            </div>
                        </div>
                    </label>
                    
                    <label class="cursor-pointer">
                        <input type="radio" name="need_delivery" value="no" class="sr-only" onchange="toggleDeliveryOptions()">
                        <div id="deliveryNo" class="border-2 border-gray-200 bg-white p-6 rounded-xl flex items-center gap-4 transition">
                            <i class="fas fa-store text-3xl text-gray-600"></i>
                            <div>
                                <h3 class="font-bold text-lg text-gray-900">Pickup</h3>
                                <p class="text-sm text-gray-600">Pick up your order from our store</p>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
            
            <!-- Customer Info -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <i class="fas fa-user text-green-600"></i>
                    Customer Information
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Full Name *</label>
                        <input type="text" id="customerName" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number *</label>
                        <input type="tel" id="customerPhone" required class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                    
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Email (optional)</label>
                        <input type="email" id="customerEmail" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">
                    </div>
                </div>
            </div>
            
            <!-- Delivery Address & Location -->
            <div id="deliveryAddressSection" class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <i class="fas fa-map-marker-alt text-green-600"></i>
                    Delivery Location
                </h2>
                
                <!-- Location Type Selection -->
                <div class="mb-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="cursor-pointer">
                            <input type="radio" name="location_type" value="current" checked class="sr-only" onchange="toggleLocationType()">
                            <div id="locationCurrent" class="border-2 border-green-500 bg-green-50 p-4 rounded-xl flex items-center gap-3 transition">
                                <i class="fas fa-location-crosshairs text-xl text-green-600"></i>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Current Location</h3>
                                    <p class="text-xs text-gray-600">Use your current GPS location</p>
                                </div>
                            </div>
                        </label>
                        
                        <label class="cursor-pointer">
                            <input type="radio" name="location_type" value="other" class="sr-only" onchange="toggleLocationType()">
                            <div id="locationOther" class="border-2 border-gray-200 bg-white p-4 rounded-xl flex items-center gap-3 transition">
                                <i class="fas fa-map-marked-alt text-xl text-gray-600"></i>
                                <div>
                                    <h3 class="font-semibold text-gray-900">Other Location</h3>
                                    <p class="text-xs text-gray-600">Select location on map or search</p>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
                
                <!-- Current Location Status -->
                <div id="currentLocationStatus" class="mb-6">
                    <div class="p-4 bg-gray-50 border border-gray-200 rounded-xl">
                        <div class="flex items-start gap-3">
                            <i id="currentLocationIcon" class="fas fa-spinner fa-spin text-blue-600 text-xl mt-1"></i>
                            <div class="flex-1">
                                <p id="currentLocationText" class="font-medium text-gray-700">Waiting to detect your location…</p>
                                <p id="currentLocationCoords" class="text-xs text-gray-500 mt-1 hidden"></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Other Location: Search and Map -->
                <div id="otherLocationSection" class="mb-6 hidden">
                    <!-- Search Location -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Search Location</label>
                        <div class="flex gap-2">
                            <input type="text" id="searchLocation" placeholder="Search for a place (e.g., Kariakoo market)" class="flex-1 border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">
                            <button type="button" onclick="searchLocation()" class="px-6 py-3 bg-green-600 text-white rounded-lg font-medium hover:bg-green-700 transition">
                                Search
                            </button>
                        </div>
                    </div>
                    
                    <!-- Map -->
                    <div class="mb-4">
                        <div id="map"></div>
                    </div>
                </div>
                
                <!-- Delivery Address -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Address *</label>
                    <textarea id="deliveryAddress" required rows="3" placeholder="Enter your delivery address (e.g., Kiboriloni, Moshi)" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                </div>
                
                <!-- Delivery Fee -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Delivery Fee (optional)</label>
                    <input type="number" id="deliveryFee" value="0" min="0" step="0.01" class="w-full border border-gray-300 rounded-lg px-4 py-3 focus:outline-none focus:ring-2 focus:ring-green-500">
                </div>
            </div>
            
            <!-- Payment Method -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <i class="fas fa-credit-card text-green-600"></i>
                    Payment Method
                </h2>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_method" value="cash" checked class="sr-only" onchange="selectPaymentMethod('cash')">
                        <div id="paymentCash" class="border-2 border-green-500 bg-green-50 p-6 rounded-xl flex items-center gap-4 transition">
                            <i class="fas fa-money-bill-wave text-3xl text-green-600"></i>
                            <div>
                                <h3 class="font-bold text-lg text-gray-900">Cash</h3>
                                <p class="text-sm text-gray-600">Pay on delivery/pickup</p>
                            </div>
                        </div>
                    </label>
                    
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_method" value="online" class="sr-only" onchange="selectPaymentMethod('online')">
                        <div id="paymentOnline" class="border-2 border-gray-200 bg-white p-6 rounded-xl flex items-center gap-4 transition">
                            <i class="fas fa-mobile-alt text-3xl text-gray-600"></i>
                            <div>
                                <h3 class="font-bold text-lg text-gray-900">Online Payment</h3>
                                <p class="text-sm text-gray-600">Pay via mobile money</p>
                            </div>
                        </div>
                    </label>
                    
                    <label class="cursor-pointer">
                        <input type="radio" name="payment_method" value="bank" class="sr-only" onchange="selectPaymentMethod('bank')">
                        <div id="paymentBank" class="border-2 border-gray-200 bg-white p-6 rounded-xl flex items-center gap-4 transition">
                            <i class="fas fa-university text-3xl text-gray-600"></i>
                            <div>
                                <h3 class="font-bold text-lg text-gray-900">Bank Transfer</h3>
                                <p class="text-sm text-gray-600">Pay via bank deposit</p>
                            </div>
                        </div>
                    </label>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                <h2 class="text-xl font-bold text-gray-900 mb-6 flex items-center gap-2">
                    <i class="fas fa-shopping-cart text-green-600"></i>
                    Order Summary
                </h2>
                
                <div id="checkoutItems" class="space-y-3 mb-6"></div>
                
                <div class="border-t border-gray-100 pt-6 space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Subtotal</span>
                        <span id="subtotal" class="font-semibold text-gray-900">TZS 0.00</span>
                    </div>
                    
                    <div class="flex items-center justify-between">
                        <span class="text-gray-600">Delivery Fee</span>
                        <span id="deliveryFeeDisplay" class="font-semibold text-gray-900">TZS 0.00</span>
                    </div>
                    
                    <div class="flex items-center justify-between text-xl font-bold pt-3 border-t border-gray-100">
                        <span class="text-gray-900">Total</span>
                        <span id="checkoutTotal" class="text-green-700">TZS 0.00</span>
                    </div>
                </div>
            </div>
            
            <!-- Place Order -->
            <button type="submit" id="placeOrderBtn" class="w-full bg-green-600 hover:bg-green-700 text-white py-4 rounded-xl font-bold text-lg transition flex items-center justify-center gap-3">
                <i class="fas fa-check-circle"></i>
                Place Order
            </button>
        </form>
    </main>

    <footer class="bg-gray-900 text-white py-6 mt-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-gray-400">&copy; 2024 Feedtan Store. All rights reserved.</p>
        </div>
    </footer>

    <script>
        let cart = [];
        let userLocation = { lat: null, lng: null };
        let map, marker;
        
        // Initialize cart from localStorage
        function initCart() {
            const saved = localStorage.getItem('shopCart');
            if (saved) {
                cart = JSON.parse(saved);
                if (cart.length === 0) {
                    window.location.href = '{{ route('shop.index') }}';
                } else {
                    renderCheckoutItems();
                    updateTotal();
                }
            } else {
                window.location.href = '{{ route('shop.index') }}';
            }
        }
        
        function renderCheckoutItems() {
            const container = document.getElementById('checkoutItems');
            let html = '';
            cart.forEach(item => {
                const total = item.price * item.quantity;
                html += `
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 bg-gray-100 rounded-lg flex items-center justify-center">
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
        
        function calculateTotal() {
            return cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
        }
        
        function updateTotal() {
            const subtotal = calculateTotal();
            const deliveryFee = parseFloat(document.getElementById('deliveryFee').value) || 0;
            const total = subtotal + deliveryFee;
            
            document.getElementById('subtotal').textContent = 'TZS ' + subtotal.toLocaleString('en-US', { minimumFractionDigits: 2 });
            document.getElementById('deliveryFeeDisplay').textContent = 'TZS ' + deliveryFee.toLocaleString('en-US', { minimumFractionDigits: 2 });
            document.getElementById('checkoutTotal').textContent = 'TZS ' + total.toLocaleString('en-US', { minimumFractionDigits: 2 });
        }
        
        // Toggle delivery options
        function toggleDeliveryOptions() {
            const needDelivery = document.querySelector('input[name="need_delivery"]:checked').value;
            const deliveryAddressSection = document.getElementById('deliveryAddressSection');
            const deliveryYes = document.getElementById('deliveryYes');
            const deliveryNo = document.getElementById('deliveryNo');
            
            if (needDelivery === 'yes') {
                deliveryAddressSection.classList.remove('hidden');
                deliveryYes.className = 'border-2 border-green-500 bg-green-50 p-6 rounded-xl flex items-center gap-4 transition';
                deliveryNo.className = 'border-2 border-gray-200 bg-white p-6 rounded-xl flex items-center gap-4 transition';
            } else {
                deliveryAddressSection.classList.add('hidden');
                deliveryNo.className = 'border-2 border-green-500 bg-green-50 p-6 rounded-xl flex items-center gap-4 transition';
                deliveryYes.className = 'border-2 border-gray-200 bg-white p-6 rounded-xl flex items-center gap-4 transition';
            }
        }
        
        // Select payment method
        function selectPaymentMethod(method) {
            const paymentCash = document.getElementById('paymentCash');
            const paymentOnline = document.getElementById('paymentOnline');
            const paymentBank = document.getElementById('paymentBank');
            
            paymentCash.className = method === 'cash' 
                ? 'border-2 border-green-500 bg-green-50 p-6 rounded-xl flex items-center gap-4 transition' 
                : 'border-2 border-gray-200 bg-white p-6 rounded-xl flex items-center gap-4 transition';
            paymentOnline.className = method === 'online' 
                ? 'border-2 border-green-500 bg-green-50 p-6 rounded-xl flex items-center gap-4 transition' 
                : 'border-2 border-gray-200 bg-white p-6 rounded-xl flex items-center gap-4 transition';
            paymentBank.className = method === 'bank' 
                ? 'border-2 border-green-500 bg-green-50 p-6 rounded-xl flex items-center gap-4 transition' 
                : 'border-2 border-gray-200 bg-white p-6 rounded-xl flex items-center gap-4 transition';
        }
        
        // Toggle location type
        function toggleLocationType() {
            const locationType = document.querySelector('input[name="location_type"]:checked').value;
            const currentLocationStatus = document.getElementById('currentLocationStatus');
            const otherLocationSection = document.getElementById('otherLocationSection');
            const locationCurrent = document.getElementById('locationCurrent');
            const locationOther = document.getElementById('locationOther');
            
            if (locationType === 'current') {
                currentLocationStatus.classList.remove('hidden');
                otherLocationSection.classList.add('hidden');
                locationCurrent.className = 'border-2 border-green-500 bg-green-50 p-4 rounded-xl flex items-center gap-3 transition';
                locationOther.className = 'border-2 border-gray-200 bg-white p-4 rounded-xl flex items-center gap-3 transition';
                getCurrentLocation();
            } else {
                currentLocationStatus.classList.add('hidden');
                otherLocationSection.classList.remove('hidden');
                locationOther.className = 'border-2 border-green-500 bg-green-50 p-4 rounded-xl flex items-center gap-3 transition';
                locationCurrent.className = 'border-2 border-gray-200 bg-white p-4 rounded-xl flex items-center gap-3 transition';
                initMap();
            }
        }
        
        // Get current location automatically
        function getCurrentLocation() {
            const statusEl = document.getElementById('currentLocationText');
            const coordsEl = document.getElementById('currentLocationCoords');
            const iconEl = document.getElementById('currentLocationIcon');
            
            iconEl.className = 'fas fa-spinner fa-spin text-blue-600 text-xl mt-1';
            statusEl.textContent = 'Waiting to detect your location…';
            statusEl.className = 'font-medium text-gray-700';
            
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        userLocation.lat = position.coords.latitude;
                        userLocation.lng = position.coords.longitude;
                        
                        statusEl.textContent = 'Location detected successfully!';
                        statusEl.className = 'font-medium text-green-700';
                        coordsEl.textContent = `${userLocation.lat.toFixed(6)}, ${userLocation.lng.toFixed(6)}`;
                        coordsEl.className = 'text-xs text-gray-500 mt-1';
                        iconEl.className = 'fas fa-check-circle text-green-600 text-xl mt-1';
                    },
                    (error) => {
                        let errorMsg = 'Unable to detect your location.';
                        switch (error.code) {
                            case error.PERMISSION_DENIED:
                                errorMsg = 'Please allow location access to use your current location.';
                                break;
                            case error.POSITION_UNAVAILABLE:
                                errorMsg = 'Location information unavailable.';
                                break;
                            case error.TIMEOUT:
                                errorMsg = 'Location request timed out.';
                                break;
                        }
                        statusEl.textContent = errorMsg;
                        statusEl.className = 'font-medium text-red-700';
                        iconEl.className = 'fas fa-exclamation-circle text-red-600 text-xl mt-1';
                    },
                    {
                        enableHighAccuracy: true,
                        timeout: 15000,
                        maximumAge: 60000
                    }
                );
            } else {
                statusEl.textContent = 'Geolocation is not supported by your browser.';
                statusEl.className = 'font-medium text-red-700';
                iconEl.className = 'fas fa-exclamation-circle text-red-600 text-xl mt-1';
            }
        }
        
        // Initialize map
        function initMap() {
            if (!map) {
                const defaultLat = -3.3869;
                const defaultLng = 36.6883;
                
                map = L.map('map').setView([defaultLat, defaultLng], 13);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap contributors'
                }).addTo(map);
                
                marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);
                userLocation.lat = defaultLat;
                userLocation.lng = defaultLng;
                
                // Update location when marker is dragged
                marker.on('dragend', function(e) {
                    const position = marker.getLatLng();
                    userLocation.lat = position.lat;
                    userLocation.lng = position.lng;
                });
                
                // Update location when map is clicked
                map.on('click', function(e) {
                    marker.setLatLng(e.latlng);
                    userLocation.lat = e.latlng.lat;
                    userLocation.lng = e.latlng.lng;
                });
            }
        }
        
        // Search location
        async function searchLocation() {
            const query = document.getElementById('searchLocation').value;
            if (!query) return;
            
            try {
                const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(query)}`);
                const data = await response.json();
                
                if (data && data.length > 0) {
                    const firstResult = data[0];
                    userLocation.lat = parseFloat(firstResult.lat);
                    userLocation.lng = parseFloat(firstResult.lon);
                    
                    if (map) {
                        map.setView([userLocation.lat, userLocation.lng], 15);
                        marker.setLatLng([userLocation.lat, userLocation.lng]);
                    }
                }
            } catch (error) {
                console.error('Error searching location:', error);
            }
        }
        
        document.getElementById('deliveryFee').addEventListener('input', updateTotal);
        
        document.getElementById('checkoutForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            // Ensure we have location
            const needDelivery = document.querySelector('input[name="need_delivery"]:checked').value;
            if (needDelivery === 'yes' && (!userLocation.lat || !userLocation.lng)) {
                alert('Please allow location access to proceed with delivery.');
                getCurrentLocation();
                return;
            }
            
            // Even if pickup, still try to capture location for analytics
            if (!userLocation.lat || !userLocation.lng) {
                getCurrentLocation();
            }
            
            const placeOrderBtn = document.getElementById('placeOrderBtn');
            placeOrderBtn.disabled = true;
            placeOrderBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing…';
            
            const items = cart.map(item => ({ product_id: item.id, quantity: item.quantity }));
            const deliveryFee = parseFloat(document.getElementById('deliveryFee').value) || 0;
            const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
            
            const orderData = {
                customer_name: document.getElementById('customerName').value,
                customer_phone: document.getElementById('customerPhone').value,
                customer_email: document.getElementById('customerEmail').value,
                delivery_address: document.getElementById('deliveryAddress').value || 'Store Pickup',
                delivery_latitude: userLocation.lat,
                delivery_longitude: userLocation.lng,
                delivery_fee: deliveryFee,
                payment_method: paymentMethod,
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
                    cart = [];
                    localStorage.setItem('shopCart', JSON.stringify(cart));
                    window.location.href = '/shop/tracking/' + data.order_number;
                }
            } catch (error) {
                console.error(error);
                placeOrderBtn.disabled = false;
                placeOrderBtn.innerHTML = '<i class="fas fa-check-circle mr-2"></i>Place Order';
                alert('Failed to place order. Please try again.');
            }
        });
        
        initCart();
        // Auto-detect current location on load
        getCurrentLocation();
    </script>
</body>
</html>
