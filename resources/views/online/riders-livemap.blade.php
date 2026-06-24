@extends('layouts.app')

@section('page-title', 'Riders Live Map')

@section('content')
<div class="animate-[fadeIn_0.4s_ease] space-y-6">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <h1 class="text-xl font-bold" :class="darkMode?'text-white':'text-primary-900'">Riders Live Map</h1>
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
    
    // Add store marker
    L.marker([storeLat, storeLng])
        .addTo(map)
        .bindPopup("<b>Store</b>")
        .openPopup();
    
    // Object to track markers
    const riderMarkers = {};
    
    // Initial rider markers
    @foreach($riders as $rider)
        @if($rider->latitude && $rider->longitude)
            riderMarkers[{{ $rider->id }}] = L.marker([{{ $rider->latitude }}, {{ $rider->longitude }}])
                .addTo(map)
                .bindPopup(`
                    <div class="p-2">
                        <h4 class="font-bold text-base text-gray-900 mb-1">Rider: {{ $rider->name }}</h4>
                        <p class="text-sm text-gray-700"><strong>Phone:</strong> <a href="tel:{{ $rider->phone }}">{{ $rider->phone }}</a></p>
                        <p class="text-sm text-gray-700"><strong>Vehicle:</strong> {{ $rider->vehicle_type }}</p>
                    </div>
                `);
        @elseif($rider->latest_location && $rider->latest_location->latitude && $rider->latest_location->longitude)
            riderMarkers[{{ $rider->id }}] = L.marker([{{ $rider->latest_location->latitude }}, {{ $rider->latest_location->longitude }}])
                .addTo(map)
                .bindPopup(`
                    <div class="p-2">
                        <h4 class="font-bold text-base text-gray-900 mb-1">Rider: {{ $rider->name }}</h4>
                        <p class="text-sm text-gray-700"><strong>Phone:</strong> <a href="tel:{{ $rider->phone }}">{{ $rider->phone }}</a></p>
                        <p class="text-sm text-gray-700"><strong>Vehicle:</strong> {{ $rider->vehicle_type }}</p>
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
                                    <p class="text-sm text-gray-700"><strong>Vehicle:</strong> ${rider.vehicle_type}</p>
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