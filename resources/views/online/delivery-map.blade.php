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

<script src="https://cdn.jsdelivr.net/npm/maplibre-gl@3.6.2/dist/maplibre-gl.js"></script>
<script>
    const map = new maplibregl.Map({
        container: 'map',
        style: 'https://demotiles.maplibre.org/style.json',
        center: [35.5296, -6.3690],
        zoom: 6
    });

    map.addControl(new maplibregl.NavigationControl());

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

    // Add markers for active orders
    @foreach($activeOrders as $index => $order)
        const el_{{ $order->id }} = document.createElement('div');
        el_{{ $order->id }}.className = 'w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white shadow-lg border-2 border-white cursor-pointer';
        el_{{ $order->id }}.style.backgroundColor = statusColors['{{ $order->status }}'];
        el_{{ $order->id }}.innerHTML = '{{ $index + 1 }}';
        
        new maplibregl.Marker(el_{{ $order->id }})
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
                        @if($order->rider)
                            <div class="flex gap-2">
                                <span class="text-gray-500 font-medium min-w-[70px]">Rider:</span>
                                <span class="text-gray-900">{{ $order->rider->name }} ({{ $order->rider->phone }})</span>
                            </div>
                        @endif
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
    @endforeach

    // Optionally add markers for riders
    @foreach($riders as $rider)
        @if($rider->latitude && $rider->longitude)
            const rider_{{ $rider->id }} = document.createElement('div');
            rider_{{ $rider->id }}.className = 'w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold text-white shadow-lg border-2 border-white cursor-pointer bg-blue-600';
            rider_{{ $rider->id }}.innerHTML = '{{ strtoupper(substr($rider->name, 0, 2)) }}';
            
            new maplibregl.Marker(rider_{{ $rider->id }})
                .setLngLat([{{ $rider->longitude }}, {{ $rider->latitude }}])
                .setPopup(new maplibregl.Popup({ offset: 25, maxWidth: '250px' }).setHTML(`
                    <div class="p-4 min-w-[220px]">
                        <div class="flex items-center justify-between mb-3 pb-2 border-b border-gray-100">
                            <h4 class="font-bold text-base text-gray-900 m-0">Rider</h4>
                            <span class="bg-blue-100 text-blue-700 px-2 py-1 rounded text-xs font-semibold">{{ strtoupper(substr($rider->name, 0, 2)) }}</span>
                        </div>
                        <div class="grid grid-cols-1 gap-2 text-sm">
                            <div class="flex gap-2">
                                <span class="text-gray-500 font-medium min-w-[50px]">Name:</span>
                                <span class="text-gray-900 font-medium">{{ $rider->name }}</span>
                            </div>
                            <div class="flex gap-2">
                                <span class="text-gray-500 font-medium min-w-[50px]">Phone:</span>
                                <a href="tel:{{ $rider->phone }}" class="text-blue-600 hover:text-blue-800">{{ $rider->phone }}</a>
                            </div>
                        </div>
                    </div>
                `))
                .addTo(map);
        @endif
    @endforeach
</script>
@endsection
