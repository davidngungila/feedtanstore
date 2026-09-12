@extends('layouts.app')
@section('content')
<div class="p-4">
  <h1 class="text-2xl font-bold mb-4">Demand Intelligence Dashboard</h1>
  <p class="text-sm text-gray-500 mb-4">Combined: cashier requests, field sales, online searches, out-of-stock, wishlist, abandoned cart, competitor.</p>
  <div class="grid grid-cols-2 gap-4">
    <div class="card p-4 rounded-xl"><h3 class="font-bold mb-2">Most Requested Products</h3>@foreach($mostRequested as $r)<div class="flex justify-between text-sm"><span>{{ $r->product_requested }}</span><span>{{ $r->cnt }} (qty {{ $r->qty }})</span></div>@endforeach</div>
    <div class="card p-4 rounded-xl"><h3 class="font-bold mb-2">Out-of-Stock Demand</h3>@foreach($outOfStockDemand as $r)<div class="flex justify-between text-sm"><span>{{ $r->product_requested }}</span><span class="badge badge-red">{{ $r->cnt }}</span></div>@endforeach</div>
    <div class="card p-4 rounded-xl"><h3 class="font-bold mb-2">By Location</h3>@foreach($byLocation as $r)<div class="flex justify-between text-sm"><span>{{ $r->branch->name ?? 'No branch' }}</span><span>{{ $r->cnt }}</span></div>@endforeach</div>
    <div class="card p-4 rounded-xl"><h3 class="font-bold mb-2">By Month</h3>@foreach($byMonth as $r)<div class="flex justify-between text-sm"><span>{{ $r->month }}</span><span>{{ $r->cnt }}</span></div>@endforeach</div>
    <div class="card p-4 rounded-xl"><h3 class="font-bold mb-2">Trend (30 days)</h3>@foreach($trends as $r)<div class="flex justify-between text-sm"><span>{{ $r->d }}</span><span>{{ $r->cnt }}</span></div>@endforeach</div>
    <div class="card p-4 rounded-xl"><h3 class="font-bold mb-2">Wishlist Top</h3>@foreach($wishlistTop as $r)<div class="flex justify-between text-sm"><span>{{ $r->product->name ?? $r->product_id }}</span><span>{{ $r->cnt }}</span></div>@endforeach</div>
    <div class="card p-4 rounded-xl"><h3 class="font-bold mb-2">Top Searches (online)</h3>@foreach($searchTop as $r)<div class="flex justify-between text-sm"><span>{{ $r->search_term }}</span><span>{{ $r->cnt }} ({{ round($r->avg_results,1) }} results)</span></div>@endforeach</div>
    <div class="card p-4 rounded-xl"><h3 class="font-bold mb-2">Competitor Watch</h3>@foreach($competitorTop as $r)<div class="flex justify-between text-sm"><span>{{ $r->product_name }}</span><span>{{ $r->cnt }} avg diff {{ number_format($r->avg_diff) }}</span></div>@endforeach</div>
  </div>
</div>
@endsection
