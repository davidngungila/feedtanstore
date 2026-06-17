<!DOCTYPE html>
<html>
<head>
    <title>Discount Report</title>
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
        <h1>Discount Report</h1>
        <p>From {{ $startDate }} to {{ $endDate }}</p>
    </div>
    <div class="stats">
        <h3>Total Discounts Given: TZS {{ number_format($totalDiscounts, 2) }}</h3>
    </div>
    <table>
        <thead>
            <tr>
                <th>Date</th>
                <th>Invoice #</th>
                <th>Cashier</th>
                <th class="text-right">Discount</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sales as $sale)
            <tr>
                <td>{{ $sale->created_at->format('Y-m-d H:i') }}</td>
                <td>{{ $sale->invoice_number }}</td>
                <td>{{ $sale->user ? $sale->user->name : 'N/A' }}</td>
                <td class="text-right">TZS {{ number_format($sale->discount, 2) }}</td>
                <td class="text-right">TZS {{ number_format($sale->total, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>