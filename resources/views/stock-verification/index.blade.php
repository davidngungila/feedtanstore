@extends('layouts.app')
@section('content')
<div class="p-4">
  <div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold text-primary-900">Stock Verification Sessions</h1>
    <a href="{{ route('stock-verification.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded-lg">Create Session</a>
  </div>
  @if(session('success'))<div class="bg-green-100 text-green-800 p-3 rounded mb-3">{{ session('success') }}</div>@endif
  @if(session('error'))<div class="bg-red-100 text-red-800 p-3 rounded mb-3">{{ session('error') }}</div>@endif
  <div class="card rounded-xl overflow-hidden">
    <table class="data-table">
      <thead><tr><th>Session #</th><th>Store/Location</th><th>Auditor</th><th>Status</th><th>Products</th><th>Created</th><th>Action</th></tr></thead>
      <tbody>
        @foreach($sessions as $s)
        <tr>
          <td class="font-mono">{{ $s->session_number }}</td>
          <td>{{ $s->branch->name ?? $s->location->name ?? '-' }}</td>
          <td>{{ $s->auditor->name ?? '-' }}</td>
          <td><span class="badge badge-blue">{{ $s->status }}</span></td>
          <td>{{ $s->total_products }} / {{ $s->counted_products }} counted</td>
          <td>{{ $s->created_at->format('Y-m-d') }}</td>
          <td><a href="{{ route('stock-verification.show',$s) }}" class="text-primary-600 hover:underline">View</a></td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <div class="p-3">{{ $sessions->links() }}</div>
  </div>
</div>
@endsection
