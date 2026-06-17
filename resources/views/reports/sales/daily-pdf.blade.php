<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daily Sales Summary</title>
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
        .stat-card { padding: 15px; background: #f8fafc; border-radius: 8px; width: 23%; }
        .stat-card h3 { margin: 0; font-size: 20px; color: #1e3a8a; }
        .stat-card p { margin: 5px 0 0; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Daily Sales Summary</h1>
        <p>Date: {{ $date }}</p>
        <p>Generated on: {{ now()->format('Y-m-d H:i:s') }}</p>
    </div>

    <div class="stats">
        <div class="stat-card">
            <h3>TZS {{ number_format($totalSales, 2) }}</h3>
            <p>Total Sales</p>
        </div>
        <div class="stat-card">
            <h3>{{ $transactionCount }}</h3>
            <p>Transactions</p>
        </div>
        <div class="stat-card">
            <h3>TZS {{ number_format($averageSale, 2) }}</h3>
            <p>Average Sale</p>
        </div>
        <div class="stat-card">
            <h3>{{ $itemsSold }}</h3>
            <p>Items Sold</p>
        </div>
    </div>

    <h3>Payment Method Breakdown</h3>
    <table>
        <thead>
            <tr>
                <th>Payment Method</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Cash</td>
                <td class="text-right">TZS {{ number_format($cashTotal, 2) }}</td>
            </tr>
            <tr>
                <td>Card</td>
                <td class="text-right">TZS {{ number_format($cardTotal, 2) }}</td>
            </tr>
            <tr>
                <td>Mobile Money</td>
                <td class="text-right">TZS {{ number_format($mobileMoneyTotal, 2) }}</td>
            </tr>
            <tr>
                <td>Credit</td>
                <td class="text-right">TZS {{ number_format($creditTotal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <h3 style="margin-top: 30px;">Transactions</h3>
    <table>
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Time</th>
                <th>Customer</th>
                <th>Cashier</th>
                <th>Payment</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
            <tr>
                <td>{{ $transaction->invoice_number }}</td>
                <td>{{ $transaction->created_at->format('H:i:s') }}</td>
                <td>{{ $transaction->customer ? $transaction->customer->name : 'Walk-in' }}</td>
                <td>{{ $transaction->user ? $transaction->user->name : 'N/A' }}</td>
                <td>{{ ucwords(str_replace('_', ' ', $transaction->payment_method)) }}</td>
                <td class="text-right">TZS {{ number_format($transaction->total, 2) }}</td>
            </tr>
            @endforeach
            @if($transactions->isEmpty())
            <tr>
                <td colspan="6" class="text-center">No transactions found for this date</td>
            </tr>
            @endif
        </tbody>
    </table>
</body>
</html>
