@extends('layouts.app')

@section('page-title', 'Customer Locations Map')

@section('content')
<div class="animate-[fadeIn_0.4s_ease] space-y-6">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <h1 class="text-xl font-bold" :class="darkMode?'text-white':'text-primary-900'">Customer Locations</h1>
        <a href="{{ route('online.delivery') }}" class="text-primary-600 hover:text-primary-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Delivery Management
        </a>
    </div>

    <!-- Filter Section -->
    <div class="card rounded-2xl p-4">
        <h3 class="font-semibold mb-3">Filter by Status</h3>
        <form action="{{ route('online.customer.locations') }}" method="GET" class="flex flex-wrap gap-2">
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
                <a href="{{ route('online.customer.locations') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">Reset</a>
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">Apply Filter</button>
            </div>
        </form>
    </div>

    <div class="card rounded-2xl overflow-hidden">
        <div id="customer-map" class="w-full h-[600px]"></div>
    </div>

    <!-- Legend -->
    <div class="card rounded-2xl p-4">
        <h3 class="font-semibold mb-3">Order Status Legend</h3>
        <div class="flex flex-wrap gap-4">
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded-full bg-yellow-500"></div>
                <span class="text-sm">Pending</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded-full bg-blue-500"></div>
                <span class="text-sm">Confirmed</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded-full bg-purple-500"></div>
                <span class="text-sm">Preparing</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded-full bg-cyan-500"></div>
                <span class="text-sm">Ready</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded-full bg-orange-500"></div>
                <span class="text-sm">Out for Delivery</span>
            </div>
            <div class="flex items-center gap-2">
                <div class="w-4 h-4 rounded-full bg-green-500"></div>
                <span class="text-sm">Delivered</span>
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
    
    const map = L.map('customer-map').setView([storeLat, storeLng], 10);
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
        .bindPopup("<b>Store</b>");
    
    // Add markers and routes for orders
    @foreach($orders as $index => $order)
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
            L.polyline(points, { color: statusColors['{{ $order->status }}'], weight: 3, opacity: 0.7 }).addTo(map);
        }
    @endforeach
</script>
@endsection
