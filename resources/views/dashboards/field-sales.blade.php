@extends('layouts.app')
@section('content')
<div class="p-4">
  <h1 class="text-2xl font-bold mb-4">Field Sales Dashboard</h1>
  <div class="grid grid-cols-2 gap-4 mb-4">
    <div class="card p-4 rounded-xl"><h3 class="font-bold mb-2">My Sales</h3>@forelse($mySales as $s)<div class="text-sm flex justify-between"><span>{{ $s->invoice_number }}</span><span>TZS {{ number_format($s->total) }}</span></div>@empty<p class="text-sm text-gray-500">No sales</p>@endforelse</div>
    <div class="card p-4 rounded-xl"><h3 class="font-bold mb-2">My Orders</h3>@forelse($myOrders as $o)<div class="text-sm flex justify-between"><span>{{ $o->order_number }}</span><span class="badge badge-blue">{{ $o->status }}</span></div>@empty<p class="text-sm text-gray-500">No orders</p>@endforelse</div>
  </div>
  <div class="card p-4 rounded-xl">
    <h3 class="font-bold mb-2">Quick Actions (Mobile App)</h3>
    <p class="text-sm text-gray-500">Field staff can via API: login, view products, check availability, register customers, create sales, capture demand, competitor prices, submit orders, view performance.</p>
    <div class="flex gap-2 mt-2">
      <a href="{{ route('customer-demands.index') }}" class="px-3 py-1 bg-blue-600 text-white rounded text-sm">Demands</a>
      <a href="{{ route('competitor-intel.index') }}" class="px-3 py-1 bg-yellow-600 text-white rounded text-sm">Competitor</a>
    </div>
  </div>
</div>
@endsection
