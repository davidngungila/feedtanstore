@extends('layouts.app')
@section('content')
<div class="p-4 space-y-4">
  <h1 class="text-2xl font-bold">Field Sales Dashboard</h1>

  @if(session('success'))<div class="bg-green-100 text-green-800 p-3 rounded">{{ session('success') }}</div>@endif
  @if(session('error'))<div class="bg-red-100 text-red-800 p-3 rounded">{{ session('error') }}</div>@endif

  <!-- Driver / Reference Code for Today's Sales -->
  <div class="card p-4 rounded-xl border-2 border-primary-200 bg-primary-50">
    <h3 class="font-bold text-primary-900 mb-2"><i class="fas fa-id-card mr-2"></i>Today's Driver / Reference Code</h3>
    <p class="text-xs text-gray-600 mb-3">Enter the <strong>driver code</strong> (e.g., rider vehicle plate, staff code, or route code) to use as reference for all sales you make <strong>today</strong>. Every sale/order created today will be tagged with this code and you can filter today's report by it.</p>
    <form method="POST" action="{{ route('field-sales.driver-code.store') }}" class="flex flex-wrap gap-2 items-end">
      @csrf
      <div class="flex-1 min-w-[220px]">
        <label class="form-label">Driver Code *</label>
        <input type="text" name="driver_code" value="{{ $driverCode ?? $todayDriver->driver_code ?? '' }}" required placeholder="e.g., DRV-001 / T123ABC / REF-DAVID2026" class="form-input input-field font-mono">
      </div>
      <div>
        <label class="form-label">Date</label>
        <input type="date" name="sales_date" value="{{ today()->toDateString() }}" class="form-input input-field">
      </div>
      <button class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium">Save Driver Code</button>
      @if($driverCode)
        <a href="{{ route('field-sales.dashboard') }}" class="px-4 py-2 bg-gray-200 rounded-lg text-sm">Clear Filter</a>
      @endif
    </form>
    @if($todayDriver)
      <div class="mt-3 p-2 bg-white rounded border text-sm">Active today: <span class="font-mono font-bold text-primary-700">{{ $todayDriver->driver_code }}</span></div>
    @endif
  </div>

  <!-- Today's Sales Summary by Driver Code -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
    <div class="card p-4 rounded-xl text-center">
      <div class="text-sm text-gray-500">Today's Sales @if($driverCode) <span class="font-mono">({{ $driverCode }})</span> @endif</div>
      <div class="text-3xl font-bold text-primary-700">{{ $todaySummary['count'] }}</div>
      <div class="text-sm text-gray-600">TZS {{ number_format($todaySummary['total']) }}</div>
      <div class="text-xs text-gray-400 mt-1">{{ today()->format('Y-m-d') }}</div>
    </div>
    <div class="card p-4 rounded-xl">
      <h3 class="font-bold mb-2">Sales by Driver Code (Today)</h3>
      @forelse($byDriver as $row)
        <div class="flex justify-between items-center p-1 border-b text-sm">
          <span class="font-mono">{{ $row->driver_code ?? '— no code —' }}</span>
          <span class="badge badge-blue">{{ $row->cnt }} sales</span>
          <span class="font-semibold">TZS {{ number_format($row->total) }}</span>
          <a href="{{ route('field-sales.dashboard',['driver_code'=>$row->driver_code]) }}" class="text-xs text-primary-600 hover:underline">Filter</a>
        </div>
      @empty <p class="text-sm text-gray-500">No sales today yet</p> @endforelse
    </div>
    <div class="card p-4 rounded-xl">
      <h3 class="font-bold mb-2">Quick Actions (Mobile App)</h3>
      <p class="text-xs text-gray-500 mb-2">Field staff via API: login, view products, check availability, register customers, create sales (with <code>driver_code</code>), capture demand, competitor prices, submit orders, view performance.</p>
      <div class="text-xs font-mono bg-gray-50 p-2 rounded">POST /api/field-sales/orders<br>{ ..., "driver_code":"DRV-001", "sales_date":"2026-09-13" }</div>
      <div class="flex gap-2 mt-2">
        <a href="{{ route('customer-demands.index') }}" class="px-3 py-1 bg-blue-600 text-white rounded text-sm">Demands</a>
        <a href="{{ route('competitor-intel.index') }}" class="px-3 py-1 bg-yellow-600 text-white rounded text-sm">Competitor</a>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="card p-4 rounded-xl">
      <h3 class="font-bold mb-2">Today's Sales List @if($driverCode)<span class="text-xs font-mono">filter: {{ $driverCode }}</span>@endif</h3>
      @forelse($todaySalesList as $s)
        <div class="text-sm flex justify-between p-1 border-b"><span class="font-mono">{{ $s->invoice_number }} @if($s->driver_code)<span class="badge badge-green text-[10px]">{{ $s->driver_code }}</span>@endif</span><span>TZS {{ number_format($s->total) }}</span></div>
      @empty <p class="text-sm text-gray-500">No field sales today @if($driverCode) for {{ $driverCode }} @endif</p> @endforelse
      @forelse($todayOrdersList as $o)
        <div class="text-sm flex justify-between p-1 border-b"><span class="font-mono">{{ $o->order_number }} @if($o->driver_code)<span class="badge badge-yellow text-[10px]">{{ $o->driver_code }}</span>@endif</span><span class="badge badge-blue">{{ $o->status }}</span></div>
      @empty @endforelse
    </div>
    <div class="card p-4 rounded-xl">
      <h3 class="font-bold mb-2">My Sales (Last 10)</h3>
      @forelse($mySales as $s)<div class="text-sm flex justify-between"><span>{{ $s->invoice_number }} @if($s->driver_code)<span class="text-xs text-gray-400">({{ $s->driver_code }})</span>@endif</span><span>TZS {{ number_format($s->total) }}</span></div>@empty<p class="text-sm text-gray-500">No sales</p>@endforelse
      <h3 class="font-bold mt-4 mb-2">My Orders (Last 10)</h3>
      @forelse($myOrders as $o)<div class="text-sm flex justify-between"><span>{{ $o->order_number }} @if($o->driver_code)<span class="text-xs text-gray-400">({{ $o->driver_code }})</span>@endif</span><span class="badge badge-blue">{{ $o->status }}</span></div>@empty<p class="text-sm text-gray-500">No orders</p>@endforelse
    </div>
  </div>
</div>
@endsection
