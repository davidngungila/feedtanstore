@include('reports._pdf-chrome', ['title' => 'EXECUTIVE DASHBOARD REPORT', 'period' => today()->format('F Y')])

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Today's Sales</div>
        <div class="stat-value">TZS {{ number_format($todaySales, 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Today's Transactions</div>
        <div class="stat-value">{{ number_format($todayTransactions) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">This Month's Sales</div>
        <div class="stat-value">TZS {{ number_format($monthSales, 2) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Low Stock Items</div>
        <div class="stat-value">{{ number_format($lowStockCount) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Out of Stock</div>
        <div class="stat-value">{{ number_format($outOfStockCount) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Stock Value</div>
        <div class="stat-value">TZS {{ number_format($totalStockValue, 2) }}</div>
    </div>
</div>

<h4 class="sec">Top Products This Month:</h4>
<table class="items-table">
    <thead>
        <tr>
            <th>#</th>
            <th>Product</th>
            <th style="text-align: right;">Qty Sold</th>
        </tr>
    </thead>
    <tbody>
        @foreach($topProducts as $index => $product)
        <tr>
            <td>{{ $index + 1 }}</td>
            <td>{{ $product->name }}</td>
            <td style="text-align: right;">{{ number_format($product->total_qty) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<h4 class="sec">Recent Sales:</h4>
<table class="items-table">
    <thead>
        <tr>
            <th>Date</th>
            <th>Invoice #</th>
            <th>Customer</th>
            <th>Cashier</th>
            <th style="text-align: right;">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($recentSales as $sale)
        <tr>
            <td>{{ $sale->created_at->format('M d, Y H:i') }}</td>
            <td>{{ $sale->invoice_number ?? '#' . $sale->id }}</td>
            <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
            <td>{{ $sale->user->name ?? '-' }}</td>
            <td style="text-align: right;">TZS {{ number_format($sale->total, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@include('reports._pdf-foot')
