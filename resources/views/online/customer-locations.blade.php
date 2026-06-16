@extends('layouts.app')

@section('page-title', 'Customer Locations Map')

@section('content')
<div class="animate-[fadeIn_0.4s_ease] space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold" :class="darkMode?'text-white':'text-primary-900'">Customer Locations</h1>
        <a href="{{ route('online.delivery') }}" class="text-primary-600 hover:text-primary-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Delivery Management
        </a>
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

<script src="https://cdn.jsdelivr.net/npm/maplibre-gl@3.6.2/dist/maplibre-gl.js"></script>
<script>
    const map = new maplibregl.Map({
        container: 'customer-map',
        style: 'https://demotiles.maplibre.org/style.json',
        center: [35.5296, -6.3690],
        zoom: 6
    });

    map.addControl(new maplibregl.NavigationControl());
    map.addControl(new maplibregl.FullscreenControl());

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

    // Add markers for all customer locations
    @foreach($orders as $order)
        (function() {
            const el = document.createElement('div');
            el.className = 'w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white shadow-lg border-2 border-white cursor-pointer';
            el.style.backgroundColor = statusColors['{{ $order->status }}'];
            el.innerHTML = '{{ $order->id }}';
            
            const marker = new maplibregl.Marker(el)
                .setLngLat([{{ $order->delivery_longitude }}, {{ $order->delivery_latitude }}])
                .setPopup(new maplibregl.Popup({ offset: 25 }).setHTML(`
                    <div class="p-4 min-w-[250px]">
                        <h4 class="font-bold text-base mb-2">Order #{{ $order->order_number }}</h4>
                        <div class="space-y-1 text-sm">
                            <p><strong>Customer:</strong> {{ $order->customer_name }}</p>
                            <p><strong>Phone:</strong> {{ $order->customer_phone }}</p>
                            <p><strong>Address:</strong> {{ $order->delivery_address }}</p>
                            <p><strong>Status:</strong> <span class="px-2 py-0.5 rounded-full text-xs" style="background-color: ${statusColors['{{ $order->status }}']}20; color: ${statusColors['{{ $order->status }}']}">{{ ucwords(str_replace('_', ' ', $order->status)) }}</span></p>
                            <p><strong>Total:</strong> TZS {{ number_format($order->total, 2) }}</p>
                            <p class="text-gray-500 text-xs">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <a href="{{ route('online.orders.show', $order) }}" class="mt-3 inline-block text-primary-600 hover:text-primary-800 font-medium text-sm">
                            View Order →
                        </a>
                    </div>
                `))
                .addTo(map);
        })();
    @endforeach
</script>
@endsection
