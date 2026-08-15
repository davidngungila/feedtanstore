@extends('layouts.app')

@section('page-title', 'Batch #'.$batch->id)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <a href="{{ route('marketing-officer.dispatch-batches') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Batches
            </a>
            <h1 class="text-2xl font-bold text-primary-900 mt-2">Dispatch Batch #{{ $batch->id }}</h1>
            <p class="text-gray-600">Created {{ $batch->created_at->format('M d, Y H:i') }} by {{ $batch->creator->name ?? 'Marketing Officer' }}</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="px-4 py-2 text-sm font-semibold rounded-full
                {{ $batch->status == 'accepted' ? 'bg-green-100 text-green-700' :
                   ($batch->status == 'cancelled' ? 'bg-red-100 text-red-700' :
                   'bg-yellow-100 text-yellow-700') }}">
                {{ ucfirst($batch->status) }}
            </span>
            <a href="{{ route('marketing-officer.bulk-dispatch') }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-layer-group mr-2"></i>New Bulk Dispatch
            </a>
            @if($batch->status === 'pending')
            <form action="{{ route('marketing-officer.dispatch-batch-cancel', $batch->id) }}" method="POST" onsubmit="return confirm('Cancel batch #{{ $batch->id }}? Its orders will be released for a new dispatch.')">
                @csrf
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-ban mr-2"></i>Cancel Batch
                </button>
            </form>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
        <div class="lg:col-span-3 space-y-6">
            <div class="card rounded-2xl overflow-hidden">
                <div class="p-4 border-b border-gray-200">
                    <h2 class="font-semibold text-gray-900"><i class="fas fa-map-marker-alt mr-2 text-primary-600"></i>Delivery Map</h2>
                </div>
                <div id="batch-map" class="w-full h-[360px]"></div>
            </div>

            <div class="card rounded-2xl overflow-hidden">
                <div class="p-4 border-b border-gray-200 flex items-center justify-between">
                    <h2 class="font-semibold text-gray-900"><i class="fas fa-box mr-2 text-primary-600"></i>Orders in Batch</h2>
                    <span class="text-sm text-gray-500">{{ $orders->count() }} order(s) · TZS {{ number_format($orders->sum('total'), 0) }}</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Order</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Delivery Point</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Distance</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            @forelse($orders as $order)
                            <tr class="hover:bg-gray-50 align-top">
                                <td class="px-6 py-4">
                                    <a href="{{ route('marketing-officer.order-details', $order->id) }}" class="font-semibold text-primary-600 hover:text-primary-800">{{ $order->order_number }}</a>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    <p class="font-medium text-gray-900">{{ $order->customer_name }}</p>
                                    <p class="text-xs text-gray-500">{{ $order->customer_phone }}</p>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 max-w-[220px]">
                                    <p class="truncate">{{ $order->delivery_address }}</p>
                                    <p class="text-xs text-gray-400">{{ $order->items->count() }} item(s)</p>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @if($order->delivery_latitude !== null && $order->delivery_longitude !== null)
                                        {{ round(\App\Support\Geo::haversine($storeLat, $storeLng, (float)$order->delivery_latitude, (float)$order->delivery_longitude) / 1000, 1) }} km
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">TZS {{ number_format($order->total, 0) }}</td>
                                <td class="px-6 py-4">
                                    <span class="px-2 py-1 text-xs font-semibold rounded-full bg-blue-50 text-blue-700">{{ ucfirst($order->status) }}</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center text-gray-500">No orders in this batch.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <div class="card rounded-2xl p-6">
                <h2 class="font-semibold text-gray-900 mb-4"><i class="fas fa-info-circle mr-2 text-primary-600"></i>Batch Details</h2>
                <dl class="space-y-3 text-sm">
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Target Rider</dt>
                        <dd class="font-medium text-gray-900">{{ $batch->targetRider->name ?? 'All available' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Accepted By</dt>
                        <dd>
                            @if($batch->acceptedRider)
                                <span class="font-medium text-green-700">{{ $batch->acceptedRider->name }}</span>
                                <p class="text-xs text-gray-500">{{ $batch->accepted_at ? $batch->accepted_at->format('M d, H:i') : '' }}</p>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Expires</dt>
                        <dd class="font-medium text-gray-900">{{ $batch->expires_at ? $batch->expires_at->format('M d, H:i') : '—' }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Orders</dt>
                        <dd class="font-medium text-gray-900">{{ $orders->count() }}</dd>
                    </div>
                    <div class="flex justify-between">
                        <dt class="text-gray-500">Total Value</dt>
                        <dd class="font-medium text-gray-900">TZS {{ number_format($orders->sum('total'), 0) }}</dd>
                    </div>
                    @if($batch->notes)
                    <div class="border-t border-gray-100 pt-3">
                        <dt class="text-gray-500 mb-1">Notes</dt>
                        <dd class="text-gray-700 italic">{{ $batch->notes }}</dd>
                    </div>
                    @endif
                </dl>
            </div>

            <div class="card rounded-2xl p-6">
                <h2 class="font-semibold text-gray-900 mb-4"><i class="fas fa-motorcycle mr-2 text-primary-600"></i>Rider Responses</h2>
                @forelse($batch->responses as $response)
                <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                    <div>
                        <p class="font-medium text-gray-900 text-sm">{{ $response->rider->name ?? 'Rider #'.$response->delivery_rider_id }}</p>
                        <p class="text-xs text-gray-500">{{ $response->created_at->format('M d, H:i') }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $response->response == 'accepted' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ ucfirst($response->response) }}
                    </span>
                </div>
                @empty
                <p class="text-sm text-gray-500">No rider has responded yet.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css">
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
(function () {
    const storeLat = {{ $storeLat }};
    const storeLng = {{ $storeLng }};
    const orders = @json($ordersForMap);

    const map = L.map('batch-map').setView([storeLat, storeLng], 12);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors'
    }).addTo(map);

    L.marker([storeLat, storeLng], { icon: L.divIcon({
        className: '', html: '<div style="background:#059669;border:2px solid #fff;border-radius:8px;width:34px;height:34px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:14px;box-shadow:0 2px 8px rgba(0,0,0,.3)"><i class="fas fa-store"></i></div>',
        iconSize: [34, 34], iconAnchor: [17, 34]
    }) }).addTo(map).bindTooltip('Store', { permanent: false });

    orders.forEach(o => {
        L.marker([o.lat, o.lng], { icon: L.divIcon({
            className: '', html: '<div style="background:#3b82f6;border:2px solid #fff;border-radius:50%;width:28px;height:28px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:11px;box-shadow:0 2px 6px rgba(0,0,0,.3)">'+o.id+'</div>',
            iconSize: [28, 28], iconAnchor: [14, 14]
        }) }).addTo(map)
            .bindTooltip(o.order_number + ' — ' + o.customer_name, { direction: 'top' })
            .bindPopup('<strong>' + o.order_number + '</strong><br>' + o.customer_name + '<br>' + (o.address || '') + '<br>TZS ' + o.total.toLocaleString());
    });
})();
</script>
@endsection
