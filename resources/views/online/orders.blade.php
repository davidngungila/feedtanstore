@extends('layouts.app')

@section('page-title', 'Online Orders')

@section('content')
<div class="animate-[fadeIn_0.4s_ease] space-y-6">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Online Orders</h2>
            <a href="{{ route('online.orders.create') }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                <i class="fas fa-plus mr-2"></i>New Order
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filter Section -->
        <div class="mb-6 p-4 bg-gray-50 rounded-lg">
            <form action="{{ route('online.orders') }}" method="GET" id="onlineOrderSearchForm" class="space-y-4">
                <div class="flex flex-col md:flex-row md:items-center gap-3">
                    <h3 class="font-semibold text-gray-900 whitespace-nowrap">Filter by Status</h3>
                    <div class="relative w-full md:max-w-sm">
                        <input
                            type="text"
                            name="search"
                            id="onlineOrderSearch"
                            value="{{ $search ?? '' }}"
                            placeholder="Search online orders..."
                            class="w-full px-4 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500 bg-white"
                            autocomplete="off"
                        >
                        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary-600">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div class="flex flex-wrap gap-2">
                @foreach($allStatuses as $status)
                    @php
                        $statusLabel = ucwords(str_replace('_', ' ', $status));
                        $isChecked = in_array($status, $statusFilter);
                    @endphp
                    <label class="flex items-center gap-2 px-3 py-2 bg-white rounded-lg cursor-pointer hover:bg-gray-100 transition-colors">
                        <input type="checkbox" name="status[]" value="{{ $status }}" {{ $isChecked ? 'checked' : '' }} class="rounded text-primary-600 focus:ring-primary-500">
                        <span class="text-sm">{{ $statusLabel }}</span>
                    </label>
                @endforeach
                <div class="flex gap-2 ml-auto">
                    <a href="{{ route('online.orders') }}" class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition-colors">Reset</a>
                    <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">Apply Filter</button>
                </div>
                </div>
            </form>
        </div>

        <!-- Map Section -->
        <div class="mb-6">
            <div class="card rounded-2xl overflow-hidden">
                <div id="orders-map" class="w-full h-[400px]"></div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700">Order #</th>
                        <th class="px-4 py-3 text-left text-gray-700">Delivery Code</th>
                        <th class="px-4 py-3 text-left text-gray-700">Customer</th>
                        <th class="px-4 py-3 text-left text-gray-700">Distance</th>
                        <th class="px-4 py-3 text-left text-gray-700">Delivery Fee</th>
                        <th class="px-4 py-3 text-left text-gray-700">Total</th>
                        <th class="px-4 py-3 text-left text-gray-700">Status</th>
                        <th class="px-4 py-3 text-left text-gray-700">Payment</th>
                        <th class="px-4 py-3 text-left text-gray-700">Rider</th>
                        <th class="px-4 py-3 text-left text-gray-700">Date</th>
                        <th class="px-4 py-3 text-left text-gray-700">Actions</th>
                    </tr>
                </thead>
                <tbody id="online-orders-table-body" class="divide-y">
                    @forelse($orders as $order)
                    <tr data-search="{{ strtolower($order->order_number . ' ' . $order->delivery_code . ' ' . ($order->customer_name ?? '') . ' ' . ($order->customer_phone ?? '') . ' ' . ($order->customer_email ?? '') . ' ' . ($order->delivery_address ?? '') . ' ' . ($order->status ?? '') . ' ' . ($order->payment_status ?? '') . ' ' . ($order->payment_method ?? '') . ' ' . ($order->rider->name ?? '') . ' ' . ($order->user->name ?? '') . ' ' . $order->total) }}">
                        <td class="px-4 py-3 font-medium text-primary-600">
                            {{ $order->short_customer_reference }}
                            <span class="text-xs text-gray-400 block">{{ $order->order_number }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 bg-primary-100 text-primary-800 rounded text-xs font-mono font-bold">{{ $order->delivery_code }}</span>
                        </td>
                        <td class="px-4 py-3">
                            <div>{{ $order->customer_name }}</div>
                            <div class="text-xs text-gray-500">{{ $order->customer_phone }}</div>
                        </td>
                        <td class="px-4 py-3">{{ $orderDistances[$order->id] ?? 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $orderDeliveryFees[$order->id] ?? 'N/A' }}</td>
                        <td class="px-4 py-3 font-semibold">TZS {{ number_format($order->total, 2) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                                @elseif($order->status === 'preparing') bg-purple-100 text-purple-800
                                @elseif($order->status === 'ready') bg-cyan-100 text-cyan-800
                                @elseif($order->status === 'out_for_delivery') bg-orange-100 text-orange-800
                                @elseif($order->status === 'delivered') bg-green-100 text-green-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucwords(str_replace('_', ' ', $order->status)) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 rounded-full text-xs font-semibold 
                                @if($order->payment_status === 'paid') bg-green-100 text-green-800
                                @elseif($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                                @else bg-red-100 text-red-800 @endif">
                                {{ ucwords($order->payment_status) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $order->rider ? $order->rider->name : 'Not Assigned' }}</td>
                        <td class="px-4 py-3">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                        <td class="px-4 py-3 flex gap-2">
                            <a href="{{ route('online.orders.show', $order) }}" class="text-primary-600 hover:text-primary-800 transition-colors" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('online.orders.edit', $order) }}" class="text-blue-600 hover:text-blue-800 transition-colors" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('online.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this order?');" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 transition-colors" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="11" class="px-4 py-8 text-center text-gray-500">
                            No online orders found.
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
    const routes = @json($routes);
    
    const map = L.map('orders-map').setView([storeLat, storeLng], 10);
    
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
    
    // Order status to color mapping
    const statusColors = {
        'pending': '#eab308',
        'confirmed': '#3b82f6',
        'preparing': '#a855f7',
        'ready': '#06b6d4',
        'out_for_delivery': '#f97316',
        'delivered': '#22c55e',
        'cancelled': '#ef4444'
    };
    
    // Add store marker
    L.marker([storeLat, storeLng])
        .addTo(map)
        .bindPopup('<strong>Store</strong>')
        .openPopup();
    
    // Add markers for orders with location data
    @foreach($orders as $order)
        @if($order->delivery_latitude && $order->delivery_longitude)
            const orderMarker{{ $order->id }} = L.circleMarker([{{ $order->delivery_latitude }}, {{ $order->delivery_longitude }}], {
                radius: 8,
                fillColor: statusColors['{{ $order->status }}'],
                color: '#fff',
                weight: 2,
                fillOpacity: 0.8
            }).addTo(map).bindPopup(`
                <div class="p-2 min-w-[250px]">
                    <h4 class="font-bold text-base text-gray-900 mb-1">Order {{ $order->short_customer_reference }}</h4>
                    <p class="text-sm text-gray-700 mb-1"><strong>Customer:</strong> {{ $order->customer_name }}</p>
                    <p class="text-sm text-gray-700 mb-1"><strong>Phone:</strong> <a href="tel:{{ $order->customer_phone }}">{{ $order->customer_phone }}</a></p>
                    <p class="text-sm text-gray-700 mb-2"><strong>Address:</strong> {{ $order->delivery_address }}</p>
                    <a href="{{ route('online.orders.show', $order) }}" class="text-primary-600 hover:underline text-sm">View Order</a>
                </div>
            `);
            
            // Add route if available
            if (routes['{{ $order->id }}']) {
                const coords{{ $order->id }} = routes['{{ $order->id }}'].features[0].geometry.coordinates;
                const points{{ $order->id }} = coords{{ $order->id }}.map(c => [c[1], c[0]]);
                L.polyline(points{{ $order->id }}, { color: statusColors['{{ $order->status }}'], weight: 3, opacity: 0.7 }).addTo(map);
            }
        @endif
    @endforeach

    const onlineOrderSearch = document.getElementById('onlineOrderSearch');
    const onlineOrderRows = document.querySelectorAll('#online-orders-table-body tr');
    let onlineOrderSearchTimer = null;

    if (onlineOrderSearch) {
        onlineOrderSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();

            onlineOrderRows.forEach(row => {
                const searchData = row.getAttribute('data-search') || '';
                row.style.display = searchData.includes(searchTerm) ? '' : 'none';
            });

            clearTimeout(onlineOrderSearchTimer);
            onlineOrderSearchTimer = setTimeout(() => {
                document.getElementById('onlineOrderSearchForm').submit();
            }, 350);
        });
    }
</script>
@endsection
