@php
    $bulkCheckedIds = $bulkCheckedIds ?? [];
@endphp

@if($bulkOrders->isEmpty())
<div class="card rounded-2xl p-8 text-center">
    <i class="fas fa-box-open text-4xl text-gray-300 mb-4"></i>
    <h2 class="text-lg font-semibold text-gray-800 mb-1">No orders available for dispatch</h2>
    <p class="text-gray-500 text-sm">Packaged and reconciled orders awaiting a rider appear here.</p>
</div>
@else
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<form action="{{ route('marketing-officer.bulk-dispatch.send') }}" method="POST" id="bulk-form">
    @csrf
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Orders to select -->
        <div class="lg:col-span-2 card rounded-2xl overflow-hidden">
            <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                <h2 class="font-semibold text-gray-900">Orders Needing Rider Assignment</h2>
                <span class="text-sm text-gray-500">{{ $bulkOrders->count() }} ready</span>
            </div>
            <div id="order-map" class="w-full h-[320px] border-b border-gray-200"></div>
            <div class="max-h-[480px] overflow-y-auto divide-y divide-gray-100">
                @foreach($bulkOrders as $order)
                @php
                    $hasLocation = $order->delivery_latitude !== null && $order->delivery_longitude !== null;
                    $distanceKm = $hasLocation ? round(\App\Support\Geo::haversine($storeLat, $storeLng, (float)$order->delivery_latitude, (float)$order->delivery_longitude) / 1000, 1) : null;
                    $checked = in_array($order->id, $bulkCheckedIds, true) ? 'checked' : '';
                @endphp
                <label class="flex items-start gap-3 px-4 py-3 hover:bg-gray-50 cursor-pointer order-row">
                    <input type="checkbox" name="order_ids[]" value="{{ $order->id }}" data-id="{{ $order->id }}" {{ $checked }} class="order-checkbox mt-1 rounded text-primary-600">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <p class="font-semibold text-gray-900 text-sm">{{ $order->order_number }}</p>
                            <span class="text-xs font-medium px-2 py-0.5 rounded-full {{ $hasLocation ? 'bg-blue-50 text-blue-700' : 'bg-gray-100 text-gray-600' }}">{{ $hasLocation ? $distanceKm.' km' : 'No location' }}</span>
                        </div>
                        <p class="text-sm text-gray-600 truncate">{{ $order->customer_name }} · {{ $order->customer_phone }}</p>
                        <p class="text-xs text-gray-500 truncate"><i class="fas fa-map-marker-alt mr-1"></i>{{ $order->delivery_address }}</p>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $order->items->count() }} item(s) · TZS {{ number_format($order->total, 0) }} · {{ ucfirst(str_replace('_', ' ', $order->payment_method ?? 'cash')) }} {{ $order->payment_status }}
                        </p>
                    </div>
                </label>
                @endforeach
            </div>
        </div>

        <!-- Dispatch settings -->
        <div class="space-y-6">
            <div class="card rounded-2xl p-6">
                <h2 class="font-semibold text-gray-900 mb-4"><i class="fas fa-layer-group mr-2 text-primary-600"></i>Batch by Location</h2>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="radius_km">Cluster radius (km)</label>
                <input type="number" step="0.5" min="1" max="100" name="radius_km" id="radius_km" value="{{ $defaultRadius }}"
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                <p class="text-xs text-gray-500 mt-1">Orders whose customers are within this distance of each other are grouped into the same dispatch batch.</p>

                <div id="preview-container" class="mt-4 hidden">
                    <h3 class="text-sm font-semibold text-gray-800 mb-2">Suggested Batches</h3>
                    <div id="preview-list" class="space-y-2"></div>
                </div>
            </div>

            <div class="card rounded-2xl p-6">
                <h2 class="font-semibold text-gray-900 mb-4"><i class="fas fa-motorcycle mr-2 text-primary-600"></i>Dispatch To</h2>
                <label class="block text-sm font-medium text-gray-700 mb-1" for="rider_id">Rider</label>
                <select name="rider_id" id="rider_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <option value="">All available riders (first to accept wins)</option>
                    @foreach($riders as $rider)
                        <option value="{{ $rider->id }}">{{ $rider->name }} ({{ $rider->vehicle_type ?? '—' }} {{ $rider->vehicle_plate ?? '' }})</option>
                    @endforeach
                </select>

                <label class="block text-sm font-medium text-gray-700 mt-4 mb-1" for="notes">Notes (optional)</label>
                <textarea name="notes" id="notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500"></textarea>
            </div>

            <button type="button" onclick="previewBatches()" class="w-full px-4 py-3 border border-primary-300 text-primary-700 rounded-lg text-sm font-medium hover:bg-primary-50 transition-colors">
                <i class="fas fa-eye mr-2"></i>Preview Suggested Batches
            </button>
            <button type="submit" class="w-full bg-primary-600 hover:bg-primary-700 text-white px-4 py-3 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-paper-plane mr-2"></i>Dispatch Selected Orders
            </button>
        </div>
    </div>
</form>

