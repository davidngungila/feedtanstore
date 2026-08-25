@include('reports._pdf-chrome', ['title' => 'SALES BY BRAND', 'period' => $startDate . ' to ' . $endDate])

@if($brands->isEmpty())
<div class="empty">No sales recorded in this period.</div>
@else
<table class="items-table">
    <thead>
        <tr>
            <th>Brand</th>
            <th style="text-align: right;">Qty Sold</th>
            <th style="text-align: right;">Total Sales</th>
        </tr>
    </thead>
    <tbody>
        @foreach($brands as $brand)
        <tr>
            <td>{{ $brand->name }}</td>
            <td style="text-align: right;">{{ number_format($brand->total_qty) }}</td>
            <td style="text-align: right;">TZS {{ number_format($brand->total_sales, 2) }}</td>
        </tr>
        @endforeach
        <tr style="background: #f3f4f6; font-weight: bold;">
            <td style="text-align: right;">Total</td>
            <td style="text-align: right;">{{ number_format($brands->sum('total_qty')) }}</td>
            <td style="text-align: right;">TZS {{ number_format($brands->sum('total_sales'), 2) }}</td>
        </tr>
    </tbody>
</table>
@endif

@include('reports._pdf-foot')
