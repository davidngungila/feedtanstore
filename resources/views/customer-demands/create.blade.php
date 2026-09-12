@extends('layouts.app')
@section('content')
<div class="p-4 max-w-3xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">Record Customer Demand</h1>
  <p class="text-sm text-gray-500 mb-4">"What product would you like us to have next time?"</p>
  <form method="POST" action="{{ route('customer-demands.store') }}" class="card p-6 rounded-xl space-y-3">
    @csrf
    <div class="grid grid-cols-2 gap-3">
      <div><label class="form-label">Customer (optional)</label><select name="customer_id" class="form-input input-field"><option value="">Walk-in</option>@foreach($customers as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
      <div><label class="form-label">Customer Name (if walk-in)</label><input name="customer_name" class="form-input input-field"></div>
      <div><label class="form-label">Product Requested *</label><input name="product_requested" required class="form-input input-field" placeholder="e.g., Dog food 20kg"></div>
      <div><label class="form-label">Requested Qty *</label><input type="number" name="requested_quantity" value="1" min="1" class="form-input input-field" required></div>
      <div><label class="form-label">Date *</label><input type="date" name="request_date" value="{{ date('Y-m-d') }}" class="form-input input-field" required></div>
      <div><label class="form-label">Branch</label><select name="branch_id" class="form-input input-field"><option value="">--</option>@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select></div>
      <div class="col-span-2"><label class="form-label">Note</label><input name="note" class="form-input input-field" placeholder="Optional note"></div>
      <label class="flex items-center gap-2"><input type="checkbox" name="was_out_of_stock" value="1"> Was currently out of stock</label>
      <div><label class="form-label">Status</label><select name="status" class="form-input input-field"><option>new</option><option>reviewing</option><option>planned</option><option>ordered</option><option>available</option><option>closed</option></select></div>
    </div>
    <button class="px-6 py-2 bg-primary-600 text-white rounded">Save Demand</button>
  </form>
</div>
@endsection
