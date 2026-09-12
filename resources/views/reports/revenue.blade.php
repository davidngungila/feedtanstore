@extends('layouts.app')
@section('content')
<div class="p-4">
  <h1 class="text-2xl font-bold mb-4">Revenue Analytics (Gross / Net / COGS / Profit)</h1>
  <form class="flex gap-2 mb-4">
    <input type="date" name="from" value="{{ $from }}" class="form-input input-field" style="max-width:150px">
    <input type="date" name="to" value="{{ $to }}" class="form-input input-field" style="max-width:150px">
    <select name="channel" class="form-input input-field" style="max-width:150px"><option value="all">All Channels</option><option value="in_store" {{ $channel==='in_store'?'selected':'' }}>In-store</option><option value="field_sales" {{ $channel==='field_sales'?'selected':'' }}>Field</option><option value="online" {{ $channel==='online'?'selected':'' }}>Online</option></select>
    <button class="px-4 py-2 bg-primary-600 text-white rounded">Filter</button>
  </form>
  <div class="grid grid-cols-4 gap-4 mb-4">
    <div class="card p-3 rounded-xl"><div class="text-sm text-gray-500">Gross Sales</div><div class="font-bold">TZS {{ number_format($gross) }}</div></div>
    <div class="card p-3 rounded-xl"><div class="text-sm text-gray-500">Discounts / Tax</div><div class="font-bold">{{ number_format($discounts) }} / {{ number_format($tax) }}</div></div>
    <div class="card p-3 rounded-xl"><div class="text-sm text-gray-500">COGS / Gross Profit</div><div class="font-bold">{{ number_format($cogs) }} / {{ number_format($grossProfit) }}</div></div>
    <div class="card p-3 rounded-xl"><div class="text-sm text-gray-500">Refunds / Final Revenue</div><div class="font-bold">{{ number_format($refunds) }} / {{ number_format($final) }}</div></div>
  </div>
  <div class="grid grid-cols-2 gap-4">
    <div class="card p-4 rounded-xl"><h3 class="font-bold mb-2">By Channel</h3>@foreach($byChannel as $r)<div class="flex justify-between text-sm"><span>{{ $r->sales_channel }}</span><span>{{ number_format($r->total) }} ({{ $r->cnt }} orders)</span></div>@endforeach</div>
    <div class="card p-4 rounded-xl"><h3 class="font-bold mb-2">By Day</h3>@foreach($byDay as $r)<div class="flex justify-between text-sm"><span>{{ $r->d }}</span><span>{{ number_format($r->total) }}</span></div>@endforeach</div>
    <div class="card p-4 rounded-xl"><h3 class="font-bold mb-2">By Cashier</h3>@foreach($byCashier as $r)<div class="flex justify-between text-sm"><span>{{ $r->user->name ?? '-' }}</span><span>{{ number_format($r->total) }} ({{ $r->cnt }})</span></div>@endforeach</div>
    <div class="card p-4 rounded-xl"><h3 class="font-bold mb-2">Top Products</h3>@foreach($byProduct as $r)<div class="flex justify-between text-sm"><span>#{{ $r->product_id }}</span><span>{{ number_format($r->total) }} qty {{ $r->qty }}</span></div>@endforeach</div>
  </div>
  <p class="text-xs text-gray-500 mt-4">Reports available by Day / Week / Month / Year / Store / Cashier / Product / Category / Channel.</p>
</div>
@endsection
