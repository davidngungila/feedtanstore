@extends('layouts.app')
@section('content')
<div class="p-4">
  <h1 class="text-2xl font-bold mb-3">Stock Verifications – Pending Review</h1>
  <div class="card rounded-xl overflow-hidden">
    <table class="data-table">
      <thead><tr><th>Session #</th><th>Branch</th><th>Auditor</th><th>Status</th><th>Products</th><th>Date</th><th>Action</th></tr></thead>
      <tbody>
        @foreach($sessions as $s)
          <tr>
            <td class="font-mono text-xs">{{ $s->session_number }}</td>
            <td>{{ $s->branch->name ?? '-' }}</td>
            <td>{{ $s->auditor->name ?? '-' }}</td>
            <td><span class="badge badge-yellow">{{ $s->status }}</span></td>
            <td>{{ $s->total_products }}</td>
            <td class="text-xs">{{ $s->created_at->format('Y-m-d') }}</td>
            <td><a href="{{ route('stock-verification.show',$s) }}" class="text-primary-600 text-sm">Review →</a></td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <div class="p-3">{{ $sessions->links() }}</div>
  </div>
</div>
@endsection
