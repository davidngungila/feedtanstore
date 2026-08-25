@include('reports._pdf-chrome', ['title' => 'INVENTORY VALUATION', 'period' => null])

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Products</div>
        <div class="stat-value">{{ $products->count() }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Cost Value</div>
        <div class="stat-value">TZS {{ number_format($totalCostValue, 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Retail Value</div>
        <div class="stat-value">TZS {{ number_format($totalRetailValue, 2) }}</div>
    </div>
</div>

<table class="items-table">
    <thead>
        <tr>
            <th>Product</th>
            <th>Category</th>
            <th style="text-align: right;">Qty</th>
            <th style="text-align: right;">Cost Price</th>
            <th style="text-align: right;">Cost Value</th>
            <th style="text-align: right;">Selling Price</th>
            <th style="text-align: right;">Retail Value</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
        <tr>
            <td>{{ $product->name }}</td>
            <td>{{ $product->category->name ?? '-' }}</td>
            <td style="text-align: right;">{{ number_format($product->quantity) }}</td>
            <td style="text-align: right;">TZS {{ number_format($product->cost_price, 2) }}</td>
            <td style="text-align: right;">TZS {{ number_format($product->quantity * $product->cost_price, 2) }}</td>
            <td style="text-align: right;">TZS {{ number_format($product->selling_price, 2) }}</td>
            <td style="text-align: right;">TZS {{ number_format($product->quantity * $product->selling_price, 2) }}</td>
        </tr>
        @endforeach
        <tr style="background: #f3f4f6; font-weight: bold;">
            <td colspan="4" style="text-align: right;">Total</td>
            <td style="text-align: right;">TZS {{ number_format($totalCostValue, 2) }}</td>
            <td></td>
            <td style="text-align: right;">TZS {{ number_format($totalRetailValue, 2) }}</td>
        </tr>
    </tbody>
</table>

@include('reports._pdf-foot')
