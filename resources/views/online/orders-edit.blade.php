@extends('layouts.app')

@section('page-title', 'Edit Online Order')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Edit Online Order {{ $order->order_number }}</h2>
            <a href="{{ route('online.orders') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Orders
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

        <form action="{{ route('online.orders.update', $order) }}" method="POST" id="orderForm">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Customer Info -->
                <div class="lg:col-span-2">
                    <h3 class="text-lg font-semibold text-primary-900 mb-4">Customer Information</h3>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Existing Customer (Optional)</label>
                        <select name="customer_id" id="customerSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Or fill in new customer details below</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" {{ $order->customer_id == $customer->id ? 'selected' : '' }} data-name="{{ $customer->name }}" data-phone="{{ $customer->phone }}" data-email="{{ $customer->email }}" data-address="{{ $customer->address }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name *</label>
                            <input type="text" name="customer_name" id="customerName" value="{{ old('customer_name', $order->customer_name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Customer Phone *</label>
                            <input type="text" name="customer_phone" id="customerPhone" value="{{ old('customer_phone', $order->customer_phone) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Customer Email</label>
                            <input type="email" name="customer_email" id="customerEmail" value="{{ old('customer_email', $order->customer_email) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Address *</label>
                            <input type="text" name="delivery_address" id="customerAddress" value="{{ old('delivery_address', $order->delivery_address) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>
                    
                    <!-- Location Picker -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Location (Optional)</label>
                        <div class="flex gap-3 mb-3">
                            <input type="number" step="0.0000001" name="delivery_latitude" id="deliveryLatitude" value="{{ old('delivery_latitude', $order->delivery_latitude) }}" class="form-input w-full" placeholder="Latitude">
                            <input type="number" step="0.0000001" name="delivery_longitude" id="deliveryLongitude" value="{{ old('delivery_longitude', $order->delivery_longitude) }}" class="form-input w-full" placeholder="Longitude">
                            <button type="button" id="getCurrentLocationBtn" class="bg-blue-100 text-blue-700 px-4 py-2 rounded-lg hover:bg-blue-200 transition-colors">
                                <i class="fas fa-location-crosshairs mr-1"></i> Current
                            </button>
                        </div>
                        <div id="editOrderMap" class="w-full h-[300px] rounded-lg border border-gray-300"></div>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="lg:col-span-2">
                    <h3 class="text-lg font-semibold text-primary-900 mb-4">Order Details</h3>
                    <div class="mb-4 rounded-xl border border-orange-200 bg-orange-50 px-4 py-3 text-sm text-orange-900">
                        <div class="font-semibold">Get TZS 5,000 off your first order</div>
                        <div>Use code <span class="font-bold">FEEDTAN5K</span> at checkout. Valid on orders above TZS 30,000.</div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Fee (TZS)</label>
                            <input type="number" name="delivery_fee" value="{{ old('delivery_fee', $order->delivery_fee) }}" min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                            <select name="payment_method" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Select Payment Method</option>
                                <option value="Cash" {{ old('payment_method', $order->payment_method) == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="M-Pesa" {{ old('payment_method', $order->payment_method) == 'M-Pesa' ? 'selected' : '' }}>M-Pesa</option>
                                <option value="Tigo Pesa" {{ old('payment_method', $order->payment_method) == 'Tigo Pesa' ? 'selected' : '' }}>Tigo Pesa</option>
                                <option value="Airtel Money" {{ old('payment_method', $order->payment_method) == 'Airtel Money' ? 'selected' : '' }}>Airtel Money</option>
                                <option value="Bank Transfer" {{ old('payment_method', $order->payment_method) == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Assign Rider</label>
                            <select name="delivery_rider_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Select Rider (Optional)</option>
                                @foreach($riders as $rider)
                                    @if($rider->is_active)
                                        <option value="{{ $rider->id }}" {{ old('delivery_rider_id', $order->delivery_rider_id) == $rider->id ? 'selected' : '' }}>{{ $rider->name }} ({{ $rider->phone }})</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Promo Code</label>
                            <input
                                type="text"
                                name="promo_code"
                                id="promoCode"
                                value="{{ old('promo_code', (float) $order->discount > 0 ? 'FEEDTAN5K' : '') }}"
                                placeholder="FEEDTAN5K"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg uppercase focus:ring-2 focus:ring-primary-500 focus:border-primary-500"
                            >
                            <p id="promoHelper" class="mt-1 text-xs text-gray-500">Applies TZS 5,000 off on the first online order above TZS 30,000.</p>
                            @error('promo_code')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Order Items -->
                <div class="lg:col-span-2">
                    <h3 class="text-lg font-semibold text-primary-900 mb-4">Order Items</h3>
                    <div class="overflow-x-auto">
                        <table class="w-full mb-4" id="orderItemsTable">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Product</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Price</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Quantity</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Total</th>
                                    <th class="px-4 py-2 text-left text-sm font-medium text-gray-700">Actions</th>
                                </tr>
                            </thead>
                            <tbody id="orderItemsBody">
                                @foreach($order->items as $index => $item)
                                <tr class="border-t">
                                    <td class="px-4 py-2">
                                        <select name="items[{{ $index }}][product_id]" class="product-select w-full px-3 py-1 border border-gray-300 rounded" required>
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                @if($product->is_available_online)
                                                    <option value="{{ $product->id }}" {{ $item->product_id == $product->id ? 'selected' : '' }} data-price="{{ $product->selling_price }}" data-stock="{{ $product->quantity }}">{{ $product->name }} (Stock: {{ $product->quantity }})</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-2 product-price">{{ number_format($item->price, 2) }}</td>
                                    <td class="px-4 py-2">
                                        <input type="number" name="items[{{ $index }}][quantity]" min="1" value="{{ $item->quantity }}" class="item-quantity w-20 px-3 py-1 border border-gray-300 rounded">
                                    </td>
                                    <td class="px-4 py-2 item-total">{{ number_format($item->total, 2) }}</td>
                                    <td class="px-4 py-2">
                                        <button type="button" class="remove-item text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <button type="button" id="addItemBtn" class="px-4 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                            <i class="fas fa-plus mr-2"></i>Add Item
                        </button>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="lg:col-span-2">
                    <div class="bg-gray-50 p-4 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Subtotal:</span>
                                <span class="font-semibold text-primary-900" id="subtotalDisplay">TZS {{ number_format($order->subtotal, 2) }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Delivery Fee:</span>
                                <span class="font-semibold text-primary-900" id="deliveryFeeDisplay">TZS {{ number_format($order->delivery_fee, 2) }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Discount:</span>
                                <span class="font-semibold text-green-700" id="discountDisplay">-TZS {{ number_format($order->discount ?? 0, 2) }}</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Total:</span>
                                <span class="font-bold text-lg text-primary-900" id="totalDisplay">TZS {{ number_format($order->total, 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('notes', $order->notes) }}</textarea>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('online.orders') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Update Order
                </button>
            </div>
        </form>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
let itemIndex = {{ $order->items->count() }};
let editOrderMap;
let marker;

document.addEventListener('DOMContentLoaded', function() {
    // Initial setup
    updateOrderSummary();
    
    // Initialize location picker map
    const initialLat = parseFloat(document.getElementById('deliveryLatitude').value) || -3.3869;
    const initialLng = parseFloat(document.getElementById('deliveryLongitude').value) || 36.6883;
    editOrderMap = L.map('editOrderMap').setView([initialLat, initialLng], 12);
    
    // OpenStreetMap base layer
    const osmLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    });
    
    // World Imagery base layer (Esri)
    const worldImageryLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri, DigitalGlobe, GeoEye, i-cubed, USDA, USGS, AEX, Getmapping, Aerogrid, IGN, IGP, swisstopo, and the GIS User Community'
    });
    
    // Add OSM as default
    osmLayer.addTo(editOrderMap);
    
    // Layer control
    const baseLayers = {
        'OpenStreetMap': osmLayer,
        'World Imagery': worldImageryLayer
    };
    
    L.control.layers(baseLayers).addTo(editOrderMap);
    
    marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(editOrderMap);
    
    marker.on('dragend', function(e) {
        const pos = marker.getLatLng();
        document.getElementById('deliveryLatitude').value = pos.lat.toFixed(7);
        document.getElementById('deliveryLongitude').value = pos.lng.toFixed(7);
    });
    
    editOrderMap.on('click', function(e) {
        marker.setLatLng(e.latlng);
        document.getElementById('deliveryLatitude').value = e.latlng.lat.toFixed(7);
        document.getElementById('deliveryLongitude').value = e.latlng.lng.toFixed(7);
    });
    
    // Update marker when inputs change
    document.getElementById('deliveryLatitude').addEventListener('input', updateMarkerFromInputs);
    document.getElementById('deliveryLongitude').addEventListener('input', updateMarkerFromInputs);
    
    function updateMarkerFromInputs() {
        const lat = parseFloat(document.getElementById('deliveryLatitude').value);
        const lng = parseFloat(document.getElementById('deliveryLongitude').value);
        if (!isNaN(lat) && !isNaN(lng)) {
            marker.setLatLng([lat, lng]);
            editOrderMap.setView([lat, lng], editOrderMap.getZoom());
        }
    }
    
    // Get current location button
    document.getElementById('getCurrentLocationBtn').addEventListener('click', function() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;
                document.getElementById('deliveryLatitude').value = lat.toFixed(7);
                document.getElementById('deliveryLongitude').value = lng.toFixed(7);
                marker.setLatLng([lat, lng]);
                editOrderMap.setView([lat, lng], 15);
            });
        }
    });
    
    // Customer select change
    document.getElementById('customerSelect').addEventListener('change', function() {
        const selected = this.options[this.selectedIndex];
        if (selected.value) {
            document.getElementById('customerName').value = selected.dataset.name;
            document.getElementById('customerPhone').value = selected.dataset.phone;
            document.getElementById('customerEmail').value = selected.dataset.email;
            document.getElementById('customerAddress').value = selected.dataset.address;
        }
    });
    
    // Add item button
    document.getElementById('addItemBtn').addEventListener('click', function() {
        addNewItem();
    });

    // Remove item buttons (delegated event listener)
    document.getElementById('orderItemsBody').addEventListener('click', function(e) {
        if (e.target.closest('.remove-item')) {
            const row = e.target.closest('tr');
            if (document.querySelectorAll('#orderItemsBody tr').length > 1) {
                row.remove();
                updateOrderSummary();
            }
        }
    });

    // Product select change
    document.getElementById('orderItemsBody').addEventListener('change', function(e) {
        if (e.target.classList.contains('product-select')) {
            const row = e.target.closest('tr');
            updateRowPrice(row);
            updateOrderSummary();
        }
    });

    // Quantity change
    document.getElementById('orderItemsBody').addEventListener('input', function(e) {
        if (e.target.classList.contains('item-quantity')) {
            const row = e.target.closest('tr');
            updateRowTotal(row);
            updateOrderSummary();
        }
    });

    // Delivery fee change
    document.querySelector('input[name="delivery_fee"]').addEventListener('input', function() {
        updateOrderSummary();
    });

    document.getElementById('promoCode').addEventListener('input', function() {
        this.value = this.value.toUpperCase();
        updateOrderSummary();
    });
});

