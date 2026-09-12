@extends('layouts.app')
@section('content')
<div class="p-4 max-w-6xl mx-auto">
  <h1 class="text-2xl font-bold">External Audit View – {{ $session->session_number }}</h1>
  <p class="text-sm text-gray-600">Opening | Purchases | Sales | Returns | Transfers | Adjustments | System Closing | Physical | Variance available in reports.</p>
  <div class="card p-4 mt-4 rounded-xl">
    <div class="grid grid-cols-3 gap-4 text-sm">
      <div>Branch: {{ $session->branch->name ?? '-' }}</div>
      <div>Location: {{ $session->location->name ?? '-' }}</div>
      <div>Status: {{ $session->status }}</div>
      <div>Auditor: {{ $session->auditor->name ?? '-' }}</div>
      <div>Audit Month: {{ $session->audit_month ?? '-' }}</div>
      <div>Submitted: {{ $session->submitted_at }}</div>
    </div>
  </div>
  <div class="card rounded-xl overflow-hidden mt-4">
    <table class="data-table">
      <thead><tr><th>Product</th><th>System</th><th>Physical</th><th>Variance</th><th>%</th><th>Type</th><th>Reason</th></tr></thead>
      <tbody>
        @foreach($session->items as $it)
        <tr><td>{{ $it->product->name }}</td><td>{{ $it->system_quantity }}</td><td>{{ $it->physical_quantity }}</td><td>{{ $it->variance_quantity }}</td><td>{{ $it->variance_percentage }}%</td><td>{{ $it->variance_type }}</td><td>{{ $it->variance_reason }}</td></tr>
        @endforeach
      </tbody>
    </table>
  </div>
  <p class="text-xs text-red-600 mt-2">Read-only: external auditors cannot modify operational transactions.</p>
</div>
@endsection
