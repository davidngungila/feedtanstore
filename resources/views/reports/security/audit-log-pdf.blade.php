@include('reports._pdf-chrome', ['title' => 'SYSTEM AUDIT LOG', 'period' => $startDate . ' to ' . $endDate])

@if($logs->isEmpty())
<div class="empty">No activity logged in this period.</div>
@else
<table class="items-table">
    <thead>
        <tr>
            <th>Date &amp; Time</th>
            <th>User</th>
            <th>Action</th>
            <th>Details</th>
            <th>IP Address</th>
        </tr>
    </thead>
    <tbody>
        @foreach($logs as $log)
        <tr>
            <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
            <td>{{ $log->user->name ?? 'System' }}</td>
            <td>{{ $log->action }}</td>
            <td>{{ $log->details ?? '-' }}</td>
            <td>{{ $log->ip_address ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@include('reports._pdf-foot')
