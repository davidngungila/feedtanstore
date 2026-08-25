@include('reports._pdf-chrome', ['title' => 'STOCK OUT REPORT', 'period' => $startDate . ' to ' . $endDate])

<h4 class="sec">Adjustments (Reductions)</h4>
@if($adjustments->isEmpty())
<div class="empty">No stock reductions in this period.</div>
@else
<table class="items-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Product</th>
            <th style="text-align: right;">Qty Reduced</th>
            <th>Reason</th>
        </tr>
    </thead>
    <tbody>
        @foreach($adjustments as $adj)
        <tr>
            <td>{{ $adj->created_at->format('M d, Y H:i') }}</td>
            <td>{{ $adj->product->name ?? 'N/A' }}</td>
            <td style="text-align: right; font-weight: bold;">-{{ number_format($adj->quantity_change) }}</td>
            <td>{{ $adj->reason ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

<h4 class="sec">Customer Returns</h4>
@if($returns->isEmpty())
<div class="empty">No returns recorded in this period.</div>
@else
<table class="items-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Return #</th>
            <th>Items</th>
            <th style="text-align: right;">Total Value</th>
        </tr>
    </thead>
    <tbody>
        @foreach($returns as $return)
        @php
            $itemNames = $return->items->map(fn($i) => ($i->saleItem?->product?->name ?? 'Item') . ' (' . number_format($i->quantity) . ')')->implode(', ');
        @endphp
        <tr>
            <td>{{ $return->created_at->format('M d, Y') }}</td>
            <td>{{ $return->return_number ?? '#' . $return->id }}</td>
            <td>{{ $itemNames }}</td>
            <td style="text-align: right;">TZS {{ number_format($return->total, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@include('reports._pdf-foot')
