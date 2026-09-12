@extends('layouts.app')
@section('content')
<div class="p-4">
  <h1 class="text-2xl font-bold mb-4">My Verification Sessions (Stock Auditor)</h1>
  <p class="text-sm text-gray-500 mb-4">You will only see product names and a field to enter physical count. System quantities are hidden.</p>
  <div class="card rounded-xl overflow-hidden">
    <table class="data-table">
      <thead><tr><th>Session #</th><th>Store</th><th>Status</th><th>Products</th><th>Remaining</th><th>Action</th></tr></thead>
      <tbody>
        @foreach($sessions as $s)
        <tr>
          <td>{{ $s->session_number }}</td>
          <td>{{ $s->branch->name ?? '-' }}</td>
          <td><span class="badge badge-blue">{{ $s->status }}</span></td>
          <td>{{ $s->items_count ?? $s->total_products }}</td>
          <td>{{ ($s->total_products ?? 0) - ($s->counted_products ?? 0) }}</td>
          <td><a href="{{ route('stock-verification.show',$s) }}" class="text-primary-600">Open → Count</a></td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <div class="p-3">{{ $sessions->links() }}</div>
  </div>
</div>
@endsection
