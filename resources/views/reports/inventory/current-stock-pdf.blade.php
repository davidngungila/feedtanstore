<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Current Stock Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; color: #1e3a8a; }
        .header p { color: #666; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: #f5f5f5; font-weight: bold; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .stats { display: flex; justify-content: space-between; margin: 20px 0; }
        .stat-card { padding: 15px; background: #f8fafc; border-radius: 8px; width: 30%; }
        .stat-card h3 { margin: 0; font-size: 24px; color: #1e3a8a; }
        .stat-card p { margin: 5px 0 0; color: #666; }
        .badge { padding: 4px 8px; border-radius: 12px; font-size: 11px; font-weight: bold; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        .badge-yellow { background: #fef3c7; color: #92400e; }
        .badge-green { background: #d1fae5; color: #065f46; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Current Stock Report</h1>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <div class="stats">
        <div class="stat-card">
            <h3>{{ $products->count() }}</h3>
            <p>Total Products</p>
        </div>
        <div class="stat-card">
            <h3>TZS {{ number_format($totalStockValue, 2) }}</h3>
            <p>Total Stock Value</p>
        </div>
        <div class="stat-card">
            <h3>{{ $products->filter(fn($p) => $p->quantity <= $p->reorder_level)->count() }}</h3>
            <p>Low Stock Items</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Brand</th>
                <th class="text-right">Quantity</th>
                <th class="text-right">Cost Price</th>
                <th class="text-right">Selling Price</th>
                <th class="text-right">Stock Value</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category ? $product->category->name : 'N/A' }}</td>
                <td>{{ $product->brand ? $product->brand->name : 'N/A' }}</td>
                <td class="text-right">{{ number_format($product->quantity) }}</td>
                <td class="text-right">TZS {{ number_format($product->cost_price, 2) }}</td>
                <td class="text-right">TZS {{ number_format($product->selling_price, 2) }}</td>
                <td class="text-right">TZS {{ number_format($product->quantity * $product->cost_price, 2) }}</td>
                <td>
                    @if($product->quantity == 0)
                        <span class="badge badge-red">Out of Stock</span>
                    @elseif($product->quantity <= $product->reorder_level)
                        <span class="badge badge-yellow">Low Stock</span>
                    @else
                        <span class="badge badge-green">In Stock</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