<script>
(function () {
    const storeLat = {{ $storeLat }};
    const storeLng = {{ $storeLng }};
    const orders = @json($ordersForMap);

    let map = null;
    const markers = {};

    function haversineKm(lat1, lng1, lat2, lng2) {
        const R = 6371;
        const dLat = (lat2 - lat1) * Math.PI / 180;
        const dLng = (lng2 - lng1) * Math.PI / 180;
        const a = Math.sin(dLat / 2) ** 2 + Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * Math.sin(dLng / 2) ** 2;
        return 2 * R * Math.asin(Math.sqrt(a));
    }

    function initMap() {
        map = L.map('order-map').setView([storeLat, storeLng], 12);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        L.marker([storeLat, storeLng], { icon: L.divIcon({
            className: '', html: '<div style="background:#059669;border:2px solid #fff;border-radius:8px;width:34px;height:34px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;box-shadow:0 2px 8px rgba(0,0,0,.3)"><i class="fas fa-store"></i></div>',
            iconSize: [34, 34], iconAnchor: [17, 34]
        }) }).addTo(map).bindTooltip('Store', { permanent: false });

        orders.forEach(o => {
            const icon = L.divIcon({
                className: '', html: '<div style="background:#3b82f6;border:2px solid #fff;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;box-shadow:0 2px 6px rgba(0,0,0,.3)">'+o.id+'</div>',
                iconSize: [28, 28], iconAnchor: [14, 14]
            });
            const m = L.marker([o.lat, o.lng], { icon }).addTo(map)
                .bindTooltip(o.order_number + ' — ' + o.customer_name, { direction: 'top' })
                .on('click', () => { const cb = document.querySelector('.order-checkbox[data-id="'+o.id+'"]'); if (cb) { cb.checked = !cb.checked; onSelectionChange(); } });
            markers[o.id] = m;
        });
    }

    function selectedOrderIds() {
        return Array.from(document.querySelectorAll('.order-checkbox:checked')).map(cb => parseInt(cb.dataset.id, 10));
    }

    function updateMarker(o) {
        const m = markers[o.id];
        if (!m) return;
        const selected = document.querySelector('.order-checkbox[data-id="'+o.id+'"]')?.checked;
        const color = selected ? '#059669' : '#3b82f6';
        m.setIcon(L.divIcon({
            className: '', html: '<div style="background:'+color+';border:2px solid #fff;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;box-shadow:0 2px 6px rgba(0,0,0,.3)">'+o.id+'</div>',
            iconSize: [28, 28], iconAnchor: [14, 14]
        }));
    }

    function onSelectionChange() {
        const ids = selectedOrderIds();
        const counter = document.getElementById('selected-count');
        if (counter) counter.textContent = ids.length;
        orders.forEach(o => updateMarker(o));
    }

    function clusterSelected(radiusKm) {
        const ids = selectedOrderIds();
        const selected = orders.filter(o => ids.includes(o.id));
        const clusters = [];
        selected.forEach(o => {
            let placed = false;
            for (let i = 0; i < clusters.length; i++) {
                const c = clusters[i];
                if (haversineKm(c.centroid[0], c.centroid[1], o.lat, o.lng) <= radiusKm) {
                    c.orders.push(o);
                    c.centroid = [
                        c.orders.reduce((s, x) => s + x.lat, 0) / c.orders.length,
                        c.orders.reduce((s, x) => s + x.lng, 0) / c.orders.length,
                    ];
                    placed = true;
                    break;
                }
            }
            if (!placed) clusters.push({ centroid: [o.lat, o.lng], orders: [o] });
        });
        return clusters;
    }

    window.selectAll = function () {
        document.querySelectorAll('.order-checkbox').forEach(cb => cb.checked = true);
        onSelectionChange();
    };

    window.clearSelection = function () {
        document.querySelectorAll('.order-checkbox').forEach(cb => cb.checked = false);
        onSelectionChange();
    };

    window.previewBatches = function () {
        const radius = parseFloat(document.getElementById('radius_km').value) || 5;
        const clusters = clusterSelected(radius);
        const container = document.getElementById('preview-container');
        const list = document.getElementById('preview-list');

        if (clusters.length === 0) {
            container.classList.add('hidden');
            return;
        }

        list.innerHTML = clusters.map((c, i) => {
            const total = c.orders.reduce((s, o) => s + o.total, 0);
            return '<div class="border border-gray-200 rounded-lg p-3">' +
                '<p class="text-xs font-semibold text-gray-700">Batch ' + (i + 1) + ' — ' + c.orders.length + ' order(s)</p>' +
                '<p class="text-xs text-gray-500">' + c.orders.map(o => o.order_number).join(', ') + '</p>' +
                '<p class="text-xs text-gray-500 mt-1">TZS ' + total.toLocaleString() + '</p>' +
            '</div>';
        }).join('');

        container.classList.remove('hidden');
        list.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    };

    document.querySelectorAll('.order-checkbox').forEach(cb => cb.addEventListener('change', onSelectionChange));

    document.addEventListener('DOMContentLoaded', function () {
        initMap();
        onSelectionChange();
    });
})();
</script>
@endif
