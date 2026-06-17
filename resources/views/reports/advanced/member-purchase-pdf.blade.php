<!DOCTYPE html>
<html>
<head>
    <title>Member Purchase Report</title>
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
        <h1>Member Purchase Report</h1>
        <p>From {{ $startDate }} to {{ $endDate }}</p>
    </div>

    <div class="stats">
        <div class="stat-card">
            <p>Total Members</p>
            <h3>{{ $members->count() }}</h3>
        </div>
        <div class="stat-card">
            <p>Total Purchases</p>
            <h3>{{ $members->sum('purchase_count') }}</h3>
        </div>
        <div class="stat-card">
            <p>Total Spent</p>
            <h3>TZS {{ number_format($members->sum('total_spent'), 2) }}</h3>
        </div>
        <div class="stat-card">
            <p>Avg. Spent/Member</p>
            <h3>TZS {{ number_format($members->count() > 0 ? $members->sum('total_spent') / $members->count() : 0, 2) }}</h3>
        </div>
    </div>

    <h2>Member Purchases</h2>
    <table>
        <thead>
            <tr>
                <th>Member</th>
                <th>Email</th>
                <th>Phone</th>
                <th class="text-right">Purchases</th>
                <th class="text-right">Total Spent</th>
            </tr>
        </thead>
        <tbody>
            @foreach($members as $member)
            <tr>
                <td>{{ $member->name }}</td>
                <td>{{ $member->email }}</td>
                <td>{{ $member->phone }}</td>
                <td class="text-right">{{ number_format($member->purchase_count) }}</td>
                <td class="text-right text-green">TZS {{ number_format($member->total_spent, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>