function addNewItem() {
    const tbody = document.getElementById('orderItemsBody');
    const templateRow = tbody.querySelector('tr:first-child');
    const newRow = templateRow.cloneNode(true);
    
    // Update names and values
    newRow.querySelectorAll('[name]').forEach(function(input) {
        input.name = input.name.replace(/\[\d+\]/, '[' + itemIndex + ']');
        if (input.tagName === 'SELECT') {
            input.selectedIndex = 0;
        } else if (input.classList.contains('item-quantity')) {
            input.value = 1;
        }
    });
    
    newRow.querySelector('.product-price').textContent = '0.00';
    newRow.querySelector('.item-total').textContent = '0.00';
    
    tbody.appendChild(newRow);
    itemIndex++;
}

function updateRowPrice(row) {
    const select = row.querySelector('.product-select');
    const priceCell = row.querySelector('.product-price');
    if (select.selectedIndex > 0) {
        const price = select.options[select.selectedIndex].dataset.price;
        priceCell.textContent = parseFloat(price).toFixed(2);
        updateRowTotal(row);
    } else {
        priceCell.textContent = '0.00';
        updateRowTotal(row);
    }
}

function updateRowTotal(row) {
    const price = parseFloat(row.querySelector('.product-price').textContent) || 0;
    const qty = parseInt(row.querySelector('.item-quantity').value) || 0;
    row.querySelector('.item-total').textContent = (price * qty).toFixed(2);
}

