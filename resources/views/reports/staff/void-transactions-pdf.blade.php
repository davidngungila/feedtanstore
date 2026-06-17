<!DOCTYPE html>
<html>
<head>
    <title>Void Transactions Report</title>
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
        <h1>Void Transactions Report</h1>
        <p>From {{ $startDate }} to {{ $endDate }}</p>
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
            @foreach($cancelledSales as $cancelled)
            <tr>
                <td>{{ $cancelled->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $cancelled->invoice_number }}</td>
                <td>{{ $cancelled->user ? $cancelled->user->name : 'N/A' }}</td>
                <td>{{ $cancelled->cancellation_reason }}</td>
                <td class="text-right">TZS {{ number_format($cancelled->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>