<!DOCTYPE html>
<html>
<head>
    <title>Branch Profit Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 20px; }
        .stat-card { border: 1px solid #e5e7eb; padding: 15px; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        th { background-color: #f9fafb; }
        .text-right { text-align: right; }
        .text-green { color: #16a34a; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Branch Profit Report</h1>
        <p>From {{ $startDate }} to {{ $endDate }}</p>
    </div>

    <div class="stats">
        <div class="stat-card">
            <p>Total Sales</p>
            <h3>TZS {{ number_format($totalSales, 2) }}</h3>
        </div>
        <div class="stat-card">
            <p>Total COGS</p>
            <h3>TZS {{ number_format($totalCOGS, 2) }}</h3>
        </div>
        <div class="stat-card">
            <p>Gross Profit</p>
            <h3 class="text-green">TZS {{ number_format($totalGrossProfit, 2) }}</h3>
        </div>
        <div class="stat-card">
            <p>Gross Margin</p>
            <h3>{{ number_format($grossMargin, 2) }}%</h3>
        </div>
    </div>

    <h2>Monthly Profit Breakdown</h2>
    <table>
        <thead>
            <tr>
                <th>Month</th>
                <th class="text-right">Sales</th>
                <th class="text-right">COGS</th>
                <th class="text-right">Gross Profit</th>
                <th class="text-right">Margin</th>
            </tr>
        </thead>
        <tbody>
            @foreach($monthlyData as $month)
            <tr>
                <td>{{ $month['month'] }}</td>
                <td class="text-right">TZS {{ number_format($month['sales'], 2) }}</td>
                <td class="text-right">TZS {{ number_format($month['cogs'], 2) }}</td>
                <td class="text-right text-green">TZS {{ number_format($month['profit'], 2) }}</td>
                <td class="text-right">{{ number_format($month['margin'], 2) }}%</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>