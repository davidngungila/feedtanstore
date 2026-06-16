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

    // Add markers for active orders
    @foreach($activeOrders as $order)
        const el_{{ $order->id }} = document.createElement('div');
        el_{{ $order->id }}.className = 'bg-orange-500 text-white p-2 rounded-full text-xs font-bold shadow-lg';
        el_{{ $order->id }}.innerHTML = '{{ $order->id }}';
        
        new maplibregl.Marker(el_{{ $order->id }})
            .setLngLat([{{ $order->delivery_longitude }}, {{ $order->delivery_latitude }}])
            .setPopup(new maplibregl.Popup({ offset: 25 }).setHTML(`
                <div class="p-2">
                    <h4 class="font-bold text-sm">Order #{{ $order->id }}</h4>
                    <p class="text-xs text-gray-600">Customer: {{ $order->customer_name }}</p>
                    <p class="text-xs text-gray-600">Phone: {{ $order->customer_phone }}</p>
                    <p class="text-xs text-gray-600 mt-1">Total: TZS {{ number_format($order->total, 2) }}</p>
                    <p class="text-xs font-medium mt-1">{{ ucfirst(str_replace('_', ' ', $order->status)) }}</p>
                </div>
            `))
            .addTo(map);
    @endforeach

    // Optionally add markers for riders
    @foreach($riders as $rider)
        @if($rider->latitude && $rider->longitude)
            const rider_{{ $rider->id }} = document.createElement('div');
            rider_{{ $rider->id }}.className = 'bg-blue-500 text-white p-2 rounded-full text-xs font-bold shadow-lg';
            rider_{{ $rider->id }}.innerHTML = '{{ strtoupper(substr($rider->name, 0, 2)) }}';
            
            new maplibregl.Marker(rider_{{ $rider->id }})
                .setLngLat([{{ $rider->longitude }}, {{ $rider->latitude }}])
                .setPopup(new maplibregl.Popup({ offset: 25 }).setHTML(`
                    <div class="p-2">
                        <h4 class="font-bold text-sm">Rider: {{ $rider->name }}</h4>
                        <p class="text-xs text-gray-600">Phone: {{ $rider->phone }}</p>
                    </div>
                `))
                .addTo(map);
        @endif
    @endforeach
</script>
@endsection
