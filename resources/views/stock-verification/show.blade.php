@extends('layouts.app')
@section('content')
<div class="p-4 max-w-6xl mx-auto">
  <h1 class="text-2xl font-bold">Session {{ $session->session_number }} <span class="badge badge-blue">{{ $session->status }}</span></h1>
  <p class="text-sm text-gray-500">{{ $session->branch->name ?? '' }} {{ $session->location->name ?? '' }} | Auditor: {{ $session->auditor->name ?? 'Unassigned' }}</p>
  @if(session('success'))<div class="bg-green-100 text-green-800 p-3 rounded mt-3">{{ session('success') }}</div>@endif
  @if(session('error'))<div class="bg-red-100 text-red-800 p-3 rounded mt-3">{{ session('error') }}</div>@endif
  <div class="card rounded-xl overflow-hidden mt-4">
    <table class="data-table">
      <thead><tr><th>Product</th><th>System Qty</th><th>Physical Qty</th><th>Variance</th><th>%</th><th>Type</th><th>Reason</th></tr></thead>
      <tbody>
        @foreach($session->items as $it)
        <tr>
          <td>{{ $it->product->name }}</td>
          <td class="font-mono">{{ $it->system_quantity }}</td>
          <td class="font-mono">{{ $it->physical_quantity ?? '-' }}</td>
          <td class="{{ $it->variance_quantity <0 ? 'text-red-600':($it->variance_quantity>0?'text-green-600':'') }}">{{ $it->variance_quantity ?? '-' }}</td>
          <td>{{ $it->variance_percentage !== null ? $it->variance_percentage.'%' : '-' }}</td>
          <td><span class="badge {{ $it->variance_type==='shortage'?'badge-red':($it->variance_type==='surplus'?'badge-yellow':'badge-green') }}">{{ $it->variance_type ?? '-' }}</span></td>
          <td>{{ $it->variance_reason ?? '-' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
  @if(in_array($session->status,['submitted','under_review']))
  <div class="card p-4 mt-4 rounded-xl">
    <h3 class="font-bold mb-2">Management Review</h3>
    <form method="POST" action="{{ route('stock-verification.review',$session) }}" class="mb-3">@csrf
      <textarea name="review_notes" class="form-input input-field" rows="2" placeholder="Review notes"></textarea>
      <button class="mt-2 px-4 py-2 bg-blue-600 text-white rounded">Mark Under Review</button>
    </form>
    <form method="POST" action="{{ route('stock-verification.approve',$session) }}">@csrf
      <label class="flex items-center gap-2 mb-2"><input type="checkbox" name="create_adjustments" value="1" checked> Create stock adjustments automatically (inventory + movement)</label>
      <button class="px-4 py-2 bg-green-600 text-white rounded">Approve & Adjust Stock</button>
    </form>
    <form method="POST" action="{{ route('stock-verification.reject',$session) }}" class="mt-2">@csrf
      <input name="reason" class="form-input input-field" placeholder="Reject reason" required>
      <button class="mt-2 px-4 py-2 bg-red-600 text-white rounded">Reject</button>
    </form>
  </div>
  @endif
  <a href="{{ route('stock-verification.index') }}" class="inline-block mt-4 text-primary-600">← Back to sessions</a>
</div>
@endsection
