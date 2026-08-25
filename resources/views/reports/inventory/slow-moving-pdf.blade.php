@include('reports._pdf-chrome', ['title' => 'SLOW MOVING ITEMS', 'period' => 'Last ' . $days . ' days (sold <= ' . $threshold . ' units)'])

@if($products->isEmpty())
<div class="empty">No slow-moving products found.</div>
@else
<table class="items-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Product</th>
            <th>Category</th>
            <th style="text-align: right;">Qty Sold</th>
            <th style="text-align: right;">Current Stock</th>
            <th style="text-align: right;">Stock Value</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $index => $product)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $product->name }}</td>
            <td>{{ $product->category->name ?? '-' }}</td>
            <td style="text-align: right; font-weight: bold;">{{ number_format($product->total_sold) }}</td>
            <td style="text-align: right;">{{ number_format($product->quantity) }}</td>
            <td style="text-align: right;">TZS {{ number_format($product->quantity * $product->cost_price, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@include('reports._pdf-foot')
