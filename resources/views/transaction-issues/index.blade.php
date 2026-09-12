@extends('layouts.app')
@section('content')
<div class="p-4">
  <div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Transaction Issues</h1>
    <a href="{{ route('transaction-issues.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded">Report Issue</a>
  </div>
  <form class="flex gap-2 mb-4">
    <input name="search" value="{{ request('search') }}" placeholder="Search issue #" class="form-input input-field" style="max-width:250px">
    <select name="status" class="form-input input-field" style="max-width:150px"><option value="">All</option><option>open</option><option>investigating</option><option>resolved</option><option>closed</option></select>
    <button class="px-4 py-2 bg-gray-800 text-white rounded">Filter</button>
  </form>
  <div class="card rounded-xl overflow-hidden">
    <table class="data-table">
      <thead><tr><th>Issue #</th><th>Transaction</th><th>Type</th><th>Reporter</th><th>Status</th><th>Date</th><th>Action</th></tr></thead>
      <tbody>
        @foreach($issues as $i)
        <tr><td>{{ $i->issue_number }}</td><td>{{ $i->transaction_reference ?? $i->sale_id }}</td><td>{{ $i->issue_type }}</td><td>{{ $i->reporter->name ?? '-' }}</td><td><span class="badge badge-yellow">{{ $i->status }}</span></td><td>{{ $i->created_at->format('Y-m-d H:i') }}</td><td><a href="{{ route('transaction-issues.show',$i) }}" class="text-primary-600">View</a></td></tr>
        @endforeach
      </tbody>
    </table>
    <div class="p-3">{{ $issues->links() }}</div>
  </div>
</div>
@endsection
