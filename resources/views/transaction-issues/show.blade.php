@extends('layouts.app')
@section('content')
<div class="p-4 max-w-3xl mx-auto">
  <h1 class="text-2xl font-bold">{{ $issue->issue_number }} <span class="badge badge-blue">{{ $issue->status }}</span></h1>
  <p class="text-sm text-gray-500">Reported by {{ $issue->reporter->name }} on {{ $issue->reported_at }} | Type: {{ $issue->issue_type }}</p>
  <div class="card p-4 mt-4 rounded-xl">
    <p><strong>Description:</strong> {{ $issue->description }}</p>
    @if($issue->attachment)<p><a href="{{ asset('storage/'.$issue->attachment) }}" target="_blank" class="text-primary-600">View Attachment</a></p>@endif
    <p class="mt-2"><strong>Transaction:</strong> {{ $issue->transaction_reference }} ({{ $issue->transaction_type }})</p>
    <p><strong>Assigned To:</strong> {{ $issue->assignee->name ?? '-' }}</p>
    <p><strong>Resolution:</strong> {{ $issue->resolution ?? '-' }}</p>
    @if($issue->resolved_by)<p><strong>Resolved By:</strong> {{ $issue->resolver->name }} at {{ $issue->resolved_at }}</p>@endif
  </div>
  <div class="card p-4 mt-4 rounded-xl">
    <h3 class="font-bold mb-2">Update Issue (Open → Investigating → Resolved → Closed)</h3>
    <form method="POST" action="{{ route('transaction-issues.update',$issue) }}">@csrf @method('PUT')
      <select name="status" class="form-input input-field mb-2"><option>open</option><option>investigating</option><option>resolved</option><option>closed</option></select>
      <input name="assigned_to" placeholder="Assign to user ID" class="form-input input-field mb-2">
      <textarea name="resolution" placeholder="Resolution" class="form-input input-field mb-2" rows="2"></textarea>
      <button class="px-4 py-2 bg-primary-600 text-white rounded">Update</button>
    </form>
  </div>
  @if(session('success'))<div class="bg-green-100 text-green-800 p-3 rounded mt-3">{{ session('success') }}</div>@endif
  @if(session('error'))<div class="bg-red-100 text-red-800 p-3 rounded mt-3">{{ session('error') }}</div>@endif
</div>
@endsection
