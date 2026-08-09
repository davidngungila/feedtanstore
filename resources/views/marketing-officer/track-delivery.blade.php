@extends('layouts.app')

@section('page-title', 'Track Delivery')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <a href="{{ route('marketing-officer.order-details', $order->id) }}" class="text-primary-600 hover:text-primary-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Order Details
        </a>
    </div>

    <div class="card rounded-2xl p-6 mb-6">
        <h1 class="text-2xl font-bold text-primary-900 mb-6">Track Delivery - {{ $order->order_number }}</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <div>
                <p class="text-sm text-gray-600">Customer</p>
                <p class="font-semibold">{{ $order->customer_name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Delivery Address</p>
                <p class="font-semibold">{{ $order->delivery_address }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Assigned Rider</p>
                <p class="font-semibold">{{ $order->rider->name }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Rider Phone</p>
                <p class="font-semibold">{{ $order->rider->phone }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Vehicle Type</p>
                <p class="font-semibold">{{ $order->rider->vehicle_type }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Vehicle Plate</p>
                <p class="font-semibold">{{ $order->rider->vehicle_plate }}</p>
            </div>
        </div>
    </div>

    <div class="card rounded-2xl p-6 mb-6">
        <h2 class="text-lg font-bold text-primary-900 mb-4">Delivery Status</h2>
        
        <div class="space-y-4">
            @if($order->status === 'pending')
            <div class="flex items-center gap-4 p-4 bg-yellow-50 rounded-lg">
                <div class="w-10 h-10 bg-yellow-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-clock text-yellow-600"></i>
                </div>
                <div>
                    <p class="font-semibold text-yellow-800">Pending</p>
                    <p class="text-sm text-yellow-600">Order is waiting to be processed</p>
                </div>
            </div>
            @endif

            @if($order->status === 'confirmed')
            <div class="flex items-center gap-4 p-4 bg-blue-50 rounded-lg">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check text-blue-600"></i>
                </div>
                <div>
                    <p class="font-semibold text-blue-800">Confirmed</p>
                    <p class="text-sm text-blue-600">Order confirmed and rider assigned</p>
                </div>
            </div>
            @endif

            @if($order->status === 'out_for_delivery')
            <div class="flex items-center gap-4 p-4 bg-purple-50 rounded-lg">
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-motorcycle text-purple-600"></i>
                </div>
                <div>
                    <p class="font-semibold text-purple-800">Out for Delivery</p>
                    <p class="text-sm text-purple-600">Rider is on the way to customer</p>
                </div>
            </div>
            @endif

            @if($order->status === 'delivered')
            <div class="flex items-center gap-4 p-4 bg-green-50 rounded-lg">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div>
                    <p class="font-semibold text-green-800">Delivered</p>
                    <p class="text-sm text-green-600">Order successfully delivered</p>
                </div>
            </div>
            @endif

            @if($order->status === 'cancelled')
            <div class="flex items-center gap-4 p-4 bg-red-50 rounded-lg">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-times text-red-600"></i>
                </div>
                <div>
                    <p class="font-semibold text-red-800">Cancelled</p>
                    <p class="text-sm text-red-600">Order was cancelled</p>
                </div>
            </div>
            @endif
        </div>
    </div>

    <div class="card rounded-2xl p-6 mb-6">
        <h2 class="text-lg font-bold text-primary-900 mb-4">Delivery Location & Route</h2>
        
        @if($order->delivery_latitude && $order->delivery_longitude)
        <div id="delivery-map" class="w-full h-[400px] rounded-lg overflow-hidden mb-4"></div>
        
        <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
        <script>
            const storeLat = {{ $storeLat ?? -3.3869 }};
            const storeLng = {{ $storeLng ?? 36.6883 }};
            const orderLat = {{ $order->delivery_latitude }};
            const orderLng = {{ $order->delivery_longitude }};
            const route = @json($route);
            
            const deliveryMap = L.map('delivery-map').setView([(storeLat + orderLat) / 2, (storeLng + orderLng) / 2], 12);
            
            // OpenStreetMap base layer
            const osmLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            });
            
            // World Imagery base layer (Esri)
            const worldImageryLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                attribution: 'Tiles &copy; Esri &mdash; Source: Esri, DigitalGlobe, GeoEye, i-cubed, USDA, USGS, AEX, Getmapping, Aerogrid, IGN, IGP, swisstopo, and the GIS User Community'
            });
            
            // Add OSM as default
            osmLayer.addTo(deliveryMap);
            
            // Layer control
            const baseLayers = {
                'OpenStreetMap': osmLayer,
                'World Imagery': worldImageryLayer
            };
            
            L.control.layers(baseLayers).addTo(deliveryMap);
            
            // Add store marker
            L.marker([storeLat, storeLng])
                .addTo(deliveryMap)
                .bindPopup('<strong>Store</strong>').openPopup();
            
            // Add order marker
            L.circleMarker([orderLat, orderLng], {
                radius: 8,
                fillColor: '#f97316',
                color: '#fff',
                weight: 2,
                fillOpacity: 0.8
            })
                .addTo(deliveryMap)
                .bindPopup(`
                    <div class="p-2">
                        <h4 class="font-bold text-sm">Delivery Location</h4>
                        <p class="text-xs text-gray-600">{{ $order->customer_name }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $order->delivery_address }}</p>
                    </div>
                `);
            
            // Add route if available
            if (route && route.features && route.features.length > 0) {
                const coords = route.features[0].geometry.coordinates;
                const points = coords.map(c => [c[1], c[0]]);
                L.polyline(points, { color: '#3b82f6', weight: 4, opacity: 0.7 }).addTo(deliveryMap);
                deliveryMap.fitBounds(points, { padding: [50, 50] });
            } else {
                // Fit bounds to show both markers
                const bounds = L.latLngBounds([
                    [storeLat, storeLng],
                    [orderLat, orderLng]
                ]);
                deliveryMap.fitBounds(bounds, { padding: [50, 50] });
            }
        </script>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-600">Delivery Address</p>
                <p class="font-semibold">{{ $order->delivery_address }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Coordinates</p>
                <p class="font-semibold">{{ $order->delivery_latitude }}, {{ $order->delivery_longitude }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Distance from Store</p>
                <p class="font-semibold" id="distance_from_store">Calculating...</p>
            </div>
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-map-marker-alt text-4xl mb-4 text-gray-300"></i>
            <p class="font-medium">No delivery location available</p>
            <p class="text-sm">Location coordinates not provided for this order</p>
        </div>
        @endif
    </div>

    <div class="card rounded-2xl p-6">
        <h2 class="text-lg font-bold text-primary-900 mb-4">Status History</h2>
        
        @if($order->statusHistory->count() > 0)
        <div class="space-y-3">
            @foreach($order->statusHistory as $history)
            <div class="flex items-start gap-4 p-3 border border-gray-200 rounded-lg">
                <div class="w-8 h-8 bg-primary-100 rounded-full flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-history text-primary-600 text-sm"></i>
                </div>
                <div class="flex-1">
                    <div class="flex items-center justify-between">
                        <p class="font-semibold text-gray-900">{{ ucfirst($history->status) }}</p>
                        <p class="text-xs text-gray-500">{{ $history->created_at->format('M d, Y H:i') }}</p>
                    </div>
                    @if($history->notes)
                    <p class="text-sm text-gray-600 mt-1">{{ $history->notes }}</p>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-history text-4xl mb-4 text-gray-300"></i>
            <p class="font-medium">No status history available</p>
            <p class="text-sm">Status changes will appear here</p>
        </div>
        @endif
    </div>
</div>

<script>
// Store location from settings
const STORE_LATITUDE = {{ $storeSettings->store_latitude ?? -6.7924 }}; // Default: Dar es Salaam coordinates
const STORE_LONGITUDE = {{ $storeSettings->store_longitude ?? 39.2083 }};

// Calculate distance between two coordinates using Haversine formula
function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Earth's radius in km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = 
        Math.sin(dLat/2) * Math.sin(dLat/2) +
        Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
        Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    const distance = R * c; // Distance in km
    return distance.toFixed(2); // Return distance rounded to 2 decimal places
}

// Calculate distance when page loads
document.addEventListener('DOMContentLoaded', function() {
    @if($order->delivery_latitude && $order->delivery_longitude)
    const distanceElement = document.getElementById('distance_from_store');
    if (distanceElement) {
        const distance = calculateDistance(
            STORE_LATITUDE, 
            STORE_LONGITUDE, 
            {{ $order->delivery_latitude }}, 
            {{ $order->delivery_longitude }}
        );
        distanceElement.textContent = distance + ' km';
    }
    @endif
});
</script>
@endsection
