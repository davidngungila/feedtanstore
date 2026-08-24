@extends('layouts.app')

@section('page-title', 'New Stock Request')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <a href="{{ route('stock-requests.index') }}" class="text-primary-600 hover:text-primary-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Stock Requests
        </a>
    </div>

    <div class="card rounded-2xl p-6">
        <h1 class="text-2xl font-bold text-primary-900 mb-6">New Stock Request</h1>

        <form action="{{ route('stock-requests.store') }}" method="POST" id="stockRequestForm">
            @csrf
            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Request Type</label>
                    <div class="px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm text-gray-700">
                        <i class="fas fa-store mr-2 text-primary-600"></i>Shop
                    </div>
                    <input type="hidden" name="request_type" value="store_use">
                </div>

                <div id="online_order_section" class="hidden">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Online Order *</label>
                    <select name="online_order_id" id="online_order_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Select Online Order</option>
                        @foreach($onlineOrders as $order)
                            <option value="{{ $order->id }}" 
                                    data-customer="{{ $order->customer_name }}"
                                    data-address="{{ $order->delivery_address }}"
                                    data-lat="{{ $order->delivery_latitude }}"
                                    data-lng="{{ $order->delivery_longitude }}">
                                {{ $order->order_number }} - {{ $order->customer_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Order Details (shown when online order selected) -->
                <div id="order_details_section" class="hidden p-4 bg-blue-50 rounded-lg">
                    <h3 class="font-semibold text-blue-900 mb-3">Order Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm text-blue-700">Customer</p>
                            <p class="font-semibold text-blue-900" id="order_customer">-</p>
                        </div>
                        <div>
                            <p class="text-sm text-blue-700">Delivery Address</p>
                            <p class="font-semibold text-blue-900" id="order_address">-</p>
                        </div>
                        <div>
                            <p class="text-sm text-blue-700">Distance from Store</p>
                            <p class="font-semibold text-blue-900" id="order_distance">-</p>
                        </div>
                    </div>
                    @if(isset($onlineOrders) && $onlineOrders->firstWhere('delivery_latitude'))
                    <div class="mt-4">
                        <p class="text-sm text-blue-700 mb-2">Delivery Location & Route</p>
                        <div id="map_container" class="h-64 bg-gray-200 rounded-lg flex items-center justify-center">
                            <p class="text-gray-500 text-sm">Select an order to view location and route</p>
                        </div>
                    </div>
                    @endif
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Products *</label>
                    <div id="products_container">
                        <div class="product-item mb-4 p-4 border border-gray-200 rounded-lg">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                                    <select name="products[0][product_id]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product-select" onchange="showAvailableQty(this)">
                                        <option value="">Select Product</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}" data-quantity="{{ $product->quantity }}">{{ $product->name }}</option>
                                        @endforeach
                                    </select>
                                    <p class="available-qty text-xs mt-1 text-gray-500"></p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity Requested *</label>
                                    <input type="number" name="products[0][quantity_requested]" required min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" oninput="clampQty(this)">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                    <input type="text" name="products[0][notes]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                </div>
                            </div>
                            <button type="button" class="remove-product mt-2 text-red-600 hover:text-red-800 text-sm">
                                <i class="fas fa-trash mr-1"></i>Remove
                            </button>
                        </div>
                    </div>
                    <button type="button" id="add_product" class="mt-2 text-primary-600 hover:text-primary-800 text-sm">
                        <i class="fas fa-plus mr-1"></i>Add Product
                    </button>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Notes</label>
                    <textarea name="notes" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" rows="3" placeholder="Any additional notes..."></textarea>
                </div>

                <div class="flex justify-end gap-3">
                    <a href="{{ route('stock-requests.index') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                        Submit Request
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
let productIndex = 1;
let orderMap = null;
let routePolyline = null;
const onlineOrdersData = @json($onlineOrders);
const preSelectedOrderId = {{ $preSelectedOrderId ?? 'null' }};
const routes = @json($routes);

// Store location from settings
const STORE_LATITUDE = {{ $storeLat ?? -6.7924 }};
const STORE_LONGITUDE = {{ $storeLng ?? 39.2083 }};

// Calculate distance between two coordinates using Haversine formula
function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Earth's radius in km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = 
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
        Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    const distance = R * c; // Distance in km
    return distance.toFixed(2); // Return distance rounded to 2 decimal places
}

