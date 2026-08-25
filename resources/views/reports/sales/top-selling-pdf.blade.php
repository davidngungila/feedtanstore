@include('reports._pdf-chrome', ['title' => 'TOP SELLING PRODUCTS', 'period' => $startDate . ' to ' . $endDate])

@if($products->isEmpty())
<div class="empty">No sales recorded in this period.</div>
@else
<table class="items-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Product</th>
            <th>Category</th>
            <th style="text-align: right;">Qty Sold</th>
            <th style="text-align: right;">Total Sales</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $index => $product)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $product->name }}</td>
            <td>{{ $product->category->name ?? '-' }}</td>
            <td style="text-align: right;">{{ number_format($product->total_qty) }}</td>
            <td style="text-align: right;">TZS {{ number_format($product->total_sales, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@include('reports._pdf-foot')
