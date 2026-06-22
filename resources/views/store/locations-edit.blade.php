@extends('layouts.app')

@section('page-title', 'Edit Location')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Edit Location</h2>
            <a href="{{ route('store.locations') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Locations
            </a>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-800 rounded-lg">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('store.locations.update', $location) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" name="name" value="{{ old('name', $location->name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type *</label>
                    <select name="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Select Type</option>
                        <option value="warehouse" {{ old('type', $location->type) == 'warehouse' ? 'selected' : '' }}>Warehouse</option>
                        <option value="store" {{ old('type', $location->type) == 'store' ? 'selected' : '' }}>Store</option>
                        <option value="other" {{ old('type', $location->type) == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Address</label>
                    <textarea name="address" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('address', $location->address) }}</textarea>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="2" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('description', $location->description) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Latitude</label>
                    <input type="number" step="0.0000001" name="latitude" id="latitudeInput" value="{{ old('latitude', $location->latitude) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Longitude</label>
                    <input type="number" step="0.0000001" name="longitude" id="longitudeInput" value="{{ old('longitude', $location->longitude) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $location->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm font-medium text-gray-700">Active</span>
                    </label>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Select Location on Map</label>
                    <div class="flex gap-2 mb-3">
                        <button type="button" onclick="getCurrentLocation()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-location-crosshairs mr-2"></i>Capture Current Location
                        </button>
                        <button type="button" onclick="clearLocation()" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-times mr-2"></i>Clear Location
                        </button>
                    </div>
                    <div class="w-full h-[300px] border border-gray-300 rounded-lg" id="map"></div>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('store.locations') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Update Location
                </button>
            </div>
        </form>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    // Default to current location if exists, else Arusha, Tanzania
    let lat = {{ old('latitude', $location->latitude ?? -3.3869) }};
    let lng = {{ old('longitude', $location->longitude ?? 36.6883) }};

    const map = L.map('map').setView([lat, lng], 13);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    let marker = L.marker([lat, lng]).addTo(map);

    map.on('click', function(e) {
        const newLat = e.latlng.lat;
        const newLng = e.latlng.lng;

        // Update the inputs
        document.getElementById('latitudeInput').value = newLat.toFixed(7);
        document.getElementById('longitudeInput').value = newLng.toFixed(7);

        // Move the marker
        marker.setLatLng([newLat, newLng]);
    });

    // Also update marker when user types in inputs
    document.getElementById('latitudeInput').addEventListener('input', updateMarker);
    document.getElementById('longitudeInput').addEventListener('input', updateMarker);

    function updateMarker() {
        const newLat = parseFloat(document.getElementById('latitudeInput').value) || {{ $location->latitude ?? -3.3869 }};
        const newLng = parseFloat(document.getElementById('longitudeInput').value) || {{ $location->longitude ?? 36.6883 }};
        marker.setLatLng([newLat, newLng]);
        map.setView([newLat, newLng], map.getZoom());
    }

    function getCurrentLocation() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                const newLat = position.coords.latitude;
                const newLng = position.coords.longitude;

                // Update the inputs
                document.getElementById('latitudeInput').value = newLat.toFixed(7);
                document.getElementById('longitudeInput').value = newLng.toFixed(7);

                // Move the marker
                marker.setLatLng([newLat, newLng]);
                map.setView([newLat, newLng], 15);
            }, function(error) {
                alert('Error getting location: ' + error.message);
            });
        } else {
            alert('Geolocation is not supported by your browser.');
        }
    }

    function clearLocation() {
        // Clear inputs
        document.getElementById('latitudeInput').value = '';
        document.getElementById('longitudeInput').value = '';

        // Reset marker to default
        const defaultLat = -3.3869;
        const defaultLng = 36.6883;
        marker.setLatLng([defaultLat, defaultLng]);
        map.setView([defaultLat, defaultLng], 13);
    }
</script>
@endsection