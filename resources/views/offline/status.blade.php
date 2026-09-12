@extends('layouts.app')
@section('content')
<div class="p-4">
  <h1 class="text-2xl font-bold mb-4">Offline Sync Status</h1>
  <div class="grid grid-cols-3 gap-4 mb-4">
    <div class="card p-4 rounded-xl text-center"><div class="text-2xl font-bold text-yellow-600">{{ $pending }}</div><div class="text-sm">Pending Sync</div></div>
    <div class="card p-4 rounded-xl text-center"><div class="text-2xl font-bold text-green-600">{{ $synced }}</div><div class="text-sm">Synced</div></div>
    <div class="card p-4 rounded-xl text-center"><div class="text-2xl font-bold text-red-600">{{ $failed }}</div><div class="text-sm">Failed Sync</div></div>
  </div>
  <div class="card p-4 rounded-xl mb-4">
    <h3 class="font-bold mb-2">Sync Actions</h3>
    <form method="POST" action="{{ route('offline.sync') }}">@csrf<button class="px-4 py-2 bg-primary-600 text-white rounded">Sync All Pending Now</button></form>
    <p class="text-xs text-gray-500 mt-2">POS stores offline transactions safely with unique transaction IDs. Duplicate synchronization is prevented via local_transaction_id. Power-loss recovery via local persistence.</p>
  </div>
  <div class="card rounded-xl overflow-hidden">
    <table class="data-table">
      <thead><tr><th>Local ID</th><th>Type</th><th>Status</th><th>Attempts</th><th>Created</th><th>Synced Sale</th><th>Error</th><th>Action</th></tr></thead>
      <tbody>
        @foreach($queue as $q)
        <tr>
          <td class="font-mono text-xs">{{ $q->local_transaction_id }}</td>
          <td>{{ $q->transaction_type }}</td>
          <td><span class="badge {{ $q->sync_status==='synced'?'badge-green':($q->sync_status==='failed'?'badge-red':'badge-yellow') }}">{{ $q->sync_status }}</span></td>
          <td>{{ $q->sync_attempts }}</td>
          <td>{{ $q->offline_created_at }}</td>
          <td>{{ $q->synced_sale_id ?? '-' }}</td>
          <td class="text-xs">{{ Str::limit($q->last_error,50) }}</td>
          <td>@if($q->sync_status==='failed')<form method="POST" action="{{ route('offline.retry',$q) }}">@csrf<button class="text-primary-600 text-xs">Retry</button></form>@endif</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
