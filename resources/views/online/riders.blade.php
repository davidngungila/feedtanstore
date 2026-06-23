@extends('layouts.app')

@section('page-title', 'Delivery Riders')

@section('content')
<div class="animate-[fadeIn_0.4s_ease] space-y-6">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Delivery Riders</h2>
            <div class="flex items-center gap-2">
                <a href="{{ route('online.delivery.map') }}" class="px-4 py-2 bg-secondary-600 hover:bg-secondary-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-map mr-2"></i>Delivery Map
                </a>
                <a href="{{ route('online.riders.create') }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-plus mr-2"></i>Add Rider
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="card rounded-2xl overflow-hidden mb-6">
            <div id="map" class="w-full h-[400px]"></div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700">Name</th>
                        <th class="px-4 py-3 text-left text-gray-700">Email</th>
                        <th class="px-4 py-3 text-left text-gray-700">Phone</th>
                        <th class="px-4 py-3 text-left text-gray-700">Vehicle Type</th>
                        <th class="px-4 py-3 text-left text-gray-700">Plate No.</th>
                        <th class="px-4 py-3 text-left text-gray-700">Status</th>
                        <th class="px-4 py-3 text-left text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($riders as $rider)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $rider->name }}</td>
                        <td class="px-4 py-3">{{ $rider->user?->email ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $rider->phone }}</td>
                        <td class="px-4 py-3">{{ $rider->vehicle_type ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $rider->vehicle_plate ?? 'N/A' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                @if($rider->is_active) bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $rider->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-2">
                                <a href="{{ route('online.riders.edit', $rider) }}" class="text-primary-600 hover:text-primary-800 transition-colors">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('online.riders.toggle', $rider) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="text-{{ $rider->is_active ? 'red' : 'green' }}-600 hover:text-{{ $rider->is_active ? 'red' : 'green' }}-800 transition-colors">
                                        <i class="fas fa-{{ $rider->is_active ? 'ban' : 'check' }}"></i>
                                    </button>
                                </form>
                                <form action="{{ route('online.riders.destroy', $rider) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this rider?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-500">
                            No delivery riders found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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
        .bindPopup("<b>Store</b>");
    
    // Object to track markers
    const riderMarkers = {};
    
    // Initial rider markers
    @foreach($riders as $rider)
        @if($rider->latestLocation && $rider->latestLocation->latitude && $rider->latestLocation->longitude)
            riderMarkers[{{ $rider->id }}] = L.marker([{{ $rider->latestLocation->latitude }}, {{ $rider->latestLocation->longitude }}])
                .addTo(map)
                .bindPopup(`
                    <div class="p-2 min-w-[200px]">
                        <h4 class="font-bold text-base text-gray-900 mb-1">Rider: {{ $rider->name }}</h4>
                        <p class="text-sm text-gray-700 mb-1"><strong>Phone:</strong> <a href="tel:{{ $rider->phone }}">{{ $rider->phone }}</a></p>
                        <p class="text-sm text-gray-700 mb-1"><strong>Vehicle:</strong> {{ $rider->vehicle_type ?? 'N/A' }}</p>
                        <p class="text-sm text-gray-700 mb-2"><strong>Plate:</strong> {{ $rider->vehicle_plate ?? 'N/A' }}</p>
                        <a href="{{ route('online.riders.edit', $rider) }}" class="text-primary-600 hover:underline text-sm">Edit Rider</a>
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
                                <div class="p-2 min-w-[200px]">
                                    <h4 class="font-bold text-base text-gray-900 mb-1">Rider: ${rider.name}</h4>
                                    <p class="text-sm text-gray-700 mb-1"><strong>Phone:</strong> <a href="tel:${rider.phone}">${rider.phone}</a></p>
                                    <p class="text-sm text-gray-700 mb-1"><strong>Vehicle:</strong> ${rider.vehicle_type ?? 'N/A'}</p>
                                    <p class="text-sm text-gray-700 mb-2"><strong>Plate:</strong> ${rider.vehicle_plate ?? 'N/A'}</p>
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