// Initialize map with Leaflet
function initMap() {
    if (orderMap) return;
    
    mapContainer = document.getElementById('map_container');
    if (!mapContainer) return;
    
    // Load Leaflet CSS and JS
    const link = document.createElement('link');
    link.rel = 'stylesheet';
    link.href = 'https://unpkg.com/leaflet/dist/leaflet.css';
    document.head.appendChild(link);
    
    const script = document.createElement('script');
    script.src = 'https://unpkg.com/leaflet/dist/leaflet.js';
    script.onload = function() {
        orderMap = L.map('map_container').setView([STORE_LATITUDE, STORE_LONGITUDE], 12);
        
        // OpenStreetMap base layer
        const osmLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        });
        
        // World Imagery base layer (Esri)
        const worldImageryLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, DigitalGlobe, GeoEye, i-cubed, USDA, USGS, AEX, Getmapping, Aerogrid, IGN, IGP, swisstopo, and the GIS User Community'
        });
        
        // Add OSM as default
        osmLayer.addTo(orderMap);
        
        // Layer control
        const baseLayers = {
            'OpenStreetMap': osmLayer,
            'World Imagery': worldImageryLayer
        };
        
        L.control.layers(baseLayers).addTo(orderMap);
        
        // Add store marker
        L.marker([STORE_LATITUDE, STORE_LONGITUDE])
            .addTo(orderMap)
            .bindPopup('<strong>Store</strong>').openPopup();
    };
    document.head.appendChild(script);
}

// Update map with order location and pre-fetched route
function updateMapWithOrder(lat, lng, customerName, address, orderId) {
    if (!orderMap) {
        initMap();
        // Wait for map to initialize
        setTimeout(() => {
            updateMapWithOrder(lat, lng, customerName, address, orderId);
        }, 500);
        return;
    }
    
    // Remove existing route if any
    if (routePolyline) {
        orderMap.removeLayer(routePolyline);
        routePolyline = null;
    }
    
    // Add order marker
    const orderMarker = L.circleMarker([lat, lng], {
        radius: 8,
        fillColor: '#f97316',
        color: '#fff',
        weight: 2,
        fillOpacity: 0.8
    })
        .addTo(orderMap)
        .bindPopup(`
            <div class="p-2">
                <h4 class="font-bold text-sm">Delivery Location</h4>
                <p class="text-xs text-gray-600">${customerName}</p>
                <p class="text-xs text-gray-500 mt-1">${address}</p>
            </div>
        `);
    
    // Fit bounds to show both markers
    const bounds = L.latLngBounds([
        [STORE_LATITUDE, STORE_LONGITUDE],
        [lat, lng]
    ]);
    orderMap.fitBounds(bounds, { padding: [50, 50] });
    
    // Add pre-fetched route if available
    if (routes && routes[orderId] && routes[orderId].features && routes[orderId].features.length > 0) {
        const coords = routes[orderId].features[0].geometry.coordinates;
        const points = coords.map(c => [c[1], c[0]]);
        routePolyline = L.polyline(points, { color: '#3b82f6', weight: 4, opacity: 0.7 }).addTo(orderMap);
        orderMap.fitBounds(points, { padding: [50, 50] });
    }
}

// Auto-select order if pre-selected
if (preSelectedOrderId) {
    const onlineOrderSelect = document.getElementById('online_order_id');
    onlineOrderSelect.value = preSelectedOrderId;
    onlineOrderSelect.dispatchEvent(new Event('change'));
}

