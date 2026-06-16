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

    const coordinates = [
        @foreach($orders as $order)
            [{{ $order->delivery_longitude }}, {{ $order->delivery_latitude }}],
        @endforeach
    ];

    map.on('load', function() {
        // Add line layer connecting all customer locations
        if (coordinates.length > 1) {
            map.addSource('customer-route', {
                'type': 'geojson',
                'data': {
                    'type': 'Feature',
                    'properties': {},
                    'geometry': {
                        'type': 'LineString',
                        'coordinates': coordinates
                    }
                }
            });

            map.addLayer({
                'id': 'customer-route',
                'type': 'line',
                'source': 'customer-route',
                'layout': {
                    'line-join': 'round',
                    'line-cap': 'round'
                },
                'paint': {
                    'line-color': '#3b82f6',
                    'line-width': 4,
                    'line-opacity': 0.7
                }
            });
        }
    });

    // Add markers for all customer locations
    @foreach($orders as $index => $order)
        (function() {
            const el = document.createElement('div');
            el.className = 'w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white shadow-lg border-2 border-white cursor-pointer';
            el.style.backgroundColor = statusColors['{{ $order->status }}'];
            el.innerHTML = '{{ $index + 1 }}';
            
            const marker = new maplibregl.Marker(el)
                .setLngLat([{{ $order->delivery_longitude }}, {{ $order->delivery_latitude }}])
                .setPopup(new maplibregl.Popup({ offset: 25, maxWidth: '300px' }).setHTML(`
                    <div class="p-4 min-w-[280px]">
                        <!-- Order Number -->
                        <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-100">
                            <h4 class="font-bold text-base text-gray-900 m-0">Order #{{ $order->order_number }}</h4>
                            <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-semibold">#{{ $index + 1 }}</span>
                        </div>

                        <!-- Details Grid -->
                        <div class="grid grid-cols-1 gap-2 text-sm mb-3">
                            <div class="flex gap-2">
                                <span class="text-gray-500 font-medium min-w-[70px]">Customer:</span>
                                <span class="text-gray-900 font-medium">{{ $order->customer_name }}</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="text-gray-500 font-medium min-w-[70px]">Phone:</span>
                                <a href="tel:{{ $order->customer_phone }}" class="text-blue-600 hover:text-blue-800">{{ $order->customer_phone }}</a>
                            </div>
                            <div class="flex gap-2">
                                <span class="text-gray-500 font-medium min-w-[70px]">Address:</span>
                                <span class="text-gray-700">{{ $order->delivery_address }}</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="text-gray-500 font-medium min-w-[70px]">Status:</span>
                                <span class="px-2 py-0.5 rounded-full text-xs font-semibold" style="background-color: ${statusColors['{{ $order->status }}']}20; color: ${statusColors['{{ $order->status }}']}">{{ ucwords(str_replace('_', ' ', $order->status)) }}</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="text-gray-500 font-medium min-w-[70px]">Total:</span>
                                <span class="text-green-700 font-bold text-base">TZS {{ number_format($order->total, 2) }}</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="text-gray-500 font-medium min-w-[70px]">Date:</span>
                                <span class="text-gray-500 text-xs">{{ $order->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                        </div>

                        <!-- View Order Button -->
                        <div class="pt-2 border-t border-gray-100">
                            <a href="{{ route('online.orders.show', $order) }}" class="w-full inline-flex items-center justify-center gap-1 bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium text-sm transition-colors">
                                <i class="fas fa-eye"></i>
                                View Order
                            </a>
                        </div>
                    </div>
                `))
                .addTo(map);
        })();
    @endforeach
</script>
@endsection
