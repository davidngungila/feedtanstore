@extends('layouts.app')
@section('content')
<div class="p-4">
  <h1 class="text-2xl font-bold mb-3">Competitor Intelligence – Reports</h1>
  <div class="card rounded-xl overflow-hidden">
    <table class="data-table">
      <thead><tr><th>Competitor</th><th>Product</th><th>Comp Price</th><th>Our Price</th><th>Diff</th><th>Date</th></tr></thead>
      <tbody>
        @foreach($intels as $ci)
          <tr><td>{{ $ci->competitor_name }}</td><td>{{ $ci->product_name }}</td><td>TZS {{ number_format($ci->competitor_price) }}</td><td>TZS {{ number_format($ci->our_price) }}</td><td class="{{ $ci->price_difference>0?'text-red-600':'text-green-600' }}">{{ number_format($ci->price_difference) }}</td><td class="text-xs">{{ $ci->date_checked->format('Y-m-d') }}</td></tr>
        @endforeach
      </tbody>
    </table>
    <div class="p-3">{{ $intels->links() }}</div>
  </div>
  <a href="{{ route('competitor-intel.reports') }}" class="inline-block mt-3 text-sm text-primary-600">Full analysis →</a>
</div>
@endsection
