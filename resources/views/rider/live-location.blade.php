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
            <div class="flex items-center gap-3">
                <button id="toggleLocationBtn" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors flex items-center gap-2">
                    <i class="fas fa-location-crosshairs"></i>
                    <span id="locationBtnText">Enable Location</span>
                </button>
                <div id="locationStatus" class="text-sm text-gray-600">
                    <i class="fas fa-circle text-gray-400 mr-1"></i>
                    Location disabled
                </div>
            </div>
            <div id="coordinates" class="text-sm text-gray-600">
                @if($currentLat && $currentLng)
                    <span>Lat: {{ number_format($currentLat, 6) }}, Lng: {{ number_format($currentLng, 6) }}</span>
                @else
                    <span>No location data</span>
                @endif
            </div>
        </div>

        <div class="p-4 border rounded-lg bg-blue-50">
            <p class="text-sm text-blue-800">
                <i class="fas fa-info-circle mr-2"></i>
                Enable location to automatically track and update your position. Your location will be shared with the store for delivery tracking.
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
    let isLocationEnabled = false;

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

    // Toggle location tracking
    function toggleLocation() {
        if (isLocationEnabled) {
            // Disable location
            if (watchId !== null) {
                navigator.geolocation.clearWatch(watchId);
                watchId = null;
            }
            isLocationEnabled = false;
            document.getElementById('locationBtnText').textContent = 'Enable Location';
            document.getElementById('toggleLocationBtn').classList.remove('bg-red-600', 'hover:bg-red-700');
            document.getElementById('toggleLocationBtn').classList.add('bg-primary-600', 'hover:bg-primary-700');
            document.getElementById('locationStatus').innerHTML = '<i class="fas fa-circle text-gray-400 mr-1"></i> Location disabled';
        } else {
            // Enable location
            if (!navigator.geolocation) {
                alert('Geolocation is not supported by your browser');
                return;
            }

            document.getElementById('locationBtnText').textContent = 'Disable Location';
            document.getElementById('toggleLocationBtn').classList.remove('bg-primary-600', 'hover:bg-primary-700');
            document.getElementById('toggleLocationBtn').classList.add('bg-red-600', 'hover:bg-red-700');
            document.getElementById('locationStatus').innerHTML = '<i class="fas fa-circle text-green-500 mr-1"></i> Location enabled';

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
                    
                    // Reset button state on error
                    isLocationEnabled = false;
                    document.getElementById('locationBtnText').textContent = 'Enable Location';
                    document.getElementById('toggleLocationBtn').classList.remove('bg-red-600', 'hover:bg-red-700');
                    document.getElementById('toggleLocationBtn').classList.add('bg-primary-600', 'hover:bg-primary-700');
                    document.getElementById('locationStatus').innerHTML = '<i class="fas fa-circle text-gray-400 mr-1"></i> Location disabled';
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );

            isLocationEnabled = true;
        }
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
        document.getElementById('toggleLocationBtn').addEventListener('click', toggleLocation);
    });
</script>
@endsection
