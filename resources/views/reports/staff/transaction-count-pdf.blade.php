<!DOCTYPE html>
<html>
<head>
    <title>Transaction Count by Cashier Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        th { background-color: #f9fafb; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Transaction Count by Cashier Report</h1>
        <p>From {{ $startDate }} to {{ $endDate }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Cashier</th>
                <th>Email</th>
                <th class="text-right">Transactions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cashiers as $cashier)
            <tr>
                <td>{{ $cashier->name }}</td>
                <td>{{ $cashier->email }}</td>
                <td class="text-right">{{ number_format($cashier->transaction_count) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>