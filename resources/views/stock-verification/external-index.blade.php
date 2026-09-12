@extends('layouts.app')
@section('content')
<div class="p-4">
  <h1 class="text-2xl font-bold mb-4">External Audit – Read Only</h1>
  <div class="card rounded-xl overflow-hidden">
    <table class="data-table">
      <thead><tr><th>Session</th><th>Branch</th><th>Status</th><th>Variance Review</th><th>Date</th><th>View</th></tr></thead>
      <tbody>
        @foreach($sessions as $s)
        <tr><td>{{ $s->session_number }}</td><td>{{ $s->branch->name ?? '-' }}</td><td>{{ $s->status }}</td><td>{{ $s->items->sum('variance_quantity') ?? '-' }}</td><td>{{ $s->created_at->format('Y-m-d') }}</td><td><a href="{{ route('stock-verification.show',$s) }}" class="text-primary-600">View</a></td></tr>
        @endforeach
      </tbody>
    </table>
    <div class="p-3">{{ $sessions->links() }}</div>
  </div>
</div>
@endsection
