@extends('layouts.app')
@section('content')
<div class="p-4">
  <h1 class="text-2xl font-bold mb-4">Cashier Performance Analytics</h1>
  <p class="text-xs text-gray-500 mb-2">Timer starts only on New Sale click or first product scan – not on login/POS open. Measured: start, end, duration.</p>
  <form class="flex gap-2 mb-4">
    <input type="date" name="from" value="{{ $from }}" class="form-input input-field" style="max-width:150px">
    <input type="date" name="to" value="{{ $to }}" class="form-input input-field" style="max-width:150px">
    <select name="cashier_id" class="form-input input-field" style="max-width:180px"><option value="">All Cashiers</option>@foreach($cashiers as $c)<option value="{{ $c->id }}" {{ (string)$cashierId===(string)$c->id?'selected':'' }}>{{ $c->name }}</option>@endforeach</select>
    <button class="px-4 py-2 bg-primary-600 text-white rounded">Filter</button>
  </form>
  <div class="grid grid-cols-4 gap-4 mb-4">
    <div class="card p-3 rounded-xl text-center"><div class="text-sm text-gray-500">Avg Service Time</div><div class="font-bold text-lg">{{ $avgService ? gmdate('i:s', (int)$avgService) : '-' }}</div></div>
    <div class="card p-3 rounded-xl text-center"><div class="text-sm text-gray-500">Total Customers</div><div class="font-bold text-lg">{{ $totalCustomers }}</div></div>
    <div class="card p-3 rounded-xl text-center"><div class="text-sm text-gray-500">Per Hour</div><div class="font-bold text-lg">{{ $perHour }}</div></div>
    <div class="card p-3 rounded-xl text-center"><div class="text-sm text-gray-500">Fastest / Longest</div><div class="font-bold text-sm">{{ $fastest ? gmdate('i:s',$fastest):'-' }} / {{ $longest ? gmdate('i:s',$longest):'-' }}</div></div>
  </div>
  <div class="grid grid-cols-2 gap-4">
    <div class="card p-4 rounded-xl"><h3 class="font-bold mb-2">By Cashier</h3>@foreach($byCashier as $r)<div class="flex justify-between text-sm"><span>{{ $r->cashier->name ?? '-' }}</span><span>{{ gmdate('i:s',(int)$r->avg_duration) }} ({{ $r->customers }})</span></div>@endforeach</div>
    <div class="card p-4 rounded-xl"><h3 class="font-bold mb-2">By Hour (bottlenecks)</h3>@foreach($byHour as $r)<div class="flex justify-between text-sm"><span>{{ $r->hr }}:00</span><span>{{ gmdate('i:s',(int)$r->avg_duration) }} cnt {{ $r->cnt }}</span></div>@endforeach</div>
    <div class="card p-4 rounded-xl col-span-2"><h3 class="font-bold mb-2">Daily Trend</h3>@foreach($daily as $r)<div class="flex justify-between text-sm"><span>{{ $r->d }}</span><span>{{ gmdate('i:s',(int)$r->avg_duration) }} cnt {{ $r->cnt }}</span></div>@endforeach</div>
  </div>
</div>
@endsection
