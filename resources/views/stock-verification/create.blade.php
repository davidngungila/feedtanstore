@extends('layouts.app')
@section('content')
<div class="p-4 max-w-5xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">Create Stock Verification Session</h1>
  <form method="POST" action="{{ route('stock-verification.store') }}" class="card p-6 rounded-xl space-y-4">
    @csrf
    <div class="grid grid-cols-2 gap-4">
      <div><label class="form-label">Title</label><input name="title" class="form-input input-field"></div>
      <div><label class="form-label">Branch</label><select name="branch_id" class="form-input input-field"><option value="">-- Select --</option>@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select></div>
      <div><label class="form-label">Location</label><select name="location_id" class="form-input input-field"><option value="">-- Select --</option>@foreach($locations as $l)<option value="{{ $l->id }}">{{ $l->name }}</option>@endforeach</select></div>
      <div><label class="form-label">Assign Stock Auditor</label><select name="assigned_auditor_id" class="form-input input-field"><option value="">-- Unassigned (draft) --</option>@foreach($auditors as $u)<option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>@endforeach</select></div>
    </div>
    <div><label class="form-label">Products to Verify (blind count)</label>
      <div class="max-h-64 overflow-y-auto border rounded p-2 grid grid-cols-2 gap-1">
        @foreach($products as $p)
        <label class="flex items-center gap-2 p-1 hover:bg-gray-50"><input type="checkbox" name="product_ids[]" value="{{ $p->id }}"> {{ $p->name }} <span class="text-xs text-gray-500">(Q{{ $p->quantity }})</span></label>
        @endforeach
      </div>
      <p class="text-xs text-gray-500 mt-1">System quantity will be hidden from auditor; variance calculated automatically after submission.</p>
    </div>
    <div><label class="form-label">Notes</label><textarea name="notes" class="form-input input-field" rows="2"></textarea></div>
    <label class="flex items-center gap-2"><input type="checkbox" name="is_monthly_audit" value="1"> Monthly External Audit</label>
    <button class="px-6 py-2 bg-primary-600 text-white rounded-lg">Create Session</button>
  </form>
</div>
@endsection