function updateOrderSummary() {
    let subtotal = 0;
    document.querySelectorAll('.item-total').forEach(function(el) {
        subtotal += parseFloat(el.textContent) || 0;
    });
    
    const deliveryFee = parseFloat(document.querySelector('input[name="delivery_fee"]').value) || 0;
    const promoCode = (document.getElementById('promoCode').value || '').trim().toUpperCase();
    const discount = promoCode === 'FEEDTAN5K' && subtotal >= 30000 ? 5000 : 0;
    const total = Math.max(0, subtotal + deliveryFee - discount);
    
    document.getElementById('subtotalDisplay').textContent = 'TZS ' + subtotal.toFixed(2);
    document.getElementById('deliveryFeeDisplay').textContent = 'TZS ' + deliveryFee.toFixed(2);
    document.getElementById('discountDisplay').textContent = '-TZS ' + discount.toFixed(2);
    document.getElementById('totalDisplay').textContent = 'TZS ' + total.toFixed(2);
    document.getElementById('promoHelper').textContent = promoCode === 'FEEDTAN5K' && subtotal < 30000
        ? 'FEEDTAN5K needs a subtotal above TZS 30,000. First-order eligibility is checked on save.'
        : 'Applies TZS 5,000 off on the first online order above TZS 30,000.';
}
</script>
@endsection
