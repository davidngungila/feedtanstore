@include('reports._pdf-chrome', ['title' => 'STOCK MOVEMENT REPORT', 'period' => $startDate . ' to ' . $endDate])

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Stock In (GRN)</div>
        <div class="stat-value">{{ number_format($products->sum('qty_in')) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Sold</div>
        <div class="stat-value">{{ number_format($products->sum('qty_sold')) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Adjusted (+/-)</div>
        <div class="stat-value">{{ number_format($products->sum('qty_adjusted')) }}</div>
    </div>
</div>

@if($products->isEmpty())
<div class="empty">No stock movements recorded in this period.</div>
@else
<table class="items-table">
    <thead>
        <tr>
            <th>Product</th>
            <th style="text-align: right;">Current Stock</th>
            <th style="text-align: right;">In (GRN)</th>
            <th style="text-align: right;">Sold</th>
            <th style="text-align: right;">Adjusted</th>
            <th style="text-align: right;">Transferred</th>
            <th style="text-align: right;">Net Change</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
        <tr>
            <td>{{ $product->name }}</td>
            <td style="text-align: right;">{{ number_format($product->quantity) }}</td>
            <td style="text-align: right;">+{{ number_format($product->qty_in) }}</td>
            <td style="text-align: right;">-{{ number_format($product->qty_sold) }}</td>
            <td style="text-align: right;">{{ number_format($product->qty_adjusted) }}</td>
            <td style="text-align: right;">-{{ number_format($product->qty_transferred) }}</td>
            <td style="text-align: right; font-weight: bold;">{{ $product->net_change >= 0 ? '+' : '' }}{{ number_format($product->net_change) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@include('reports._pdf-foot')