document.getElementById('online_order_id').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const orderDetailsSection = document.getElementById('order_details_section');
    const orderCustomer = document.getElementById('order_customer');
    const orderAddress = document.getElementById('order_address');
    const orderDistance = document.getElementById('order_distance');
    const mapContainer = document.getElementById('map_container');
    
    if (this.value) {
        const customer = selectedOption.getAttribute('data-customer');
        const address = selectedOption.getAttribute('data-address');
        const lat = selectedOption.getAttribute('data-lat');
        const lng = selectedOption.getAttribute('data-lng');
        
        orderCustomer.textContent = customer || '-';
        orderAddress.textContent = address || '-';
        
        // Calculate and display distance if coordinates available
        if (lat && lng) {
            const distance = calculateDistance(STORE_LATITUDE, STORE_LONGITUDE, parseFloat(lat), parseFloat(lng));
            orderDistance.textContent = distance + ' km';
        } else {
            orderDistance.textContent = 'No location data';
        }
        
        orderDetailsSection.classList.remove('hidden');
        
        // Auto-populate products from order
        const orderId = this.value;
        const order = onlineOrdersData.find(o => o.id == orderId);
        
        if (order && order.items && order.items.length > 0) {
            const container = document.getElementById('products_container');
            container.innerHTML = '';
            productIndex = 0;
            
            order.items.forEach((item, index) => {
                const template = `
                    <div class="product-item mb-4 p-4 border border-gray-200 rounded-lg bg-blue-50">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                                <select name="products[${productIndex}][product_id]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-100" data-product-id="${item.product_id}" disabled>
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}">{{ $product->name }} (Available: {{ $product->quantity }})</option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="products[${productIndex}][product_id]" value="${item.product_id}">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Quantity Requested *</label>
                                <input type="number" name="products[${productIndex}][quantity_requested]" required min="1" value="${item.quantity}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-100" readonly>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                <input type="text" name="products[${productIndex}][notes]" value="From order item" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-gray-100" readonly>
                            </div>
                        </div>
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', template);
                productIndex++;
            });
            
            // Set selected values after HTML is inserted
            const selects = container.querySelectorAll('select[data-product-id]');
            selects.forEach(select => {
                const productId = select.getAttribute('data-product-id');
                select.value = productId;
            });
            
            // Hide add product button for auto-populated orders
            document.getElementById('add_product').classList.add('hidden');
        }
        
        // Show map with route if coordinates available
        if (lat && lng) {
            updateMapWithOrder(parseFloat(lat), parseFloat(lng), customer, address, this.value);
        } else {
            mapContainer.innerHTML = '<p class="text-gray-500 text-sm">No location data available for this order</p>';
        }
    } else {
        orderDetailsSection.classList.add('hidden');
        // Reset products container
        const container = document.getElementById('products_container');
        container.innerHTML = `
            <div class="product-item mb-4 p-4 border border-gray-200 rounded-lg">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                        <select name="products[0][product_id]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 product-select" onchange="showAvailableQty(this)">
                            <option value="">Select Product</option>
                            @foreach($products as $product)
                                <option value="{{ $product->id }}" data-quantity="{{ $product->quantity }}">{{ $product->name }}</option>
                            @endforeach
                        </select>
                        <p class="available-qty text-xs mt-1 text-gray-500"></p>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Quantity Requested *</label>
                        <input type="number" name="products[0][quantity_requested]" required min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" oninput="clampQty(this)">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <input type="text" name="products[0][notes]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                </div>
                <button type="button" class="remove-product mt-2 text-red-600 hover:text-red-800 text-sm">
                    <i class="fas fa-trash mr-1"></i>Remove
                </button>
            </div>
        `;
        productIndex = 1;
        document.getElementById('add_product').classList.remove('hidden');
    }
});

document.getElementById('add_product').addEventListener('click', function() {
    const container = document.getElementById('products_container');
    const template = `
        <div class="product-item mb-4 p-4 border border-gray-200 rounded-lg">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product *</label>
                    <select name="products[${productIndex}][product_id]" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" onchange="showAvailableQty(this)">
                        <option value="">Select Product</option>
                        @foreach($products as $product)
                            <option value="{{ $product->id }}" data-quantity="{{ $product->quantity }}">{{ $product->name }}</option>
                        @endforeach
                    </select>
                    <p class="available-qty text-xs mt-1 text-gray-500"></p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity Requested *</label>
                        <input type="number" name="products[${productIndex}][quantity_requested]" required min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" oninput="clampQty(this)">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <input type="text" name="products[${productIndex}][notes]" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>
            <button type="button" class="remove-product mt-2 text-red-600 hover:text-red-800 text-sm">
                <i class="fas fa-trash mr-1"></i>Remove
            </button>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', template);
    productIndex++;
});

document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-product')) {
        e.target.closest('.product-item').remove();
    }
});

function showAvailableQty(select) {
    const opt = select.options[select.selectedIndex];
    const wrapper = select.closest('.grid');
    const p = wrapper.querySelector('.available-qty');
    const qtyInput = wrapper.querySelector('input[type="number"]');
    if (!p) return;
    const qty = opt.getAttribute('data-quantity');
    if (qty !== null && qty !== undefined && select.value) {
        p.textContent = 'Available: ' + qty;
        p.className = 'available-qty text-xs mt-1 ' + (parseInt(qty) > 0 ? 'text-green-600' : 'text-red-600');
        if (qtyInput) {
            qtyInput.max = qty;
            if (parseInt(qtyInput.value) > parseInt(qty)) qtyInput.value = qty;
        }
    } else {
        p.textContent = '';
        if (qtyInput) qtyInput.removeAttribute('max');
    }
}

function clampQty(input) {
    const max = parseInt(input.max);
    if (!isNaN(max) && parseInt(input.value) > max) {
        input.value = max;
    }
    if (parseInt(input.value) < 1) {
        input.value = 1;
    }
}

document.getElementById('stockRequestForm').addEventListener('submit', function(e) {
    document.querySelectorAll('.product-select').forEach(function(sel) {
        const opt = sel.options[sel.selectedIndex];
        if (!opt || !opt.value) return;
        const max = parseInt(opt.getAttribute('data-quantity'));
        const qtyInput = sel.closest('.grid').querySelector('input[type="number"]');
        if (qtyInput && !isNaN(max) && parseInt(qtyInput.value) > max) {
            qtyInput.value = max;
        }
    });
});
</script>
@endsection
