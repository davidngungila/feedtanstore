@include('reports._pdf-chrome', ['title' => 'CUSTOMER PURCHASE HISTORY', 'period' => $startDate . ' to ' . $endDate . ($customerId ? ' | Customer ID: ' . $customerId : '')])

@if($sales->isEmpty())
<div class="empty">No purchases found in this period.</div>
@else
<table class="items-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Invoice #</th>
            <th>Customer</th>
            <th style="text-align: right;">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($sales as $sale)
        <tr>
            <td>{{ $sale->created_at->format('M d, Y H:i') }}</td>
            <td>{{ $sale->invoice_number ?? '#' . $sale->id }}</td>
            <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
            <td style="text-align: right;">TZS {{ number_format($sale->total, 2) }}</td>
        </tr>
        @endforeach
        <tr style="background: #f3f4f6; font-weight: bold;">
            <td colspan="3" style="text-align: right;">Total ({{ $sales->count() }} transactions)</td>
            <td style="text-align: right;">TZS {{ number_format($sales->sum('total'), 2) }}</td>
        </tr>
    </tbody>
</table>
@endif

@include('reports._pdf-foot')
