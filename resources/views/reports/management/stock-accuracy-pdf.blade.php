@include('reports._pdf-chrome', ['title' => 'STOCK ACCURACY REPORT', 'period' => null])

@php
    $inStock = $products->where('quantity', '>', 0)->count();
    $outOfStock = $products->where('quantity', '<=', 0)->count();
@endphp

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-label">Total Products</div>
        <div class="stat-value">{{ $products->count() }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">In Stock</div>
        <div class="stat-value">{{ number_format($inStock) }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Out of Stock</div>
        <div class="stat-value">{{ number_format($outOfStock) }}</div>
    </div>
</div>

<table class="items-table">
    <thead>
        <tr>
            <th>Product</th>
            <th>Category</th>
            <th style="text-align: right;">Qty</th>
            <th style="text-align: right;">Reorder Level</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        @foreach($products as $product)
        <tr>
            <td>{{ $product->name }}</td>
            <td>{{ $product->category->name ?? '-' }}</td>
            <td style="text-align: right;">{{ number_format($product->quantity) }}</td>
            <td style="text-align: right;">{{ number_format($product->reorder_level) }}</td>
            <td>
                @if($product->quantity <= 0)
                    <span class="badge badge-red">Out of Stock</span>
                @elseif($product->quantity <= $product->reorder_level)
                    <span class="badge badge-yellow">Reorder Soon</span>
                @else
                    <span class="badge badge-green">Healthy</span>
                @endif
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

@include('reports._pdf-foot')
