@include('reports._pdf-chrome', ['title' => 'USER ACTIVITY REPORT', 'period' => $startDate . ' to ' . $endDate])

@if($logs->isEmpty())
<div class="empty">No user activity found in this period.</div>
@else
<table class="items-table">
    <thead>
        <tr>
            <th>Date &amp; Time</th>
            <th>User</th>
            <th>Action</th>
            <th>Details</th>
        </tr>
    </thead>
    <tbody>
        @foreach($logs as $log)
        <tr>
            <td>{{ $log->created_at->format('M d, Y H:i:s') }}</td>
            <td>{{ $log->user->name ?? 'System' }}</td>
            <td>{{ $log->action }}</td>
            <td>{{ $log->details ?? '-' }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

@include('reports._pdf-foot')
