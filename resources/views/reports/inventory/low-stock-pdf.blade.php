<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Low Stock Report</title>
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
        .stats { display: flex; justify-content: flex-start; margin: 20px 0; }
        .stat-card { padding: 15px; background: #f8fafc; border-radius: 8px; }
        .stat-card h3 { margin: 0; font-size: 24px; color: #92400e; }
        .stat-card p { margin: 5px 0 0; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Low Stock Products</h1>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <div class="stats">
        <div class="stat-card">
            <h3>{{ $products->count() }}</h3>
            <p>Low Stock Items</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th>Category</th>
                <th>Brand</th>
                <th class="text-right">Current Qty</th>
                <th class="text-right">Reorder Level</th>
                <th class="text-right">Unit Cost</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
            <tr>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category ? $product->category->name : 'N/A' }}</td>
                <td>{{ $product->brand ? $product->brand->name : 'N/A' }}</td>
                <td class="text-right">{{ number_format($product->quantity) }}</td>
                <td class="text-right">{{ number_format($product->reorder_level) }}</td>
                <td class="text-right">TZS {{ number_format($product->cost_price, 2) }}</td>
            </tr>
            @endforeach
            @if($products->isEmpty())
            <tr>
                <td colspan="6" class="text-center">No low stock products found</td>
            </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
