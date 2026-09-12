@extends('layouts.app')
@section('content')
<div class="p-4">
  <h1 class="text-2xl font-bold mb-3">Customer Demands – Supervisor Review</h1>
  <div class="card rounded-xl overflow-hidden">
    <table class="data-table">
      <thead><tr><th>Product</th><th>Customer</th><th>Qty</th><th>OOS</th><th>Staff</th><th>Status</th><th>Update</th></tr></thead>
      <tbody>
        @foreach($demands as $d)
          <tr>
            <td>{{ $d->product_requested }}</td>
            <td>{{ $d->customer->name ?? $d->customer_name ?? '-' }}</td>
            <td>{{ $d->requested_quantity }}</td>
            <td>{{ $d->was_out_of_stock?'Yes':'No' }}</td>
            <td>{{ $d->staff->name ?? '-' }}</td>
            <td><span class="badge badge-blue">{{ $d->status }}</span></td>
            <td>
              <form method="POST" action="{{ route('store-supervisor.demands.update',$d) }}">@csrf @method('PUT')
                <select name="status" class="text-xs border rounded p-1">
                  @foreach(['new','reviewing','planned','ordered','available','closed'] as $s)<option value="{{ $s }}" {{ $d->status===$s?'selected':'' }}>{{ $s }}</option>@endforeach
                </select>
                <button class="px-2 py-1 bg-primary-600 text-white rounded text-xs">Save</button>
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
