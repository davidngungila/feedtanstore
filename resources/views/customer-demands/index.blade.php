@extends('layouts.app')
@section('content')
<div class="p-4">
  <div class="flex justify-between items-center mb-4">
    <h1 class="text-2xl font-bold">Customer Demands / Requests</h1>
    <a href="{{ route('customer-demands.create') }}" class="px-4 py-2 bg-primary-600 text-white rounded">+ Record Demand</a>
  </div>
  <form class="flex gap-2 mb-4">
    <input name="search" value="{{ request('search') }}" placeholder="Search product / customer" class="form-input input-field" style="max-width:300px">
    <select name="status" class="form-input input-field" style="max-width:150px"><option value="">All Status</option><option>new</option><option>reviewing</option><option>planned</option><option>ordered</option><option>available</option><option>closed</option></select>
    <button class="px-4 py-2 bg-gray-800 text-white rounded">Filter</button>
    <a href="{{ route('customer-demands.reports') }}" class="px-4 py-2 bg-blue-600 text-white rounded">Reports</a>
  </form>
  <div class="grid grid-cols-2 gap-4 mb-4">
    <div class="card p-3 rounded-xl"><h3 class="font-bold text-sm mb-2">Most Requested</h3>@foreach($mostRequested as $m)<div class="flex justify-between text-sm"><span>{{ $m->product_requested }}</span><span class="badge badge-blue">{{ $m->cnt }}</span></div>@endforeach</div>
    <div class="card p-3 rounded-xl"><h3 class="font-bold text-sm mb-2">Frequently Out-of-Stock</h3>@foreach($outOfStock as $m)<div class="flex justify-between text-sm"><span>{{ $m->product_requested }}</span><span class="badge badge-red">{{ $m->cnt }}</span></div>@endforeach</div>
  </div>
  <div class="card rounded-xl overflow-hidden">
    <table class="data-table">
      <thead><tr><th>Product Requested</th><th>Customer</th><th>Qty</th><th>Date</th><th>Staff</th><th>Store</th><th>OOS</th><th>Status</th><th>Action</th></tr></thead>
      <tbody>
        @foreach($demands as $d)
        <tr>
          <td>{{ $d->product_requested }}</td>
          <td>{{ $d->customer->name ?? $d->customer_name ?? '-' }}</td>
          <td>{{ $d->requested_quantity }}</td>
          <td>{{ $d->request_date->format('Y-m-d') }}</td>
          <td>{{ $d->staff->name ?? '-' }}</td>
          <td>{{ $d->branch->name ?? '-' }}</td>
          <td>{{ $d->was_out_of_stock ? 'Yes' : 'No' }}</td>
          <td><span class="badge badge-yellow">{{ $d->status }}</span></td>
          <td>
            <form method="POST" action="{{ route('customer-demands.update-status',$d) }}">@csrf @method('PUT')
              <select name="status" onchange="this.form.submit()" class="text-xs border rounded">
                @foreach(['new','reviewing','planned','ordered','available','closed'] as $st)<option value="{{ $st }}" {{ $d->status===$st?'selected':'' }}>{{ $st }}</option>@endforeach
              </select>
            </form>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <div class="p-3">{{ $demands->links() }}</div>
  </div>
</div>
@endsection
