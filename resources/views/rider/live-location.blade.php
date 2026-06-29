@extends('layouts.app')

@section('page-title', 'Live Location')

@section('content')
<div class="animate-[fadeIn_0.4s_ease] space-y-6">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="{{ route('rider.dashboard') }}" class="text-primary-600 hover:text-primary-800 mb-2 inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
                <h2 class="text-xl font-bold text-primary-900">Live Location</h2>
            </div>
        </div>

        <div class="mb-6">
            <div class="card rounded-2xl overflow-hidden">
                <div id="live-map" class="w-full h-[400px]"></div>
            </div>
        </div>

        <div class="flex items-center justify-between mb-4">
            <div id="locationStatus" class="text-sm text-gray-600">
                <i class="fas fa-circle text-green-500 mr-1"></i>
                Location tracking enabled
            </div>
            <div id="coordinates" class="text-sm text-gray-600">
                @if($currentLat && $currentLng)
                    <span>Lat: {{ number_format($currentLat, 6) }}, Lng: {{ number_format($currentLng, 6) }}</span>
                @else
                    <span>Acquiring location...</span>
                @endif
            </div>
        </div>

        <div class="p-4 border rounded-lg bg-blue-50">
            <p class="text-sm text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                Your location is being automatically tracked and shared with the store for delivery tracking.
            </p>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    const currentLat = {{ $currentLat ?? 'null' }};
    const currentLng = {{ $currentLng ?? 'null' }};
    let map;
    let riderMarker;
    let watchId = null;

    // Initialize map
    function initMap() {
        if (currentLat && currentLng) {
            map = L.map('live-map').setView([currentLat, currentLng], 15);
        } else {
            // Default to Arusha, Tanzania if no location
            map = L.map('live-map').setView([-3.3869, 36.6883], 12);
        }

        // OpenStreetMap base layer
        const osmLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        });

        // World Imagery base layer (Esri)
        const worldImageryLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, DigitalGlobe, GeoEye, i-cubed, USDA, USGS, AEX, Getmapping, Aerogrid, IGN, iGP, swisstopo, and the GIS User Community'
        });

        // Add OSM as default
        osmLayer.addTo(map);

        // Layer control
        const baseLayers = {
            'OpenStreetMap': osmLayer,
            'World Imagery': worldImageryLayer
        };

        L.control.layers(baseLayers).addTo(map);

        // Add rider marker if location exists
        if (currentLat && currentLng) {
            riderMarker = L.marker([currentLat, currentLng], {
                icon: L.divIcon({
                    className: 'rider-marker',
                    html: '<div style="background-color: #3b82f6; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>',
                    iconSize: [20, 20],
                    iconAnchor: [10, 10]
                })
            }).addTo(map).bindPopup('<strong>Your Location</strong>');
        }
    }

    // Start location tracking
    function startLocationTracking() {
        if (!navigator.geolocation) {
            alert('Geolocation is not supported by your browser');
            return;
        }

        watchId = navigator.geolocation.watchPosition(
            function(position) {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                // Update coordinates display
                document.getElementById('coordinates').innerHTML = 
                    `<span>Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}</span>`;

                // Update or create marker
                if (riderMarker) {
                    riderMarker.setLatLng([lat, lng]);
                } else {
                    riderMarker = L.marker([lat, lng], {
                        icon: L.divIcon({
                            className: 'rider-marker',
                            html: '<div style="background-color: #3b82f6; width: 20px; height: 20px; border-radius: 50%; border: 3px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);"></div>',
                            iconSize: [20, 20],
                            iconAnchor: [10, 10]
                        })
                    }).addTo(map).bindPopup('<strong>Your Location</strong>');
                }

                // Center map on rider
                map.setView([lat, lng], 15);

                // Send location to server
                sendLocationToServer(lat, lng);
            },
            function(error) {
                console.error('Geolocation error:', error);
                let errorMessage = 'Unable to retrieve your location';
                if (error.code === 1) {
                    errorMessage = 'Location permission denied. Please enable location access.';
                } else if (error.code === 2) {
                    errorMessage = 'Location unavailable. Please check your device settings.';
                } else if (error.code === 3) {
                    errorMessage = 'Location request timed out.';
                }
                alert(errorMessage);
                
                // Update status on error
                document.getElementById('locationStatus').innerHTML = '<i class="fas fa-circle text-red-500 mr-1"></i> Location tracking failed';
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    }

    // Send location to server
    async function sendLocationToServer(lat, lng) {
        try {
            const response = await fetch('{{ route("rider.location.update") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    latitude: lat,
                    longitude: lng
                })
            });

            if (!response.ok) {
                console.error('Failed to send location to server');
            }
        } catch (error) {
            console.error('Error sending location:', error);
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        initMap();
        startLocationTracking();
    });
</script>
@endsection
