@extends('layouts.app')

@section('page-title', 'Delivery Map')

@section('content')
<div class="animate-[fadeIn_0.4s_ease] space-y-6">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <h1 class="text-xl font-bold" :class="darkMode?'text-white':'text-primary-900'">Delivery Tracking Map</h1>
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
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);
    
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
    
    // Add markers and routes for active orders
    @foreach($activeOrders as $index => $order)
        // Add order marker
        const orderMarker_{{ $order->id }} = L.circleMarker([{{ $order->delivery_latitude }}, {{ $order->delivery_longitude }}], {
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
            L.polyline(points, { color: '#3b82f6', weight: 4, opacity: 0.7 }).addTo(map);
        }
    @endforeach
    
    // Add rider markers
    @foreach($riders as $rider)
        @if($rider->latitude && $rider->longitude)
            const riderMarker_{{ $rider->id }} = L.marker([{{ $rider->latitude }}, {{ $rider->longitude }}])
                .addTo(map)
                .bindPopup(`
                    <div class="p-2">
                        <h4 class="font-bold text-base text-gray-900 mb-1">Rider: {{ $rider->name }}</h4>
                        <p class="text-sm text-gray-700"><strong>Phone:</strong> <a href="tel:{{ $rider->phone }}">{{ $rider->phone }}</a></p>
                    </div>
                `);
        @endif
    @endforeach
</script>
@endsection
