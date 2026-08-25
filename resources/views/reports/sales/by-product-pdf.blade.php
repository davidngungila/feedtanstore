@include('reports._pdf-chrome', ['title' => 'SALES BY PRODUCT', 'period' => $startDate . ' to ' . $endDate])

@if($products->isEmpty())
<div class="empty">No sales recorded in this period.</div>
@else
<table class="items-table">
    <thead>
        <tr>
            <th>Product</th>
            <th>SKU</th>
            <th>Category</th>
            <th style="text-align: right;">Qty Sold</th>
            <th style="text-align: right;">Total Sales</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
        <tr>
            <td>{{ $product->name }}</td>
            <td>{{ $product->sku ?? '-' }}</td>
            <td>{{ $product->category->name ?? '-' }}</td>
            <td style="text-align: right;">{{ number_format($product->total_qty) }}</td>
            <td style="text-align: right;">TZS {{ number_format($product->total_sales, 2) }}</td>
        </tr>
        @endforeach
        <tr style="background: #f3f4f6; font-weight: bold;">
            <td colspan="3" style="text-align: right;">Total</td>
            <td style="text-align: right;">{{ number_format($products->sum('total_qty')) }}</td>
            <td style="text-align: right;">TZS {{ number_format($products->sum('total_sales'), 2) }}</td>
        </tr>
    </tbody>
</table>
@endif

@include('reports._pdf-foot')
