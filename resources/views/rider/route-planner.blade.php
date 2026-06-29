@extends('layouts.app')

@section('page-title', 'Route Planner')

@section('content')
<div class="animate-[fadeIn_0.4s_ease] space-y-6">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="{{ route('rider.dashboard') }}" class="text-primary-600 hover:text-primary-800 mb-2 inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
                <h2 class="text-xl font-bold text-primary-900">Route Planner</h2>
            </div>
        </div>

        <div class="mb-6">
            <div class="card rounded-2xl overflow-hidden">
                <div id="route-map" class="w-full h-[400px]"></div>
            </div>
        </div>

        <div>
            <h3 class="text-lg font-semibold text-primary-900 mb-4">Pending Deliveries</h3>
            <div class="space-y-3">
                @forelse($assignedOrders as $order)
                <div class="p-4 border rounded-lg">
                    <div class="flex justify-between items-start">
                        <div>
                            <div class="font-semibold">{{ $order->short_customer_reference }}</div>
                            <div class="text-sm text-gray-600">{{ $order->customer_name }}</div>
                            <div class="text-sm text-gray-500">{{ $order->delivery_address }}</div>
                        </div>
                        <a href="{{ route('rider.orders.show', $order) }}" class="text-primary-600 hover:text-primary-800">
                            <i class="fas fa-eye"></i>
                        </a>
                    </div>
                </div>
                @empty
                <div class="text-center text-gray-500 py-8">
                    No pending deliveries.
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    const storeLat = {{ $storeLat }};
    const storeLng = {{ $storeLng }};
    const routes = @json($routes);
    
    const map = L.map('route-map').setView([storeLat, storeLng], 12);
    
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
    
    // Add store marker
    L.marker([storeLat, storeLng])
        .addTo(map)
        .bindPopup('<strong>Store</strong>')
        .openPopup();
    
    // Add markers for assigned orders with routes
    @foreach($assignedOrders as $order)
        @if($order->delivery_latitude && $order->delivery_longitude)
            const orderMarker{{ $order->id }} = L.circleMarker([{{ $order->delivery_latitude }}, {{ $order->delivery_longitude }}], {
                radius: 10,
                fillColor: '{{ $order->status === "ready" ? "#06b6d4" : "#f97316" }}',
                color: '#fff',
                weight: 3,
                fillOpacity: 0.9
            }).addTo(map).bindPopup(`
                <div class="p-2 min-w-[250px]">
                    <h4 class="font-bold text-base text-gray-900 mb-1">Order {{ $order->short_customer_reference }}</h4>
                    <p class="text-sm text-gray-700 mb-1"><strong>Customer:</strong> {{ $order->customer_name }}</p>
                    <p class="text-sm text-gray-700 mb-1"><strong>Phone:</strong> <a href="tel:{{ $order->customer_phone }}">{{ $order->customer_phone }}</a></p>
                    <p class="text-sm text-gray-700 mb-2"><strong>Address:</strong> {{ $order->delivery_address }}</p>
                    <a href="{{ route('rider.orders.show', $order) }}" class="text-primary-600 hover:underline text-sm">View Order Details</a>
                </div>
            `);
            
            // Add route if available
            if (routes['{{ $order->id }}']) {
                const coords{{ $order->id }} = routes['{{ $order->id }}'].features[0].geometry.coordinates;
                const points{{ $order->id }} = coords{{ $order->id }}.map(c => [c[1], c[0]]);
                L.polyline(points{{ $order->id }}, { color: '{{ $order->status === "ready" ? "#06b6d4" : "#f97316" }}', weight: 4, opacity: 0.8 }).addTo(map);
            }
        @endif
    @endforeach
</script>
@endsection
