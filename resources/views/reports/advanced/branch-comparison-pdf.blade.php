<!DOCTYPE html>
<html>
<head>
    <title>Branch Comparison Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 20px; }
        .stat-card { border: 1px solid #e5e7eb; padding: 15px; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        th { background-color: #f9fafb; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Branch Comparison Report</h1>
        <p>From {{ $startDate }} to {{ $endDate }}</p>
    </div>

    <div class="stats">
        <div class="stat-card">
            <p>Total Sales</p>
            <h3>TZS {{ number_format($totalSales, 2) }}</h3>
        </div>
        <div class="stat-card">
            <p>Transactions</p>
            <h3>{{ number_format($totalTransactions) }}</h3>
        </div>
        <div class="stat-card">
            <p>Average Sale</p>
            <h3>TZS {{ number_format($totalTransactions > 0 ? $totalSales / $totalTransactions : 0, 2) }}</h3>
        </div>
        <div class="stat-card">
            <p>Items Sold</p>
            <h3>{{ number_format($totalItems) }}</h3>
        </div>
    </div>

    <h2>Payment Methods</h2>
    <table>
        <thead>
            <tr>
                <th>Payment Method</th>
                <th class="text-right">Total</th>
                <th class="text-right">Transactions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($paymentMethods as $method)
            <tr>
                <td>{{ ucfirst($method->payment_method) }}</td>
                <td class="text-right">TZS {{ number_format($method->total, 2) }}</td>
                <td class="text-right">{{ $method->count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Daily Performance</h2>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th class="text-right">Sales</th>
                <th class="text-right">Transactions</th>
                <th class="text-right">Items Sold</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dailySales as $day)
            <tr>
                <td>{{ $day['date'] }}</td>
                <td class="text-right">TZS {{ number_format($day['sales'], 2) }}</td>
                <td class="text-right">{{ $day['transactions'] }}</td>
                <td class="text-right">{{ $day['items'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>