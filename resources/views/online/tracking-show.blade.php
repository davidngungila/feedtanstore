@extends('layouts.app')

@section('page-title', 'Track Order - ' . $order->order_number)

@section('content')
<div class="animate-[fadeIn_0.4s_ease] space-y-6">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <h1 class="text-xl font-bold" :class="darkMode?'text-white':'text-primary-900'">Track Order: {{ $order->order_number }}</h1>
        <a href="{{ route('online.tracking') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">
            <i class="fas fa-arrow-left mr-2"></i> Back to Orders
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Order Details -->
        <div class="card rounded-2xl p-6 space-y-4">
            <h2 class="text-lg font-semibold text-primary-900">Order Details</h2>
            
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Customer</span>
                    <span class="font-medium">{{ $order->customer_name }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Phone</span>
                    <span class="font-medium">{{ $order->customer_phone }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Status</span>
                    <span id="orderStatus" class="px-2 py-1 rounded-full text-xs font-semibold 
                        @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                        @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                        @elseif($order->status === 'preparing') bg-purple-100 text-purple-800
                        @elseif($order->status === 'ready') bg-cyan-100 text-cyan-800
                        @elseif($order->status === 'out_for_delivery') bg-orange-100 text-orange-800
                        @elseif($order->status === 'delivered') bg-green-100 text-green-800
                        @else bg-red-100 text-red-800 @endif">
                        {{ ucwords(str_replace('_', ' ', $order->status)) }}
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total</span>
                    <span class="font-semibold text-primary-600">TZS {{ number_format($order->total, 2) }}</span>
                </div>
            </div>

            <hr class="border-gray-200">
            
            <!-- Rider Info -->
            <div id="riderInfo" class="space-y-3">
                <h3 class="text-md font-semibold text-primary-900">Assigned Rider</h3>
                @if($order->rider)
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Name</span>
                            <span id="riderName" class="font-medium">{{ $order->rider->name }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Phone</span>
                            <a href="tel:{{ $order->rider->phone }}" id="riderPhone" class="font-medium text-primary-600">{{ $order->rider->phone }}</a>
                        </div>
                    </div>
                @else
                    <p class="text-gray-500">No rider assigned yet</p>
                @endif
            </div>
        </div>

        <!-- Map -->
        <div class="card rounded-2xl overflow-hidden">
            <div id="map" class="w-full h-[500px]"></div>
        </div>
    </div>

    <!-- Order Items -->
    <div class="card rounded-2xl p-6">
        <h2 class="text-lg font-semibold text-primary-900 mb-4">Order Items</h2>
        <div class="divide-y">
            @foreach($order->items as $item)
                <div class="flex items-center justify-between py-3">
                    <div>
                        <p class="font-medium">{{ $item->product?->name ?? 'Product' }}</p>
                        <p class="text-sm text-gray-500">{{ $item->quantity }} x TZS {{ number_format($item->price, 2) }}</p>
                    </div>
                    <span class="font-semibold">TZS {{ number_format($item->total, 2) }}</span>
                </div>
            @endforeach
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    const orderId = {{ $order->id }};
    const orderNumber = '{{ $order->order_number }}';
    const storeLat = {{ $settings->store_latitude ?? -3.3869 }};
    const storeLng = {{ $settings->store_longitude ?? 36.6883 }};
    const deliveryLat = {{ $order->delivery_latitude ?? 'null' }};
    const deliveryLng = {{ $order->delivery_longitude ?? 'null' }};
    
    // Initialize map
    let map = L.map('map');
    if (deliveryLat && deliveryLng) {
        map.setView([deliveryLat, deliveryLng], 13);
    } else {
        map.setView([storeLat, storeLng], 10);
    }
    
    // Base layer
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);
    
    // Markers
    const storeMarker = L.marker([storeLat, storeLng])
        .addTo(map)
        .bindPopup('<b>Store</b>');
        
    let deliveryMarker = null;
    if (deliveryLat && deliveryLng) {
        deliveryMarker = L.circleMarker([deliveryLat, deliveryLng], {
            radius: 10,
            fillColor: '#3b82f6',
            color: '#fff',
            weight: 3,
            fillOpacity: 0.8
        }).addTo(map).bindPopup('<b>Delivery Location</b><br>{{ $order->delivery_address }}');
    }
    
    let riderMarker = null;
    
    // Function to refresh order and rider data
    async function refreshOrder() {
        try {
            const response = await fetch(`/api/tracking/${orderNumber}`);
            const data = await response.json();
            
            // Update order status
            const statusEl = document.getElementById('orderStatus');
            const statuses = {
                'pending': 'bg-yellow-100 text-yellow-800',
                'confirmed': 'bg-blue-100 text-blue-800',
                'preparing': 'bg-purple-100 text-purple-800',
                'ready': 'bg-cyan-100 text-cyan-800',
                'out_for_delivery': 'bg-orange-100 text-orange-800',
                'delivered': 'bg-green-100 text-green-800',
                'cancelled': 'bg-red-100 text-red-800'
            };
            statusEl.className = `px-2 py-1 rounded-full text-xs font-semibold ${statuses[data.order.status]}`;
            statusEl.textContent = data.order.status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
            
            // Update rider info
            const riderInfo = document.getElementById('riderInfo');
            if (data.rider) {
                riderInfo.innerHTML = `
                    <h3 class="text-md font-semibold text-primary-900">Assigned Rider</h3>
                    <div class="space-y-2">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Name</span>
                            <span id="riderName" class="font-medium">${data.rider.name}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Phone</span>
                            <a href="tel:${data.rider.phone}" id="riderPhone" class="font-medium text-primary-600">${data.rider.phone}</a>
                        </div>
                    </div>
                `;
                
                // Update rider marker
                if (data.current_location) {
                    if (riderMarker) {
                        riderMarker.setLatLng([data.current_location.latitude, data.current_location.longitude]);
                    } else {
                        riderMarker = L.marker([data.current_location.latitude, data.current_location.longitude])
                            .addTo(map)
                            .bindPopup(`<b>Rider: ${data.rider.name}</b>`);
                    }
                }
            } else {
                riderInfo.innerHTML = `
                    <h3 class="text-md font-semibold text-primary-900">Assigned Rider</h3>
                    <p class="text-gray-500">No rider assigned yet</p>
                `;
                if (riderMarker) {
                    map.removeLayer(riderMarker);
                    riderMarker = null;
                }
            }
        } catch (err) {
            console.error('Error refreshing order:', err);
        }
    }
    
    // Initial refresh and then every 3 seconds
    refreshOrder();
    setInterval(refreshOrder, 3000);
</script>
@endsection