@extends('layouts.app')

@section('page-title', 'Delivery Map')

@section('content')
<div class="animate-[fadeIn_0.4s_ease] space-y-6">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <h1 class="text-xl font-bold" :class="darkMode?'text-white':'text-primary-900'">Delivery Tracking Map</h1>
    </div>

    <!-- Filter Section -->
    <div class="card rounded-2xl p-4">
        <h3 class="font-semibold mb-3">Filter by Status</h3>
        <form action="{{ route('online.delivery.map') }}" method="GET" class="flex flex-wrap gap-2">
            @foreach($allStatuses as $status)
                @php
                    $statusLabel = ucwords(str_replace('_', ' ', $status));
                    $isChecked = in_array($status, $statusFilter);
                @endphp
                <label class="flex items-center gap-2 px-3 py-2 bg-gray-50 rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                    <input type="checkbox" name="status[]" value="{{ $status }}" {{ $isChecked ? 'checked' : '' }} class="rounded text-primary-600 focus:ring-primary-500">
                    <span class="text-sm">{{ $statusLabel }}</span>
                </label>
            @endforeach
            <div class="flex gap-2 ml-auto">
                <a href="{{ route('online.delivery.map') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">Reset</a>
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">Apply Filter</button>
            </div>
        </form>
    </div>

    <div class="card rounded-2xl overflow-hidden">
        <div id="map" class="w-full h-[500px]"></div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    const storeLat = {{ $storeLat }};
    const storeLng = {{ $storeLng }};
    const routes = @json($routes);
    
    const map = L.map('map').setView([storeLat, storeLng], 10);
    
    // OpenStreetMap base layer
    const osmLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    });
    
    // World Imagery base layer (Esri)
    const worldImageryLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Tiles &copy; Esri &mdash; Source: Esri, DigitalGlobe, GeoEye, i-cubed, USDA, USGS, AEX, Getmapping, Aerogrid, IGN, IGP, swisstopo, and the GIS User Community'
    });
    
    // Add OSM as default
    osmLayer.addTo(map);
    
    // Layer control
    const baseLayers = {
        'OpenStreetMap': osmLayer,
        'World Imagery': worldImageryLayer
    };
    
    L.control.layers(baseLayers).addTo(map);
    
    // Order status to color mapping
    const statusColors = {
        'pending': '#eab308',
        'confirmed': '#3b82f6',
        'preparing': '#a855f7',
        'ready': '#06b6d4',
        'out_for_delivery': '#f97316',
        'delivered': '#22c55e',
        'cancelled': '#ef4444'
    };
    
    // Add store marker
    L.marker([storeLat, storeLng])
        .addTo(map)
        .bindPopup("<b>Store</b>")
        .openPopup();
    
    // Object to track markers
    const riderMarkers = {};
    const orderMarkers = {};
    const orderRoutes = {};
    
    // Initial order markers
    @foreach($allOrders as $order)
        // Add order marker
        orderMarkers[{{ $order->id }}] = L.circleMarker([{{ $order->delivery_latitude }}, {{ $order->delivery_longitude }}], {
            radius: 8,
            fillColor: statusColors['{{ $order->status }}'],
            color: '#fff',
            weight: 2,
            fillOpacity: 0.8
        }).addTo(map).bindPopup(`
            <div class="p-2 min-w-[250px]">
                <h4 class="font-bold text-base text-gray-900 mb-1">Order #{{ $order->order_number }}</h4>
                <p class="text-sm text-gray-700 mb-1"><strong>Customer:</strong> {{ $order->customer_name }}</p>
                <p class="text-sm text-gray-700 mb-1"><strong>Phone:</strong> <a href="tel:{{ $order->customer_phone }}">{{ $order->customer_phone }}</a></p>
                <p class="text-sm text-gray-700 mb-2"><strong>Address:</strong> {{ $order->delivery_address }}</p>
                <a href="{{ route('online.orders.show', $order) }}" class="text-primary-600 hover:underline text-sm">View Order</a>
            </div>
        `);
        
        // Add route if available
        if (routes['{{ $order->id }}']) {
            const coords = routes['{{ $order->id }}'].features[0].geometry.coordinates;
            const points = coords.map(c => [c[1], c[0]]);
            orderRoutes[{{ $order->id }}] = L.polyline(points, { color: statusColors['{{ $order->status }}'], weight: 3, opacity: 0.7 }).addTo(map);
        }
    @endforeach
    
    // Initial rider markers
    @foreach($riders as $rider)
        @if($rider->latitude && $rider->longitude)
            riderMarkers[{{ $rider->id }}] = L.marker([{{ $rider->latitude }}, {{ $rider->longitude }}])
                .addTo(map)
                .bindPopup(`
                    <div class="p-2">
                        <h4 class="font-bold text-base text-gray-900 mb-1">Rider: {{ $rider->name }}</h4>
                        <p class="text-sm text-gray-700"><strong>Phone:</strong> <a href="tel:{{ $rider->phone }}">{{ $rider->phone }}</a></p>
                    </div>
                `);
        @elseif($rider->latest_location && $rider->latest_location.latitude && $rider->latest_location.longitude)
            riderMarkers[{{ $rider->id }}] = L.marker([{{ $rider->latest_location->latitude }}, {{ $rider->latest_location->longitude }}])
                .addTo(map)
                .bindPopup(`
                    <div class="p-2">
                        <h4 class="font-bold text-base text-gray-900 mb-1">Rider: {{ $rider->name }}</h4>
                        <p class="text-sm text-gray-700"><strong>Phone:</strong> <a href="tel:{{ $rider->phone }}">{{ $rider->phone }}</a></p>
                    </div>
                `);
        @endif
    @endforeach
    
    // Function to refresh rider locations
    async function refreshRiders() {
        try {
            const response = await fetch('/api/realtime/riders');
            const riders = await response.json();
            
            riders.forEach(rider => {
                if (rider.latest_location && rider.latest_location.latitude && rider.latest_location.longitude) {
                    if (riderMarkers[rider.id]) {
                        riderMarkers[rider.id].setLatLng([rider.latest_location.latitude, rider.latest_location.longitude]);
                    } else {
                        riderMarkers[rider.id] = L.marker([rider.latest_location.latitude, rider.latest_location.longitude])
                            .addTo(map)
                            .bindPopup(`
                                <div class="p-2">
                                    <h4 class="font-bold text-base text-gray-900 mb-1">Rider: ${rider.name}</h4>
                                    <p class="text-sm text-gray-700"><strong>Phone:</strong> <a href="tel:${rider.phone}">${rider.phone}</a></p>
                                </div>
                            `);
                    }
                }
            });
        } catch (err) {
            console.error('Error refreshing riders:', err);
        }
    }
    
    // Refresh every 3 seconds
    setInterval(refreshRiders, 3000);
</script>
@endsection
