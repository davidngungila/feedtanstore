@extends('layouts.app')

@section('page-title', 'Rider Dashboard')

@section('content')
<div class="animate-[fadeIn_0.4s_ease] space-y-6">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-primary-900">Rider Dashboard</h2>
                <p class="text-gray-600">Welcome, {{ $rider->name }}</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="p-4 bg-orange-50 border border-orange-200 rounded-xl">
                <div class="text-sm text-orange-600 font-semibold">Assigned Orders</div>
                <div class="text-3xl font-bold text-orange-800">{{ $assignedOrders->count() }}</div>
            </div>
            <div class="p-4 bg-green-50 border border-green-200 rounded-xl">
                <div class="text-sm text-green-600 font-semibold">Delivered Today</div>
                <div class="text-3xl font-bold text-green-800">{{ $deliveredOrders->where('created_at', '>=', now()->startOfDay())->count() }}</div>
            </div>
        </div>

        <!-- Map Section -->
        @if($assignedOrders->whereNotNull('delivery_latitude')->whereNotNull('delivery_longitude')->count() > 0)
        <div class="mb-6">
            <div class="card rounded-2xl overflow-hidden">
                <div id="rider-map" class="w-full h-[400px]"></div>
            </div>
        </div>
        @endif

        <!-- Assigned Orders -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Assigned Orders</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-700">Order #</th>
                            <th class="px-4 py-3 text-left text-gray-700">Customer</th>
                            <th class="px-4 py-3 text-left text-gray-700">Address</th>
                            <th class="px-4 py-3 text-left text-gray-700">Status</th>
                            <th class="px-4 py-3 text-left text-gray-700">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($assignedOrders as $order)
                        <tr>
                            <td class="px-4 py-3 font-medium text-primary-600">
                                {{ $order->short_customer_reference }}
                            </td>
                            <td class="px-4 py-3">
                                <div>{{ $order->customer_name }}</div>
                                <div class="text-xs text-gray-500">{{ $order->customer_phone }}</div>
                            </td>
                            <td class="px-4 py-3 text-gray-600">
                                {{ Str::limit($order->delivery_address, 30) }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex flex-wrap gap-2">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                        @if($order->status === 'confirmed') bg-blue-100 text-blue-800
                                        @elseif($order->status === 'ready') bg-cyan-100 text-cyan-800
                                        @elseif($order->status === 'out_for_delivery') bg-orange-100 text-orange-800
                                        @endif">
                                        {{ ucwords(str_replace('_', ' ', $order->status)) }}
                                    </span>
                                    @if($order->rider_acceptance_status === 'pending')
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-yellow-100 text-yellow-800">
                                            Pending Acceptance
                                        </span>
                                    @elseif($order->rider_acceptance_status === 'accepted')
                                        <span class="px-2 py-1 rounded-full text-xs font-semibold bg-green-100 text-green-800">
                                            Accepted
                                        </span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-3 flex gap-2">
                                <a href="{{ route('rider.orders.show', $order) }}" class="text-primary-600 hover:text-primary-800 transition-colors" title="View Details">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @if($order->rider_acceptance_status === 'pending')
                                    <form action="{{ route('rider.orders.accept', $order) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-green-600 hover:text-green-800 transition-colors" title="Accept Order">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <form action="{{ route('rider.orders.reject', $order) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="text-red-600 hover:text-red-800 transition-colors" title="Reject Order">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                No assigned orders at the moment.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recently Delivered Orders -->
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Recently Delivered</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-700">Order #</th>
                            <th class="px-4 py-3 text-left text-gray-700">Customer</th>
                            <th class="px-4 py-3 text-left text-gray-700">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @forelse($deliveredOrders as $order)
                        <tr>
                            <td class="px-4 py-3 font-medium text-primary-600">
                                {{ $order->short_customer_reference }}
                            </td>
                            <td class="px-4 py-3">{{ $order->customer_name }}</td>
                            <td class="px-4 py-3">{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-4 py-8 text-center text-gray-500">
                                No delivered orders yet.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@if($assignedOrders->whereNotNull('delivery_latitude')->whereNotNull('delivery_longitude')->count() > 0)
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
    const storeLat = {{ $storeLat }};
    const storeLng = {{ $storeLng }};
    const routes = @json($routes);
    
    const map = L.map('rider-map').setView([storeLat, storeLng], 12);
    
    // OpenStreetMap base layer
    const osmLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    });
    osmLayer.addTo(map);
    
    // Add store marker
    L.marker([storeLat, storeLng])
        .addTo(map)
        .bindPopup('<strong>Store</strong>')
        .openPopup();
    
    // Add markers for assigned orders
    @foreach($assignedOrders as $order)
        @if($order->delivery_latitude && $order->delivery_longitude)
            const orderMarker{{ $order->id }} = L.circleMarker([{{ $order->delivery_latitude }}, {{ $order->delivery_longitude }}], {
                radius: 10,
                fillColor: '{{ $order->status === "ready" ? "#06b6d4" : "#f97316" }}',
                color: '#fff',
                weight: 3,
                fillOpacity: 0.9
            }).addTo(map).bindPopup(`
                <div class="p-2 min-w-[250px]">
                    <h4 class="font-bold text-base text-gray-900 mb-1">Order {{ $order->short_customer_reference }}</h4>
                    <p class="text-sm text-gray-700 mb-1"><strong>Customer:</strong> {{ $order->customer_name }}</p>
                    <p class="text-sm text-gray-700 mb-1"><strong>Phone:</strong> <a href="tel:{{ $order->customer_phone }}">{{ $order->customer_phone }}</a></p>
                    <p class="text-sm text-gray-700 mb-2"><strong>Address:</strong> {{ $order->delivery_address }}</p>
                    <a href="{{ route('rider.orders.show', $order) }}" class="text-primary-600 hover:underline text-sm">View Order Details</a>
                </div>
            `);
            
            // Add route if available
            if (routes['{{ $order->id }}']) {
                const coords{{ $order->id }} = routes['{{ $order->id }}'].features[0].geometry.coordinates;
                const points{{ $order->id }} = coords{{ $order->id }}.map(c => [c[1], c[0]]);
                L.polyline(points{{ $order->id }}, { color: '{{ $order->status === "ready" ? "#06b6d4" : "#f97316" }}', weight: 4, opacity: 0.8 }).addTo(map);
            }
        @endif
    @endforeach
</script>
@endif
@endsection
