@include('reports._pdf-chrome', ['title' => 'DEAD STOCK REPORT', 'period' => 'No sales in last ' . $days . ' days'])

@if($products->isEmpty())
<div class="empty">No dead stock found in the last {{ $days }} days.</div>
@else
<table class="items-table">
    <thead>
        <tr>
            <th>Product</th>
            <th>Category</th>
            <th style="text-align: right;">Qty in Stock</th>
            <th style="text-align: right;">Stock Value</th>
            <th style="text-align: right;">Expiry</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
        <tr>
            <td>{{ $product->name }}</td>
            <td>{{ $product->category->name ?? '-' }}</td>
            <td style="text-align: right; font-weight: bold;">{{ number_format($product->quantity) }}</td>
            <td style="text-align: right;">TZS {{ number_format($product->quantity * $product->cost_price, 2) }}</td>
            <td style="text-align: right;">{{ $product->expiry_date ? $product->expiry_date->format('M d, Y') : '-' }}</td>
        </tr>
        @endforeach
        <tr style="background: #f3f4f6; font-weight: bold;">
            <td colspan="3" style="text-align: right;">Total Dead Stock Value</td>
            <td colspan="2" style="text-align: right;">TZS {{ number_format($products->sum(fn($p) => $p->quantity * $p->cost_price), 2) }}</td>
        </tr>
    </tbody>
</table>
@endif

@include('reports._pdf-foot')
