@include('reports._pdf-chrome', ['title' => 'OVERSTOCK REPORT', 'period' => 'Qty > ' . $threshold])

@if($products->isEmpty())
<div class="empty">No overstocked products above {{ $threshold }} units.</div>
@else
<table class="items-table">
    <thead>
        <tr>
            <th>Product</th>
            <th>Category</th>
            <th style="text-align: right;">Qty in Stock</th>
            <th style="text-align: right;">Stock Value</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
        <tr>
            <td>{{ $product->name }}</td>
            <td>{{ $product->category->name ?? '-' }}</td>
            <td style="text-align: right; font-weight: bold;">{{ number_format($product->quantity) }}</td>
            <td style="text-align: right;">TZS {{ number_format($product->quantity * $product->cost_price, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@include('reports._pdf-foot')
