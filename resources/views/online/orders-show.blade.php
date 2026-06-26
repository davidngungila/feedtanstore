@extends('layouts.app')

@section('page-title', 'Order #' . $order->order_number)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Order #{{ $order->order_number }}</h2>
            <div class="flex gap-3">
                <a href="{{ route('online.orders.download', $order) }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-download mr-2"></i>Download PDF
                </a>
                <a href="{{ route('online.orders') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Orders
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div>
                <span class="text-sm text-gray-600">Status:</span>
                <span class="ml-2 px-3 py-1 rounded-full text-xs font-semibold 
                    @if($order->status === 'pending') bg-yellow-100 text-yellow-800
                    @elseif($order->status === 'confirmed') bg-blue-100 text-blue-800
                    @elseif($order->status === 'preparing') bg-purple-100 text-purple-800
                    @elseif($order->status === 'ready') bg-cyan-100 text-cyan-800
                    @elseif($order->status === 'out_for_delivery') bg-orange-100 text-orange-800
                    @elseif($order->status === 'delivered') bg-green-100 text-green-800
                    @else bg-red-100 text-red-800 @endif">
                    {{ ucwords(str_replace('_', ' ', $order->status)) }}
                </span>
            </div>
            <div>
                <span class="text-sm text-gray-600">Payment Status:</span>
                <span class="ml-2 px-3 py-1 rounded-full text-xs font-semibold 
                    @if($order->payment_status === 'paid') bg-green-100 text-green-800
                    @elseif($order->payment_status === 'pending') bg-yellow-100 text-yellow-800
                    @else bg-red-100 text-red-800 @endif">
                    {{ ucwords($order->payment_status) }}
                </span>
            </div>
            <div>
                <span class="text-sm text-gray-600">Date:</span>
                <span class="ml-2">{{ $order->created_at->format('d/m/Y H:i') }}</span>
            </div>
        </div>

        <!-- Customer Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
            <div>
                <h4 class="font-semibold text-primary-900 mb-2">Customer</h4>
                <p class="mb-1"><strong>Name:</strong> {{ $order->customer_name }}</p>
                <p class="mb-1"><strong>Phone:</strong> {{ $order->customer_phone }}</p>
                @if($order->customer_email)
                    <p class="mb-1"><strong>Email:</strong> {{ $order->customer_email }}</p>
                @endif
            </div>
            <div>
                <h4 class="font-semibold text-primary-900 mb-2">Delivery</h4>
                <p class="mb-1"><strong>Address:</strong> {{ $order->delivery_address }}</p>
                @if($order->delivery_latitude && $order->delivery_longitude)
                    <p class="mb-1 text-sm text-gray-600">
                        <i class="fas fa-map-marker-alt mr-1"></i>
                        {{ number_format($order->delivery_latitude, 6) }}, {{ number_format($order->delivery_longitude, 6) }}
                    </p>
                @endif
                @if($order->rider)
                    <p class="mb-1"><strong>Rider:</strong> {{ $order->rider->name }} ({{ $order->rider->phone }})</p>
                @endif
                <p class="mb-1"><strong>Payment Method:</strong> {{ $order->payment_method ?? 'N/A' }}</p>
            </div>
        </div>

        @if($order->delivery_latitude && $order->delivery_longitude)
            <div class="card rounded-2xl overflow-hidden mb-6">
                <div class="p-4 border-b">
                    <h4 class="font-semibold text-primary-900">Delivery Location & Route</h4>
                </div>
                <div id="order-map" class="w-full h-[400px]"></div>
            </div>

            <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
            <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
            <script>
                const storeLat = {{ $settings->store_latitude ?? -3.3869 }};
                const storeLng = {{ $settings->store_longitude ?? 36.6883 }};
                const orderLat = {{ $order->delivery_latitude }};
                const orderLng = {{ $order->delivery_longitude }};
                const route = @json($route);
                
                const orderMap = L.map('order-map').setView([(storeLat + orderLat) / 2, (storeLng + orderLng) / 2], 12);
                
                // OpenStreetMap base layer
                const osmLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
                });
                
                // World Imagery base layer (Esri)
                const worldImageryLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                    attribution: 'Tiles &copy; Esri &mdash; Source: Esri, DigitalGlobe, GeoEye, i-cubed, USDA, USGS, AEX, Getmapping, Aerogrid, IGN, IGP, swisstopo, and the GIS User Community'
                });
                
                // Add OSM as default
                osmLayer.addTo(orderMap);
                
                // Layer control
                const baseLayers = {
                    'OpenStreetMap': osmLayer,
                    'World Imagery': worldImageryLayer
                };
                
                L.control.layers(baseLayers).addTo(orderMap);
                
                // Add store marker
                L.marker([storeLat, storeLng])
                    .addTo(orderMap)
                    .bindPopup(`<strong>Store</strong>`).openPopup();
                
                // Add order marker
                L.circleMarker([orderLat, orderLng], {
                    radius: 8,
                    fillColor: '#f97316',
                    color: '#fff',
                    weight: 2,
                    fillOpacity: 0.8
                })
                    .addTo(orderMap)
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
                    L.polyline(points, { color: '#3b82f6', weight: 4, opacity: 0.7 }).addTo(orderMap);
                    orderMap.fitBounds(points, { padding: [50, 50] });
                }
            </script>
        @endif

        <!-- Order Items -->
        <h3 class="text-lg font-semibold text-primary-900 mb-3">Order Items</h3>
        <div class="overflow-x-auto mb-6">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-gray-700">Product</th>
                        <th class="px-4 py-2 text-left text-gray-700">Price</th>
                        <th class="px-4 py-2 text-left text-gray-700">Quantity</th>
                        <th class="px-4 py-2 text-left text-gray-700">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($order->items as $item)
                    <tr>
                        <td class="px-4 py-2">{{ $item->product->name }}</td>
                        <td class="px-4 py-2">TZS {{ number_format($item->price, 2) }}</td>
                        <td class="px-4 py-2">{{ $item->quantity }}</td>
                        <td class="px-4 py-2">TZS {{ number_format($item->total, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Order Summary -->
        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-right">
                <div class="md:col-span-2">
                    <span class="text-gray-600">Subtotal:</span>
                </div>
                <div>
                    <span class="font-semibold text-primary-900">TZS {{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="md:col-span-2">
                    <span class="text-gray-600">Delivery Fee:</span>
                </div>
                <div>
                    <span class="font-semibold text-primary-900">TZS {{ number_format($order->delivery_fee, 2) }}</span>
                </div>
                @if(($order->discount ?? 0) > 0)
                <div class="md:col-span-2">
                    <span class="text-gray-600">Discount (FEEDTAN5K):</span>
                </div>
                <div>
                    <span class="font-semibold text-green-700">-TZS {{ number_format($order->discount, 2) }}</span>
                </div>
                @endif
                <div class="md:col-span-2">
                    <span class="text-lg font-semibold text-primary-900">Total:</span>
                </div>
                <div>
                    <span class="text-xl font-bold text-primary-900">TZS {{ number_format($order->total, 2) }}</span>
                </div>
            </div>
        </div>

        @if($order->notes)
            <div class="p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded mb-6">
                <h4 class="font-semibold text-yellow-800 mb-1">Notes</h4>
                <p class="text-sm text-yellow-700">{{ $order->notes }}</p>
            </div>
        @endif

        <!-- Action Buttons -->
        <div class="flex flex-wrap gap-3 justify-end mb-6">
            <!-- Update Status -->
            <form action="{{ route('online.orders.status', $order) }}" method="POST" class="flex flex-wrap gap-2 items-center">
                @csrf
                @method('PUT')
                <select name="status" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="confirmed" {{ $order->status === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="preparing" {{ $order->status === 'preparing' ? 'selected' : '' }}>Preparing</option>
                    <option value="ready" {{ $order->status === 'ready' ? 'selected' : '' }}>Ready</option>
                    <option value="out_for_delivery" {{ $order->status === 'out_for_delivery' ? 'selected' : '' }}>Out for Delivery</option>
                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
                <select name="payment_status" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="pending" {{ $order->payment_status === 'pending' ? 'selected' : '' }}>Payment: Pending</option>
                    <option value="paid" {{ $order->payment_status === 'paid' ? 'selected' : '' }}>Payment: Paid</option>
                    <option value="failed" {{ $order->payment_status === 'failed' ? 'selected' : '' }}>Payment: Failed</option>
                </select>
                <input type="text" name="notes" placeholder="Notes (optional)" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Update Status
                </button>
            </form>

            <!-- Assign Rider -->
            @if($order->status !== 'delivered' && $order->status !== 'cancelled')
            <form action="{{ route('online.orders.assign-rider', $order) }}" method="POST" class="flex flex-wrap gap-2 items-center">
                @csrf
                <select name="delivery_rider_id" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                    <option value="">Assign Rider</option>
                    @foreach(\App\Models\DeliveryRider::where('is_active', true)->get() as $rider)
                        <option value="{{ $rider->id }}" {{ $order->delivery_rider_id == $rider->id ? 'selected' : '' }}>{{ $rider->name }}</option>
                    @endforeach
                </select>
                <input type="text" name="notes" placeholder="Notes (optional)" class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    Assign
                </button>
            </form>
            @endif
        </div>

        <!-- Status History -->
        <div class="card rounded-2xl p-6">
            <h3 class="text-lg font-bold text-primary-900 mb-4 flex items-center gap-2">
                <i class="fas fa-history text-primary-600"></i> Order Tracking History
            </h3>
            @if($order->statusHistory->count() > 0)
                <div class="space-y-4">
                    @foreach($order->statusHistory as $history)
                        <div class="flex gap-4 items-start border-l-2 border-primary-200 pl-4 pb-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 font-semibold text-sm">
                                {{ strtoupper(substr($history->user->name ?? 'System', 0, 1)) }}
                            </div>
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-3 mb-1">
                                    <span class="font-semibold text-primary-900">{{ ucwords(str_replace('_', ' ', $history->status)) }}</span>
                                    @if($history->payment_status)
                                        <span class="text-sm px-2 py-1 rounded-full bg-gray-100 text-gray-700">
                                            {{ ucwords($history->payment_status) }}
                                        </span>
                                    @endif
                                    <span class="text-xs text-gray-500">{{ $history->created_at->format('d/m/Y H:i:s') }}</span>
                                </div>
                                @if($history->notes)
                                    <p class="text-sm text-gray-700">{{ $history->notes }}</p>
                                @endif
                                @if($history->user)
                                    <p class="text-xs text-gray-500 mt-1">By: {{ $history->user->name }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No tracking history yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection
