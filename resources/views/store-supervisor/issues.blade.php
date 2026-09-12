@extends('layouts.app')
@section('page-title','Supervisor – Issues')
@section('content')
<div class="p-4">
  <h1 class="text-2xl font-bold mb-3">Transaction Issues – Supervisor Inbox</h1>
  <p class="text-xs text-gray-500 mb-3">Manages most operational issues. Workflow: Open → Investigating → Resolved → Closed. Audit trail logged.</p>
  <form method="GET" class="flex gap-2 mb-4">
    <select name="status" class="form-input input-field" style="max-width:150px"><option value="">All status</option><option value="open" {{ request('status')==='open'?'selected':'' }}>Open</option><option value="investigating" {{ request('status')==='investigating'?'selected':'' }}>Investigating</option><option value="resolved" {{ request('status')==='resolved'?'selected':'' }}>Resolved</option><option value="closed" {{ request('status')==='closed'?'selected':'' }}>Closed</option></select>
    <input name="search" value="{{ request('search') }}" placeholder="Search issue / description" class="form-input input-field" style="max-width:250px">
    <button class="px-4 py-2 bg-primary-600 text-white rounded">Filter</button>
  </form>
  @if(session('success'))<div class="bg-green-100 text-green-800 p-2 rounded mb-3">{{ session('success') }}</div>@endif
  @if(session('error'))<div class="bg-red-100 text-red-800 p-2 rounded mb-3">{{ session('error') }}</div>@endif
  <div class="card rounded-xl overflow-hidden">
    <table class="data-table">
      <thead><tr><th>Issue #</th><th>Type</th><th>Transaction</th><th>Reporter</th><th>Status</th><th>Assigned</th><th>Action</th></tr></thead>
      <tbody>
        @foreach($issues as $i)
          <tr>
            <td class="font-mono text-xs">{{ $i->issue_number }}</td>
            <td>{{ $i->issue_type }}</td>
            <td class="text-xs">{{ $i->transaction_reference ?? $i->sale_id ?? $i->online_order_id ?? '-' }}</td>
            <td class="text-xs">{{ $i->reporter->name ?? '-' }}</td>
            <td><span class="badge badge-yellow">{{ $i->status }}</span></td>
            <td class="text-xs">{{ $i->assignee->name ?? '-' }}</td>
            <td>
              <form method="POST" action="{{ route('store-supervisor.issues.update',$i) }}" class="flex gap-1 items-center">
                @csrf @method('PUT')
                <select name="status" class="text-xs border rounded p-1">
                  <option value="open" {{ $i->status==='open'?'selected':'' }}>Open</option>
                  <option value="investigating" {{ $i->status==='investigating'?'selected':'' }}>Investigating</option>
                  <option value="resolved" {{ $i->status==='resolved'?'selected':'' }}>Resolved</option>
                  <option value="closed" {{ $i->status==='closed'?'selected':'' }}>Closed</option>
                </select>
                <select name="assigned_to" class="text-xs border rounded p-1"><option value="">Assign</option>@foreach($users as $u)<option value="{{ $u->id }}" {{ (int)$i->assigned_to===(int)$u->id?'selected':'' }}>{{ $u->name }}</option>@endforeach</select>
                <button class="px-2 py-1 bg-primary-600 text-white rounded text-xs">Save</button>
              </form>
              <a href="{{ route('transaction-issues.show',$i) }}" class="text-xs text-blue-600">Detail</a>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
    <div class="p-3">{{ $issues->links() }}</div>
  </div>
</div>
@endsection
