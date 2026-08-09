@extends('layouts.app')

@section('page-title', 'Rider Details')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <a href="{{ route('marketing-officer.riders') }}" class="text-primary-600 hover:text-primary-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Riders
        </a>
    </div>

    <!-- Rider Profile Card -->
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-start gap-6">
            <div class="flex-shrink-0">
                <div class="w-24 h-24 bg-primary-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-primary-600 text-4xl"></i>
                </div>
            </div>
            <div class="flex-1">
                <div class="flex items-center justify-between mb-2">
                    <h1 class="text-2xl font-bold text-primary-900">{{ $rider->name }}</h1>
                    <span class="px-3 py-1 text-sm font-semibold rounded-full 
                        {{ $rider->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $rider->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
                <p class="text-gray-600 mb-4">{{ $rider->user?->email ?? 'N/A' }}</p>
                <div class="flex items-center gap-4 text-sm text-gray-600">
                    <span><i class="fas fa-phone mr-1"></i>{{ $rider->phone ?? 'N/A' }}</span>
                    <span><i class="fas fa-star mr-1 text-yellow-500"></i>{{ $rider->rating ?? 'N/A' }} ({{ $rider->total_reviews ?? 0 }} reviews)</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Vehicle Information -->
        <div class="card rounded-2xl p-6">
            <h2 class="text-lg font-bold text-primary-900 mb-4">Vehicle Information</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Vehicle Type</span>
                    <span class="font-semibold">{{ $rider->vehicle_type ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Vehicle Model</span>
                    <span class="font-semibold">{{ $rider->vehicle_model ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Vehicle Color</span>
                    <span class="font-semibold">{{ $rider->vehicle_color ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Vehicle Year</span>
                    <span class="font-semibold">{{ $rider->vehicle_year ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">License Plate</span>
                    <span class="font-semibold">{{ $rider->vehicle_plate ?? 'N/A' }}</span>
                </div>
            </div>
        </div>

        <!-- License Information -->
        <div class="card rounded-2xl p-6">
            <h2 class="text-lg font-bold text-primary-900 mb-4">License Information</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">License Number</span>
                    <span class="font-semibold">{{ $rider->driving_license_number ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">License Expiry</span>
                    <span class="font-semibold">{{ $rider->license_expiry_date ? \Carbon\Carbon::parse($rider->license_expiry_date)->format('M d, Y') : 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">NID Number</span>
                    <span class="font-semibold">{{ $rider->nid_number ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Date of Birth</span>
                    <span class="font-semibold">{{ $rider->date_of_birth ? \Carbon\Carbon::parse($rider->date_of_birth)->format('M d, Y') : 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Gender</span>
                    <span class="font-semibold">{{ $rider->gender ?? 'N/A' }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        <!-- Performance Statistics -->
        <div class="card rounded-2xl p-6">
            <h2 class="text-lg font-bold text-primary-900 mb-4">Performance Statistics</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Deliveries</span>
                    <span class="font-semibold text-primary-600">{{ $rider->total_deliveries ?? 0 }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Earnings</span>
                    <span class="font-semibold text-green-600">TZS {{ number_format($rider->total_earnings ?? 0, 0) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Rating</span>
                    <span class="font-semibold text-yellow-600">{{ $rider->rating ?? 'N/A' }}/5</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Reviews</span>
                    <span class="font-semibold">{{ $rider->total_reviews ?? 0 }}</span>
                </div>
            </div>
        </div>

        <!-- Banking Information -->
        <div class="card rounded-2xl p-6">
            <h2 class="text-lg font-bold text-primary-900 mb-4">Banking Information</h2>
            <div class="space-y-3">
                <div class="flex justify-between">
                    <span class="text-gray-600">Bank Name</span>
                    <span class="font-semibold">{{ $rider->bank_name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Account Number</span>
                    <span class="font-semibold">{{ $rider->bank_account_number ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Account Name</span>
                    <span class="font-semibold">{{ $rider->bank_account_name ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Bank Branch</span>
                    <span class="font-semibold">{{ $rider->bank_branch ?? 'N/A' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Mobile Money</span>
                    <span class="font-semibold">{{ $rider->mobile_money_number ?? 'N/A' }} ({{ $rider->mobile_money_provider ?? 'N/A' }})</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Current Location -->
    <div class="card rounded-2xl p-6 mb-6">
        <h2 class="text-lg font-bold text-primary-900 mb-4">Current Location</h2>
        
        @if($rider->latestLocation)
        <div class="bg-gray-50 rounded-lg overflow-hidden mb-4">
            <iframe 
                width="100%" 
                height="300" 
                frameborder="0" 
                scrolling="no" 
                marginheight="0" 
                marginwidth="0" 
                src="https://maps.google.com/maps?q={{ $rider->latestLocation->latitude }},{{ $rider->latestLocation->longitude }}&hl=en&z=14&output=embed">
            </iframe>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <p class="text-sm text-gray-600">Latitude</p>
                <p class="font-semibold">{{ $rider->latestLocation->latitude }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Longitude</p>
                <p class="font-semibold">{{ $rider->latestLocation->longitude }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Distance from Store</p>
                <p class="font-semibold" id="distance_from_store">Calculating...</p>
            </div>
            <div>
                <p class="text-sm text-gray-600">Last Updated</p>
                <p class="font-semibold">{{ $rider->latestLocation->created_at->diffForHumans() }}</p>
            </div>
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-map-marker-alt text-4xl mb-4 text-gray-300"></i>
            <p class="font-medium">No location data available</p>
            <p class="text-sm">Rider's current location not available</p>
        </div>
        @endif
    </div>

    <!-- Recent Orders -->
    <div class="card rounded-2xl p-6">
        <h2 class="text-lg font-bold text-primary-900 mb-4">Recent Deliveries</h2>
        
        @if($rider->onlineOrders->count() > 0)
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order Number</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($rider->onlineOrders->take(10) as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 font-semibold text-gray-900">{{ $order->order_number }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $order->customer_name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">TZS {{ number_format($order->total, 0) }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($order->status == 'delivered') bg-green-100 text-green-700
                                @elseif($order->status == 'out_for_delivery') bg-blue-100 text-blue-700
                                @elseif($order->status == 'cancelled') bg-red-100 text-red-700
                                @else bg-yellow-100 text-yellow-700
                                @endif">
                                {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $order->created_at->format('M d, Y H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-8 text-gray-500">
            <i class="fas fa-box text-4xl mb-4 text-gray-300"></i>
            <p class="font-medium">No deliveries yet</p>
            <p class="text-sm">This rider hasn't completed any deliveries</p>
        </div>
        @endif
    </div>
</div>

<script>
// Store location from settings
const STORE_LATITUDE = {{ $storeSettings->store_latitude ?? -6.7924 }};
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
    @if($rider->latestLocation)
    const distanceElement = document.getElementById('distance_from_store');
    if (distanceElement) {
        const distance = calculateDistance(
            STORE_LATITUDE, 
            STORE_LONGITUDE, 
            {{ $rider->latestLocation->latitude }}, 
            {{ $rider->latestLocation->longitude }}
        );
        distanceElement.textContent = distance + ' km';
    }
    @endif
});
</script>
@endsection
