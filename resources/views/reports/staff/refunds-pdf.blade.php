<!DOCTYPE html>
<html>
<head>
    <title>Refund Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .stats { margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        th { background-color: #f9fafb; }
        .text-right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Refund Report</h1>
        <p>From {{ $startDate }} to {{ $endDate }}</p>
    </div>
    <div class="stats">
        <h3>Total Refunded: TZS {{ number_format($totalRefunded, 2) }}</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Invoice #</th>
                <th>Cashier</th>
                <th>Reason</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($returns as $return)
            <tr>
                <td>{{ $return->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $return->sale ? $return->sale->invoice_number : 'N/A' }}</td>
                <td>{{ $return->user ? $return->user->name : 'N/A' }}</td>
                <td>{{ $return->reason }}</td>
                <td class="text-right">TZS {{ number_format($return->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>