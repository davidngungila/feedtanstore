@extends('layouts.app')
@section('content')
<div class="p-4">
  <h1 class="text-2xl font-bold mb-4">Customer Store Ratings</h1>
  <div class="grid grid-cols-3 gap-4 mb-4">
    <div class="card p-4 rounded-xl text-center"><div class="text-3xl font-bold">{{ number_format($avg ?? 0,1) }} ★</div><div class="text-sm text-gray-500">Average Rating</div></div>
    <div class="card p-4 rounded-xl"><h3 class="font-bold text-sm">By Store</h3>@foreach($byStore as $r)<div class="flex justify-between text-sm"><span>{{ $r->branch->name ?? 'No branch' }}</span><span>{{ number_format($r->avg_rating,1) }} ({{ $r->cnt }})</span></div>@endforeach</div>
    <div class="card p-4 rounded-xl"><h3 class="font-bold text-sm">By Staff</h3>@foreach($byStaff as $r)<div class="flex justify-between text-sm"><span>{{ $r->staff->name ?? '-' }}</span><span>{{ number_format($r->avg_rating,1) }} ({{ $r->cnt }})</span></div>@endforeach</div>
  </div>
  <div class="card rounded-xl overflow-hidden">
    <table class="data-table">
      <thead><tr><th>Rating</th><th>Transaction</th><th>Customer</th><th>Staff</th><th>Comment</th><th>Date</th></tr></thead>
      <tbody>
        @foreach($ratings as $r)
        <tr><td>{{ str_repeat('★',$r->rating) }} ({{ $r->rating }})</td><td>{{ $r->transaction_reference ?? $r->sale_id }}</td><td>{{ $r->customer->name ?? $r->customer_name ?? '-' }}</td><td>{{ $r->staff->name ?? '-' }}</td><td>{{ $r->comment }}</td><td>{{ $r->created_at->format('Y-m-d') }}</td></tr>
        @endforeach
      </tbody>
    </table>
    <div class="p-3">{{ $ratings->links() }}</div>
  </div>
  <div class="card p-4 mt-4 rounded-xl">
    <h3 class="font-bold mb-2">Submit Rating (demo)</h3>
    <form method="POST" action="{{ route('customer-ratings.store') }}" class="grid grid-cols-3 gap-2">@csrf
      <input name="rating" type="number" min="1" max="5" placeholder="1-5 *" required class="form-input input-field">
      <input name="customer_name" placeholder="Customer" class="form-input input-field">
      <input name="comment" placeholder="Comment" class="form-input input-field col-span-2">
      <button class="bg-primary-600 text-white rounded px-4">Submit</button>
    </form>
  </div>
</div>
@endsection
