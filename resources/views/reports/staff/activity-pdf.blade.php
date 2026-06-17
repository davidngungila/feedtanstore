<!DOCTYPE html>
<html>
<head>
    <title>Cashier Activity Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .header { text-align: center; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #e5e7eb; padding: 8px; text-align: left; }
        th { background-color: #f9fafb; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Cashier Activity Report</h1>
        <p>From {{ $startDate }} to {{ $endDate }}</p>
    </div>
    <table>
        <thead>
            <tr>
                <th>Date & Time</th>
                <th>User</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($activities as $activity)
            <tr>
                <td>{{ $activity->created_at }}</td>
                <td>{{ $activity->user ? $activity->user->name : 'N/A' }}</td>
                <td>{{ $activity->action }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>