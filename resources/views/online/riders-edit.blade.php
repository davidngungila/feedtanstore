@extends('layouts.app')

@section('page-title', 'Edit Delivery Rider')

@section('content')
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<div class="animate-[fadeIn_0.4s_ease] space-y-6">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Edit Delivery Rider</h2>
            <a href="{{ route('online.riders') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Riders
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

        <form action="{{ route('online.riders.update', $rider) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" name="name" value="{{ old('name', $rider->name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email *</label>
                    <input type="email" name="email" value="{{ old('email', $rider->user?->email) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">New Password (leave blank to keep current)</label>
                    <input type="password" name="password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone *</label>
                    <input type="text" name="phone" value="{{ old('phone', $rider->phone) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vehicle Type</label>
                    <input type="text" name="vehicle_type" value="{{ old('vehicle_type', $rider->vehicle_type) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Vehicle Plate Number</label>
                    <input type="text" name="vehicle_plate" value="{{ old('vehicle_plate', $rider->vehicle_plate) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $rider->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm font-medium text-gray-700">Active</span>
                    </label>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('online.riders') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Update Rider
                </button>
            </div>
        </form>
    </div>

    <div class="card rounded-2xl overflow-hidden">
        <div id="map" class="w-full h-[400px]"></div>
    </div>

    @if($rider->locations->count() > 0)
    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-semibold text-primary-900 mb-4">Location History (Last 10)</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700">Latitude</th>
                        <th class="px-4 py-3 text-left text-gray-700">Longitude</th>
                        <th class="px-4 py-3 text-left text-gray-700">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($rider->locations as $location)
                    <tr>
                        <td class="px-4 py-3">{{ $location->latitude }}</td>
                        <td class="px-4 py-3">{{ $location->longitude }}</td>
                        <td class="px-4 py-3">{{ $location->created_at->format('M d, Y h:i A') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    @if($rider->user && $rider->user->devices->count() > 0)
    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-semibold text-primary-900 mb-4">Associated Devices</h3>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700">Device Name</th>
                        <th class="px-4 py-3 text-left text-gray-700">Type</th>
                        <th class="px-4 py-3 text-left text-gray-700">Browser</th>
                        <th class="px-4 py-3 text-left text-gray-700">IP Address</th>
                        <th class="px-4 py-3 text-left text-gray-700">Last Active</th>
                        <th class="px-4 py-3 text-left text-gray-700">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($rider->user->devices as $device)
                    <tr>
                        <td class="px-4 py-3">{{ $device->device_name ?? 'Unknown' }}</td>
                        <td class="px-4 py-3">{{ $device->device_type ?? 'Unknown' }}</td>
                        <td class="px-4 py-3">{{ $device->browser ?? 'Unknown' }}</td>
                        <td class="px-4 py-3">{{ $device->ip_address ?? 'Unknown' }}</td>
                        <td class="px-4 py-3">{{ $device->last_active_at ? $device->last_active_at->format('M d, Y h:i A') : 'Never' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                @if($device->is_active) bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ $device->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const storeLat = {{ $storeSettings->store_latitude ?? -1.286389 }};
        const storeLng = {{ $storeSettings->store_longitude ?? 36.817223 }};
        const locations = @json($rider->locations);
        
        let map;
        
        if (locations.length > 0) {
            const latestLoc = locations[0];
            map = L.map('map').setView([latestLoc.latitude, latestLoc.longitude], 13);
        } else {
            map = L.map('map').setView([storeLat, storeLng], 13);
        }
        
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        L.marker([storeLat, storeLng])
            .addTo(map)
            .bindPopup('Store');
        
        // Add rider's latest location
        if (locations.length > 0) {
            const latestLoc = locations[0];
            L.marker([latestLoc.latitude, latestLoc.longitude])
                .addTo(map)
                .bindPopup('Current Location');
                
            // Draw route from previous locations
            const latlngs = locations.map(loc => [loc.latitude, loc.longitude]);
            L.polyline(latlngs, {color: 'red', weight: 3}).addTo(map);
        }
    });
</script>
@endsection