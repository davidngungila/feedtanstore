@extends('layouts.app')
@section('content')
<div class="p-4">
  <h1 class="text-2xl font-bold mb-4">Competitor Analysis Reports</h1>
  <div class="grid grid-cols-2 gap-4">
    <div class="card p-4 rounded-xl"><h3 class="font-bold mb-2">Avg Difference by Product</h3>@foreach($avgDiff as $r)<div class="flex justify-between text-sm"><span>{{ $r->product_name }}</span><span>{{ number_format($r->avg_diff) }}</span></div>@endforeach</div>
    <div class="card p-4 rounded-xl"><h3 class="font-bold mb-2">Cheaper Competitors (they are cheaper)</h3>@foreach($cheaperCompetitors as $r)<div class="flex justify-between text-sm"><span>{{ $r->competitor_name }} - {{ $r->product_name }}</span><span class="text-red-600">{{ number_format($r->price_difference) }}</span></div>@endforeach</div>
  </div>
</div>
@endsection
