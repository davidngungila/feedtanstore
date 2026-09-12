@extends('layouts.app')
@section('content')
<div class="p-4">
  <h1 class="text-2xl font-bold mb-4">Demand Reports</h1>
  <div class="grid grid-cols-2 gap-4">
    <div class="card p-4 rounded-xl"><h3 class="font-bold mb-2">By Store</h3>@foreach($byStore as $r)<div class="flex justify-between text-sm"><span>{{ $r->branch->name ?? 'No branch' }}</span><span>{{ $r->cnt }}</span></div>@endforeach</div>
    <div class="card p-4 rounded-xl"><h3 class="font-bold mb-2">By Month</h3>@foreach($byMonth as $r)<div class="flex justify-between text-sm"><span>{{ $r->month }}</span><span>{{ $r->cnt }}</span></div>@endforeach</div>
    <div class="card p-4 rounded-xl"><h3 class="font-bold mb-2">By Customer</h3>@foreach($byCustomer as $r)<div class="flex justify-between text-sm"><span>{{ $r->customer_name }}</span><span>{{ $r->cnt }}</span></div>@endforeach</div>
    <div class="card p-4 rounded-xl"><h3 class="font-bold mb-2">By Sales Rep</h3>@foreach($byRep as $r)<div class="flex justify-between text-sm"><span>{{ $r->staff->name ?? '-' }}</span><span>{{ $r->cnt }}</span></div>@endforeach</div>
  </div>
</div>
@endsection
