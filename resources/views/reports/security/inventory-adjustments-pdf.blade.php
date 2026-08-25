@include('reports._pdf-chrome', ['title' => 'INVENTORY ADJUSTMENTS REPORT', 'period' => $startDate . ' to ' . $endDate])

@if($adjustments->isEmpty())
<div class="empty">No stock adjustments recorded in this period.</div>
@else
<table class="items-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Product</th>
            <th>Type</th>
            <th style="text-align: right;">Qty Change</th>
            <th>Reason</th>
        </tr>
    </thead>
    <tbody>
        @foreach($adjustments as $adjustment)
        <tr>
            <td>{{ $adjustment->created_at->format('M d, Y H:i') }}</td>
            <td>{{ $adjustment->product->name ?? 'N/A' }}</td>
            <td><span class="badge {{ $adjustment->type === 'addition' ? 'badge-green' : 'badge-red' }}">{{ ucfirst($adjustment->type) }}</span></td>
            <td style="text-align: right; font-weight: bold;">{{ $adjustment->type === 'addition' ? '+' : '-' }}{{ number_format($adjustment->quantity_change) }}</td>
            <td>{{ $adjustment->reason ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@include('reports._pdf-foot')
