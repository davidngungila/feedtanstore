@include('reports._pdf-chrome', ['title' => 'SALES BY CATEGORY', 'period' => $startDate . ' to ' . $endDate])

@if($categories->isEmpty())
<div class="empty">No sales recorded in this period.</div>
@else
<table class="items-table">
    <thead>
        <tr>
            <th>Category</th>
            <th style="text-align: right;">Qty Sold</th>
            <th style="text-align: right;">Total Sales</th>
        </tr>
    </thead>
    <tbody>
        @foreach($categories as $category)
        <tr>
            <td>{{ $category->name }}</td>
            <td style="text-align: right;">{{ number_format($category->total_qty) }}</td>
            <td style="text-align: right;">TZS {{ number_format($category->total_sales, 2) }}</td>
        </tr>
        @endforeach
        <tr style="background: #f3f4f6; font-weight: bold;">
            <td style="text-align: right;">Total</td>
            <td style="text-align: right;">{{ number_format($categories->sum('total_qty')) }}</td>
            <td style="text-align: right;">TZS {{ number_format($categories->sum('total_sales'), 2) }}</td>
        </tr>
    </tbody>
</table>
@endif

@include('reports._pdf-foot')
