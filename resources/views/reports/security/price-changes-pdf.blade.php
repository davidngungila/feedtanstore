@include('reports._pdf-chrome', ['title' => 'PRICE CHANGES REPORT', 'period' => $startDate . ' to ' . $endDate])

@if($changes->isEmpty())
<div class="empty">No price changes recorded in this period.</div>
@else
<table class="items-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Product</th>
            <th style="text-align: right;">Old Price</th>
            <th style="text-align: right;">New Price</th>
            <th>Requested By</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($changes as $change)
        <tr>
            <td>{{ $change->created_at->format('M d, Y') }}</td>
            <td>{{ $change->product->name ?? 'N/A' }}</td>
            <td style="text-align: right;">TZS {{ number_format($change->current_price, 2) }}</td>
            <td style="text-align: right; font-weight: bold;">TZS {{ number_format($change->proposed_price, 2) }}</td>
            <td>{{ $change->requester->name ?? '-' }}</td>
            <td><span class="badge {{ $change->status === 'approved' ? 'badge-green' : ($change->status === 'rejected' ? 'badge-red' : 'badge-yellow') }}">{{ ucfirst($change->status) }}</span></td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@include('reports._pdf-foot')
