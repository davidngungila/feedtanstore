@extends('layouts.app')
@section('content')
<div class="p-4">
  <h1 class="text-2xl font-bold mb-4">Competitor Intelligence</h1>
  <div class="card p-4 rounded-xl mb-4">
    <h3 class="font-bold mb-2">Record Competitor Price</h3>
    <form method="POST" action="{{ route('competitor-intel.store') }}" enctype="multipart/form-data" class="grid grid-cols-3 gap-3">
      @csrf
      <input name="competitor_name" placeholder="Competitor name *" required class="form-input input-field">
      <input name="competitor_location" placeholder="Location" class="form-input input-field">
      <input name="product_name" placeholder="Product *" required class="form-input input-field">
      <input name="competitor_price" type="number" step="0.01" placeholder="Competitor Price *" required class="form-input input-field">
      <input name="our_price" type="number" step="0.01" placeholder="Our Price *" required class="form-input input-field">
      <select name="product_id" class="form-input input-field"><option value="">Select Product (optional)</option>@foreach($products as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach</select>
      <select name="availability" class="form-input input-field"><option value="">Availability</option><option>in_stock</option><option>out_of_stock</option><option>limited</option></select>
      <input type="date" name="date_checked" value="{{ date('Y-m-d') }}" class="form-input input-field">
      <select name="branch_id" class="form-input input-field"><option value="">Branch</option>@foreach($branches as $b)<option value="{{ $b->id }}">{{ $b->name }}</option>@endforeach</select>
      <input type="file" name="photo" class="form-input input-field">
      <input name="notes" placeholder="Notes" class="form-input input-field col-span-2">
      <button class="bg-primary-600 text-white rounded px-4 py-2">Save</button>
    </form>
  </div>
  <div class="card rounded-xl overflow-hidden">
    <table class="data-table">
      <thead><tr><th>Competitor</th><th>Product</th><th>Competitor Price</th><th>Our Price</th><th>Difference</th><th>Availability</th><th>Date</th><th>Rep</th></tr></thead>
      <tbody>
        @foreach($entries as $e)
        <tr>
          <td>{{ $e->competitor_name }}<div class="text-xs text-gray-500">{{ $e->competitor_location }}</div></td>
          <td>{{ $e->product_name }}</td>
          <td>TZS {{ number_format($e->competitor_price) }}</td>
          <td>TZS {{ number_format($e->our_price) }}</td>
          <td class="{{ $e->price_difference>0?'text-red-600':'text-green-600' }}">TZS {{ number_format($e->price_difference) }} ({{ $e->price_difference_percent }}%)</td>
          <td>{{ $e->availability }}</td>
          <td>{{ $e->date_checked->format('Y-m-d') }}</td>
          <td>{{ $e->salesRep->name ?? '-' }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
    <div class="p-3">{{ $entries->links() }}</div>
  </div>
  <a href="{{ route('competitor-intel.reports') }}" class="inline-block mt-3 px-4 py-2 bg-blue-600 text-white rounded">View Analysis Reports</a>
</div>
@endsection
