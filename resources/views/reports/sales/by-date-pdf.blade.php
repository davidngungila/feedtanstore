<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sales by Date Report</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Sales by Date Report</h1>
        <p>Period: {{ $startDate }} - {{ $endDate }}</p>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <div class="stats">
        <div class="stat-card">
            <h3>TZS {{ number_format($sales->sum('total'), 2) }}</h3>
            <p>Total Sales</p>
        </div>
        <div class="stat-card">
            <h3>{{ $sales->sum('count') }}</h3>
            <p>Total Transactions</p>
        </div>
        <div class="stat-card">
            <h3>{{ $sales->count() }}</h3>
            <p>Days Active</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th class="text-center">Transactions</th>
                <th class="text-right">Total Sales</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
            <tr>
                <td>{{ $sale->date }}</td>
                <td class="text-center">{{ $sale->count }}</td>
                <td class="text-right">TZS {{ number_format($sale->total, 2) }}</td>
            </tr>
            @endforeach
            @if($sales->isEmpty())
            <tr>
                <td colspan="3" class="text-center">No sales data found for the selected period</td>
            </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
