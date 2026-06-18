@extends('layouts.app')

@section('page-title', 'Create Online Order')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Create New Online Order</h2>
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

        <form action="{{ route('online.orders.store') }}" method="POST" id="orderForm">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Customer Info -->
                <div class="lg:col-span-2">
                    <h3 class="text-lg font-semibold text-primary-900 mb-4">Customer Information</h3>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Select Existing Customer (Optional)</label>
                        <select name="customer_id" id="customerSelect" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Or fill in new customer details below</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}" data-name="{{ $customer->name }}" data-phone="{{ $customer->phone }}" data-email="{{ $customer->email }}" data-address="{{ $customer->address }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Customer Name *</label>
                            <input type="text" name="customer_name" id="customerName" value="{{ old('customer_name') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Customer Phone *</label>
                            <input type="text" name="customer_phone" id="customerPhone" value="{{ old('customer_phone') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Customer Email</label>
                            <input type="email" name="customer_email" id="customerEmail" value="{{ old('customer_email') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Address *</label>
                            <input type="text" name="delivery_address" id="customerAddress" value="{{ old('delivery_address') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                    </div>
                    
                    <!-- Location Picker -->
                    <div class="mt-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Location (Optional)</label>
                        <div class="flex gap-3 mb-3">
                            <input type="number" step="0.0000001" name="delivery_latitude" id="deliveryLatitude" value="{{ old('delivery_latitude') }}" class="form-input w-full" placeholder="Latitude">
                            <input type="number" step="0.0000001" name="delivery_longitude" id="deliveryLongitude" value="{{ old('delivery_longitude') }}" class="form-input w-full" placeholder="Longitude">
                            <button type="button" id="getCurrentLocationBtn" class="bg-blue-100 text-blue-700 px-4 py-2 rounded-lg hover:bg-blue-200 transition-colors">
                                <i class="fas fa-location-crosshairs mr-1"></i> Current
                            </button>
                        </div>
                        <div id="createOrderMap" class="w-full h-[300px] rounded-lg border border-gray-300"></div>
                    </div>
                </div>

                <!-- Order Details -->
                <div class="lg:col-span-2">
                    <h3 class="text-lg font-semibold text-primary-900 mb-4">Order Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Delivery Fee (TZS)</label>
                            <input type="number" name="delivery_fee" value="{{ old('delivery_fee', 0) }}" min="0" step="0.01" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                            <select name="payment_method" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Select Payment Method</option>
                                <option value="Cash" {{ old('payment_method') == 'Cash' ? 'selected' : '' }}>Cash</option>
                                <option value="M-Pesa" {{ old('payment_method') == 'M-Pesa' ? 'selected' : '' }}>M-Pesa</option>
                                <option value="Tigo Pesa" {{ old('payment_method') == 'Tigo Pesa' ? 'selected' : '' }}>Tigo Pesa</option>
                                <option value="Airtel Money" {{ old('payment_method') == 'Airtel Money' ? 'selected' : '' }}>Airtel Money</option>
                                <option value="Bank Transfer" {{ old('payment_method') == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Assign Rider</label>
                            <select name="delivery_rider_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                <option value="">Select Rider (Optional)</option>
                                @foreach($riders as $rider)
                                    @if($rider->is_active)
                                        <option value="{{ $rider->id }}" {{ old('delivery_rider_id') == $rider->id ? 'selected' : '' }}>{{ $rider->name }} ({{ $rider->phone }})</option>
                                    @endif
                                @endforeach
                            </select>
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
                                <tr class="border-t">
                                    <td class="px-4 py-2">
                                        <select name="items[0][product_id]" class="product-select w-full px-3 py-1 border border-gray-300 rounded" required>
                                            <option value="">Select Product</option>
                                            @foreach($products as $product)
                                                @if($product->is_available_online && $product->quantity > 0)
                                                    <option value="{{ $product->id }}" data-price="{{ $product->selling_price }}" data-stock="{{ $product->quantity }}">{{ $product->name }} (Stock: {{ $product->quantity }})</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </td>
                                    <td class="px-4 py-2 product-price">0.00</td>
                                    <td class="px-4 py-2">
                                        <input type="number" name="items[0][quantity]" min="1" value="1" class="item-quantity w-20 px-3 py-1 border border-gray-300 rounded">
                                    </td>
                                    <td class="px-4 py-2 item-total">0.00</td>
                                    <td class="px-4 py-2">
                                        <button type="button" class="remove-item text-red-600 hover:text-red-800"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
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
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div>
                                <span class="text-sm text-gray-600">Subtotal:</span>
                                <span class="font-semibold text-primary-900" id="subtotalDisplay">TZS 0.00</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Delivery Fee:</span>
                                <span class="font-semibold text-primary-900" id="deliveryFeeDisplay">TZS 0.00</span>
                            </div>
                            <div>
                                <span class="text-sm text-gray-600">Total:</span>
                                <span class="font-bold text-lg text-primary-900" id="totalDisplay">TZS 0.00</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                <div class="lg:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea name="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('online.orders') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Create Order
                </button>
            </div>
        </form>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
let itemIndex = 1;
let createOrderMap;
let marker;

document.addEventListener('DOMContentLoaded', function() {
    // Initial setup
    updateOrderSummary();
    
    // Initialize location picker map
    const initialLat = parseFloat(document.getElementById('deliveryLatitude').value) || -3.3869;
    const initialLng = parseFloat(document.getElementById('deliveryLongitude').value) || 36.6883;
    createOrderMap = L.map('createOrderMap').setView([initialLat, initialLng], 12);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(createOrderMap);
    
    marker = L.marker([initialLat, initialLng], { draggable: true }).addTo(createOrderMap);
    
    marker.on('dragend', function(e) {
        const pos = marker.getLatLng();
        document.getElementById('deliveryLatitude').value = pos.lat.toFixed(7);
        document.getElementById('deliveryLongitude').value = pos.lng.toFixed(7);
    });
    
    createOrderMap.on('click', function(e) {
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
            createOrderMap.setView([lat, lng], createOrderMap.getZoom());
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
                createOrderMap.setView([lat, lng], 15);
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
        } else {
            document.getElementById('customerName').value = '';
            document.getElementById('customerPhone').value = '';
            document.getElementById('customerEmail').value = '';
            document.getElementById('customerAddress').value = '';
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
});

function addNewItem() {
    const tbody = document.getElementById('orderItemsBody');
    const templateRow = tbody.querySelector('tr:first-child');
    const newRow = templateRow.cloneNode(true);
    
    // Update names and values
    newRow.querySelectorAll('[name]').forEach(function(input) {
        input.name = input.name.replace('[0]', '[' + itemIndex + ']');
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
    const total = subtotal + deliveryFee;
    
    document.getElementById('subtotalDisplay').textContent = 'TZS ' + subtotal.toFixed(2);
    document.getElementById('deliveryFeeDisplay').textContent = 'TZS ' + deliveryFee.toFixed(2);
    document.getElementById('totalDisplay').textContent = 'TZS ' + total.toFixed(2);
}
</script>
@endsection