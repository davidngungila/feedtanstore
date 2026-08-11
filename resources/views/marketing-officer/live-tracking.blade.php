@extends('layouts.app')

@section('page-title', 'Live Tracking - ' . $order->order_number)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <a href="{{ route('marketing-officer.order-details', $order->id) }}" class="text-primary-600 hover:text-primary-800 font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Order Details
        </a>
        <div class="flex items-center gap-3">
            <span id="conn-badge" class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600">
                <span class="w-2 h-2 rounded-full bg-gray-400"></span> Connecting...
            </span>
            <span id="stale-badge" class="hidden inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-xs font-semibold bg-yellow-50 text-yellow-700">
                <i class="fas fa-satellite-dish"></i> Waiting for rider GPS...
            </span>
        </div>
    </div>

    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
            <div>
                <h1 class="text-2xl font-bold text-primary-900">Live Tracking - {{ $order->order_number }}</h1>
                <p class="text-sm text-gray-500">Real-time delivery monitoring ({{ $order->customer_name }})</p>
            </div>
            <div class="flex items-center gap-2">
                <label class="inline-flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                    <input type="checkbox" id="auto-follow" class="rounded text-primary-600" checked>
                    Auto-follow driver
                </label>
                <button id="recenter-btn" class="btn px-4 py-2 rounded-lg text-sm bg-primary-600 text-white hover:bg-primary-700">
                    <i class="fas fa-crosshairs mr-1"></i>Recenter
                </button>
            </div>
        </div>

        <!-- Trip status stepper -->
        <div id="status-stepper" class="flex items-center gap-1 overflow-x-auto pb-2"></div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 card rounded-2xl p-4">
            <div id="live-map" class="w-full h-[520px] rounded-xl overflow-hidden"></div>
        </div>

        <div class="space-y-6">
            <div class="card rounded-2xl p-6">
                <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-4"><i class="fas fa-stopwatch mr-2 text-primary-600"></i>Arrival Estimate</h2>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-3xl font-bold text-primary-900" id="eta-value">--</p>
                        <p class="text-xs text-gray-500">ETA</p>
                    </div>
                    <div>
                        <p class="text-3xl font-bold text-primary-900" id="distance-value">--</p>
                        <p class="text-xs text-gray-500">Distance</p>
                    </div>
                </div>
                <div class="mt-4">
                    <p class="text-xs text-gray-500">Current speed</p>
                    <p class="text-lg font-semibold text-gray-800" id="speed-value">--</p>
                </div>
            </div>

            <div class="card rounded-2xl p-6">
                <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-4"><i class="fas fa-user-tag mr-2 text-primary-600"></i>Driver</h2>
                <div class="flex items-center gap-4 mb-4">
                    <div class="w-12 h-12 rounded-full bg-primary-100 flex items-center justify-center text-primary-700 text-xl">
                        <i class="fas fa-motorcycle"></i>
                    </div>
                    <div>
                        <p class="font-semibold text-gray-900" id="driver-name">{{ $payload['driver']['name'] ?? 'Rider' }}</p>
                        <p class="text-sm text-gray-500" id="driver-vehicle">
                            {{ $payload['driver']['vehicle_type'] ?? '' }} {{ $payload['driver']['vehicle_plate'] ?? '' }}
                        </p>
                    </div>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Phone</span><span class="font-medium" id="driver-phone">{{ $payload['driver']['phone'] ?? '-' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Vehicle</span><span class="font-medium">{{ $payload['driver']['vehicle_type'] ?? '-' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Plate</span><span class="font-medium">{{ $payload['driver']['vehicle_plate'] ?? '-' }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Status</span><span class="font-medium capitalize" id="trip-status-label">{{ str_replace('_', ' ', $session->status) }}</span></div>
                </div>
            </div>

            <div class="card rounded-2xl p-6">
                <h2 class="text-sm font-bold text-gray-500 uppercase tracking-wide mb-4"><i class="fas fa-box-open mr-2 text-primary-600"></i>Order</h2>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between"><span class="text-gray-500">Delivery address</span><span class="font-medium text-right">{{ $order->delivery_address }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Payment</span><span class="font-medium capitalize">{{ $order->payment_method }} · {{ $order->payment_status }}</span></div>
                    <div class="flex justify-between"><span class="text-gray-500">Total</span><span class="font-medium">{{ number_format($order->total) }} TZS</span></div>
                </div>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdn.jsdelivr.net/npm/pusher-js@8.4.0/dist/web/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.16.1/dist/echo.iife.js"></script>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<style>
    .driver-marker { position: relative; width: 44px; height: 44px; display: flex; align-items: center; justify-content: center; }
    .driver-marker .driver-marker-arrow {
        position: absolute; top: -2px; left: 50%; transform: translateX(-50%) rotate(0deg);
        font-size: 13px; color: #059669; line-height: 1;
        transition: transform 300ms linear;
    }
    .driver-marker .driver-marker-body {
        width: 36px; height: 36px; border-radius: 50%;
        background: #059669; color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 16px;
        border: 3px solid #fff;
        box-shadow: 0 2px 10px rgba(0,0,0,0.3);
    }
    .driver-marker.is-stale .driver-marker-body { background: #d97706; }
    .step-item { min-width: 108px; text-align: center; flex-shrink: 0; }
    .step-dot { width: 12px; height: 12px; border-radius: 50%; margin: 0 auto 4px; background: #e5e7eb; border: 2px solid #fff; box-shadow: 0 0 0 2px #e5e7eb; }
    .step-item.done .step-dot { background: #059669; box-shadow: 0 0 0 2px #059669; }
    .step-item.active .step-dot { background: #d97706; box-shadow: 0 0 0 2px #d97706; }
    .step-item.cancelled .step-dot { background: #dc2626; box-shadow: 0 0 0 2px #dc2626; }
    .step-line { flex: 1; height: 2px; background: #e5e7eb; min-width: 16px; margin-top: 5px; }
    .step-line.done { background: #059669; }
    .step-label { font-size: 10px; font-weight: 600; color: #6b7280; text-transform: capitalize; }
    .step-item.done .step-label { color: #059669; }
    .step-item.active .step-label { color: #d97706; }
</style>

<script>
    (function () {
        const sessionId = {{ $session->id }};
        const orderNumber = @json($order->order_number);
        const initialPayload = @json($payload);
        const reverb = @json($reverb);
        const recalcUrl = @json(route('marketing-officer.recalculate-route', $order->id));
        const pollUrl = @json(url('/api/tracking/order/' . $order->order_number));

        const OFF_ROUTE_THRESHOLD_M = 150;
        const RECALC_COOLDOWN_MS = 60 * 1000;
        const FALLBACK_POLL_MS = 10 * 1000;

        // ---- Map ----
        const map = L.map('live-map');
        const osmLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        });
        const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri'
        });
        osmLayer.addTo(map);
        L.control.layers({ 'OpenStreetMap': osmLayer, 'Satellite': satelliteLayer }).addTo(map);

        // Pickup + destination markers
        const pickup = initialPayload.pickup;
        const destination = initialPayload.destination;
        L.marker([pickup.latitude, pickup.longitude], {
            icon: L.divIcon({ className: '', html: '<div style="width:14px;height:14px;border-radius:50%;background:#059669;border:3px solid #fff;box-shadow:0 1px 6px rgba(0,0,0,.4)"></div>', iconSize: [14, 14], iconAnchor: [7, 7] })
        }).addTo(map).bindPopup('<strong>Pickup</strong>');

        L.marker([destination.latitude, destination.longitude], {
            icon: L.divIcon({ className: '', html: '<div style="width:16px;height:16px;border-radius:50%;background:#f97316;border:3px solid #fff;box-shadow:0 1px 6px rgba(0,0,0,.4)"></div>', iconSize: [16, 16], iconAnchor: [8, 8] })
        }).addTo(map).bindPopup('<strong>Destination</strong>');

        // Driver marker (rotating vehicle icon)
        const driverMarkerEl = document.createElement('div');
        driverMarkerEl.className = 'driver-marker';
        driverMarkerEl.innerHTML = '<div class="driver-marker-arrow">▲</div><div class="driver-marker-body"><i class="fas fa-motorcycle"></i></div>';
        const driverIcon = L.divIcon({ className: '', html: driverMarkerEl.outerHTML, iconSize: [44, 44], iconAnchor: [22, 22] });
        const driverMarker = L.marker([pickup.latitude, pickup.longitude], { icon: driverIcon, interactive: false, zIndexOffset: 1000 }).addTo(map);
        const arrowEl = driverMarker.getElement().querySelector('.driver-marker-arrow');
        const bodyEl = driverMarker.getElement().querySelector('.driver-marker-body');

        // Route polyline
        let routeLayer = null;

        // ---- State ----
        let driverPos = null;
        let driverTarget = null;
        let headingNow = 0;
        let headingTarget = 0;
        let autoFollow = true;
        let lastRecalcAt = 0;
        let rafId = null;

        function renderRoute(route) {
            if (routeLayer) { routeLayer.remove(); routeLayer = null; }
            const poly = route && route.polyline && route.polyline.length >= 2 ? route.polyline : null;
            if (poly) {
                routeLayer = L.polyline(poly.map(p => [p[0], p[1]]), { color: '#3b82f6', weight: 5, opacity: 0.75 }).addTo(map);
            }
        }

        function fitInitialView() {
            const route = initialPayload.route && initialPayload.route.polyline && initialPayload.route.polyline.length
                ? initialPayload.route.polyline.map(p => [p[0], p[1]])
                : null;
            const points = route && route.length
                ? route
                : [[pickup.latitude, pickup.longitude], [destination.latitude, destination.longitude]];
            map.fitBounds(L.latLngBounds(points), { padding: [60, 60] });
        }

        function shortestAngle(from, to) {
            let d = (to - from) % 360;
            if (d > 180) d -= 360;
            if (d < -180) d += 360;
            return d;
        }

        function distanceM(a, b) {
            const R = 6371000;
            const dLat = (b[0] - a[0]) * Math.PI / 180;
            const dLng = (b[1] - a[1]) * Math.PI / 180;
            const s = Math.sin(dLat / 2) ** 2 + Math.cos(a[0] * Math.PI / 180) * Math.cos(b[0] * Math.PI / 180) * Math.sin(dLng / 2) ** 2;
            return 2 * R * Math.asin(Math.min(1, Math.sqrt(s)));
        }

        function distanceToSegment(p, a, b) {
            const dx = b[1] - a[1], dy = b[0] - a[0];
            const lenSq = dx * dx + dy * dy;
            if (lenSq < 1e-12) return distanceM(p, a);
            let t = ((p[1] - a[1]) * dx + (p[0] - a[0]) * dy) / lenSq;
            t = Math.max(0, Math.min(1, t));
            return distanceM(p, [a[0] + t * dy, a[1] + t * dx]);
        }

        function maxDistanceToRoute(p) {
            const poly = routeLayer && routeLayer.getLatLngs();
            if (!poly || poly.length < 2) return Infinity;
            const pts = Array.isArray(poly[0]) ? poly[0] : poly;
            let max = 0;
            for (let i = 0; i < pts.length - 1; i++) {
                max = Math.max(max, distanceToSegment(p, [pts[i].lat, pts[i].lng], [pts[i + 1].lat, pts[i + 1].lng]));
            }
            return max;
        }

        function maybeRecalculate() {
            if (!driverTarget) return;
            const off = maxDistanceToRoute([driverTarget.lat, driverTarget.lng]);
            const now = Date.now();
            if (off > OFF_ROUTE_THRESHOLD_M && now - lastRecalcAt > RECALC_COOLDOWN_MS) {
                lastRecalcAt = now;
                fetch(recalcUrl, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } })
                    .then(r => r.json())
                    .then(data => {
                        renderRoute(data.route);
                    }).catch(() => {});
            }
        }

        // ---- Smooth marker animation ----
        function animate() {
            if (!driverPos || !driverTarget) return;
            const k = 0.18;
            driverPos.lat += (driverTarget.lat - driverPos.lat) * k;
            driverPos.lng += (driverTarget.lng - driverPos.lng) * k;
            headingNow += shortestAngle(headingNow, headingTarget) * 0.22;
            headingNow = (headingNow + 360) % 360;

            driverMarker.setLatLng(driverPos);
            if (arrowEl) arrowEl.style.transform = 'translateX(-50%) rotate(' + headingNow + 'deg)';
            if (autoFollow) map.panTo(driverPos, { animate: false });

            if (distanceM([driverPos.lat, driverPos.lng], [driverTarget.lat, driverTarget.lng]) > 0.00001) {
                rafId = requestAnimationFrame(animate);
            } else {
                rafId = null;
            }
        }

        function onLocation(data) {
            const d = data.driver || {};
            if (d.latitude == null || d.longitude == null) return;

            driverTarget = L.latLng(parseFloat(d.latitude), parseFloat(d.longitude));
            if (!driverPos) { driverPos = L.latLng(driverTarget.lat, driverTarget.lng); }
            if (d.heading != null) headingTarget = parseFloat(d.heading);

            if (data.route && data.route.polyline && data.route.polyline.length) renderRoute(data.route);
            else if (!routeLayer && initialPayload.route) renderRoute(initialPayload.route);

            maybeRecalculate();
            updateHud(data);

            if (rafId == null) rafId = requestAnimationFrame(animate);
        }

        // ---- HUD (ETA / distance / speed / stale) ----
        function updateHud(data) {
            if (data.eta_seconds != null) {
                const s = data.eta_seconds;
                document.getElementById('eta-value').textContent = s < 60 ? '<1 min' : Math.round(s / 60) + ' min';
            }
            if (data.distance_remaining != null) {
                const m = data.distance_remaining;
                document.getElementById('distance-value').textContent = m < 1000 ? Math.round(m) + ' m' : (m / 1000).toFixed(1) + ' km';
            }
            if (data.driver && data.driver.speed != null) {
                const kmh = Math.round(data.driver.speed * 3.6);
                document.getElementById('speed-value').textContent = kmh + ' km/h';
            }
            const stale = document.getElementById('stale-badge');
            const isStale = !!data.stale;
            stale.classList.toggle('hidden', !isStale);
            if (driverMarker.getElement()) driverMarker.getElement().classList.toggle('is-stale', isStale);
        }

        // ---- Status stepper ----
        const STEPS = ['accepted', 'driver_arriving', 'driver_arrived', 'trip_started', 'trip_in_progress', 'trip_completed'];
        function renderStepper(status) {
            const container = document.getElementById('status-stepper');
            container.innerHTML = '';
            if (status === 'cancelled') {
                container.innerHTML = '<div class="step-item cancelled"><div class="step-dot"></div><div class="step-label">Cancelled</div></div>';
                return;
            }
            const idx = STEPS.indexOf(status);
            STEPS.forEach((s, i) => {
                const item = document.createElement('div');
                item.className = 'step-item' + (i < idx ? ' done' : i === idx ? ' active' : '');
                item.innerHTML = '<div class="step-dot"></div><div class="step-label">' + s.replace(/_/g, ' ') + '</div>';
                container.appendChild(item);
                if (i < STEPS.length - 1) {
                    const line = document.createElement('div');
                    line.className = 'step-line' + (i < idx ? ' done' : '');
                    container.appendChild(line);
                }
            });
            document.getElementById('trip-status-label').textContent = status.replace(/_/g, ' ');
        }

        function onStatus(status) {
            renderStepper(status);
            if (status === 'trip_completed') {
                Swal.fire({ icon: 'success', title: 'Trip completed', text: 'This delivery has been marked as delivered.' });
            }
            if (status === 'cancelled') {
                Swal.fire({ icon: 'warning', title: 'Trip cancelled', text: 'This delivery has been cancelled.' });
            }
        }

        // ---- WebSocket (Laravel Echo + Reverb) ----
        let echo = null;
        function connectEcho() {
            try {
                if (!window.Echo || !window.Pusher) return;
                echo = new Echo({
                    broadcaster: 'pusher',
                    key: reverb.key,
                    wsHost: reverb.host,
                    wsPort: reverb.port,
                    wssPort: reverb.port,
                    forceTLS: reverb.useTLS,
                    enabledTransports: ['ws', 'wss'],
                    authEndpoint: '/broadcasting/auth',
                    auth: { headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } }
                });

                const channel = echo.private('tracking.session.' + sessionId);
                channel.listen('.driver.location.updated', onLocation);
                channel.listen('.trip.status.updated', (e) => onStatus(e.status));
                channel.listen('.trip.completed', () => onStatus('trip_completed'));
                channel.listen('.trip.cancelled', () => onStatus('cancelled'));

                echo.connector.pusher.connection.bind('connected', () => setConn(true));
                echo.connector.pusher.connection.bind('disconnected', () => setConn(false));
                echo.connector.pusher.connection.bind('state_change', (s) => setConn(s.current === 'connected'));
            } catch (e) {
                setConn(false);
            }
        }

        const connBadge = document.getElementById('conn-badge');
        function setConn(ok) {
            connBadge.innerHTML = ok
                ? '<span class="w-2 h-2 rounded-full bg-green-500"></span> Live'
                : '<span class="w-2 h-2 rounded-full bg-yellow-500"></span> Polling fallback';
        }

        // ---- Fallback polling when WebSocket is unavailable ----
        let lastFallbackKey = null;
        function pollFallback() {
            fetch(pollUrl).then(r => r.json()).then(data => {
                const live = data.session && (data.session.live_location || data.session.latest_location);
                if (live && data.session.latest_location) {
                    const key = data.session.latest_location.id + ':' + (data.session.latest_location.recorded_at || '');
                    if (key !== lastFallbackKey) {
                        lastFallbackKey = key;
                        const loc = data.session.latest_location;
                        onLocation({
                            driver: {
                                latitude: loc.latitude, longitude: loc.longitude,
                                heading: loc.heading, speed: loc.speed, accuracy: loc.accuracy
                            },
                            route: data.session.route,
                            stale: false,
                            distance_remaining: null,
                            eta_seconds: null
                        });
                    }
                }
                if (data.session && data.session.status) onStatus(data.session.status);
            }).catch(() => {});
        }

        // ---- Init ----
        renderStepper(initialPayload.status || 'accepted');
        renderRoute(initialPayload.route);
        if (initialPayload.latest_location) {
            const loc = initialPayload.latest_location;
            onLocation({
                driver: { latitude: loc.latitude, longitude: loc.longitude, heading: loc.heading, speed: loc.speed, accuracy: loc.accuracy },
                route: initialPayload.route,
                stale: false,
                distance_remaining: null,
                eta_seconds: null
            });
        }
        fitInitialView();

        document.getElementById('auto-follow').addEventListener('change', e => autoFollow = e.target.checked);
        document.getElementById('recenter-btn').addEventListener('click', () => {
            if (driverPos) map.setView(driverPos, Math.max(map.getZoom(), 15), { animate: true });
        });

        connectEcho();
        setInterval(pollFallback, FALLBACK_POLL_MS);
    })();
</script>
@endsection
