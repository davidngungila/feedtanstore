@include('reports._pdf-chrome', ['title' => 'STOCK TRANSFERS REPORT', 'period' => $startDate . ' to ' . $endDate])

@if($transfers->isEmpty())
<div class="empty">No transfers recorded in this period.</div>
@else
<table class="items-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Transfer #</th>
            <th>Product</th>
            <th>From</th>
            <th>To</th>
            <th style="text-align: right;">Qty</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transfers as $transfer)
        <tr>
            <td>{{ $transfer->created_at->format('M d, Y') }}</td>
            <td>{{ $transfer->transfer_number ?? '#' . $transfer->id }}</td>
            <td>{{ $transfer->product->name ?? 'N/A' }}</td>
            <td>{{ $transfer->fromLocation->name ?? '-' }}</td>
            <td>{{ $transfer->toLocation->name ?? '-' }}</td>
            <td style="text-align: right; font-weight: bold;">{{ number_format($transfer->quantity) }}</td>
            <td><span class="badge {{ $transfer->status === 'completed' ? 'badge-green' : ($transfer->status === 'canceled' ? 'badge-red' : 'badge-yellow') }}">{{ ucfirst($transfer->status) }}</span></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@include('reports._pdf-foot')
