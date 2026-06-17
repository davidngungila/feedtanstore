<!DOCTYPE html>
<html>
<head>
    <title>Supplier Credit Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-bottom: 20px; }
        .stat-card { border: 1px solid #e5e7eb; padding: 15px; border-radius: 8px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        th { background-color: #f9fafb; }
        .text-right { text-align: right; }
        .text-orange { color: #ea580c; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Supplier Credit Report</h1>
    </div>

    <div class="stats">
        <div class="stat-card">
            <p>Total Suppliers</p>
            <h3>{{ $suppliers->count() }}</h3>
        </div>
        <div class="stat-card">
            <p>Total Credit</p>
            <h3>TZS {{ number_format($totalCredit, 2) }}</h3>
        </div>
        @php
            $suppliersWithCredit = $suppliers->filter(function($s) { return $s->purchaseOrders->sum('total') > 0; });
        @endphp
        <div class="stat-card">
            <p>Suppliers with Credit</p>
            <h3>{{ $suppliersWithCredit->count() }}</h3>
        </div>
        <div class="stat-card">
            <p>Avg. Credit/Supplier</p>
            <h3>TZS {{ number_format($suppliers->count() > 0 ? $totalCredit / $suppliers->count() : 0, 2) }}</h3>
        </div>
    </div>

    <h2>Supplier Credit Details</h2>
    <table>
        <thead>
            <tr>
                <th>Supplier</th>
                <th>Email</th>
                <th>Phone</th>
                <th class="text-right">Outstanding Credit</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suppliers as $supplier)
            @php
                $credit = $supplier->purchaseOrders->sum('total');
            @endphp
            <tr>
                <td>{{ $supplier->name }}</td>
                <td>{{ $supplier->email }}</td>
                <td>{{ $supplier->phone }}</td>
                <td class="text-right {{ $credit > 0 ? 'text-orange' : 'text-gray-600' }}">TZS {{ number_format($credit, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>