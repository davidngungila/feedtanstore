@include('reports._pdf-chrome', ['title' => 'STOCK IN REPORT', 'period' => $startDate . ' to ' . $endDate])

@if($grns->isEmpty())
<div class="empty">No goods received in this period.</div>
@else
<table class="items-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>GRN #</th>
            <th>Supplier</th>
            <th>Products</th>
            <th style="text-align: right;">Total Qty</th>
        </tr>
    </thead>
    <tbody>
        @foreach($grns as $grn)
        @php
            $totalQty = $grn->items->sum('quantity');
            $itemNames = $grn->items->map(fn($i) => ($i->product->name ?? 'Product') . ' (' . number_format($i->quantity) . ')')->implode(', ');
        @endphp
        <tr>
            <td>{{ $grn->created_at->format('M d, Y') }}</td>
            <td>{{ $grn->grn_number ?? '#' . $grn->id }}</td>
            <td>{{ $grn->supplier->name ?? 'N/A' }}</td>
            <td>{{ $itemNames }}</td>
            <td style="text-align: right; font-weight: bold;">{{ number_format($totalQty) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@include('reports._pdf-foot')
