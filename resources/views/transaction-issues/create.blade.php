@extends('layouts.app')
@section('content')
<div class="p-4 max-w-2xl mx-auto">
  <h1 class="text-2xl font-bold mb-4">Report Transaction Issue</h1>
  <form method="POST" action="{{ route('transaction-issues.store') }}" enctype="multipart/form-data" class="card p-6 rounded-xl space-y-3">
    @csrf
    <div><label class="form-label">Sale ID (optional)</label><input name="sale_id" value="{{ $sale->id ?? '' }}" class="form-input input-field"></div>
    <div><label class="form-label">Issue Type *</label><select name="issue_type" required class="form-input input-field"><option>wrong_quantity</option><option>wrong_product</option><option>wrong_price</option><option>payment_problem</option><option>duplicate_transaction</option><option>customer_complaint</option><option>product_damaged</option><option>failed_transaction</option><option>receipt_problem</option><option>other</option></select></div>
    <div><label class="form-label">Description *</label><textarea name="description" required class="form-input input-field" rows="3"></textarea></div>
    <div><label class="form-label">Attachment/Photo</label><input type="file" name="attachment" class="form-input input-field"></div>
    <div><label class="form-label">Assign To (optional)</label><input name="assigned_to" class="form-input input-field" placeholder="User ID"></div>
    <button class="px-6 py-2 bg-primary-600 text-white rounded">Submit Issue</button>
  </form>
</div>
@endsection
