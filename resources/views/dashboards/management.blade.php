@extends('layouts.app')
@section('content')
<div class="p-4">
  <h1 class="text-2xl font-bold mb-4">Management Dashboard – POS + Inventory + Online + Field Sales + Intelligence + Audit</h1>
  <div class="grid grid-cols-4 gap-4 mb-4">
    <div class="card p-4 rounded-xl"><div class="text-sm text-gray-500">Today Sales</div><div class="text-2xl font-bold">TZS {{ number_format($todaySales) }}</div></div>
    <div class="card p-4 rounded-xl"><div class="text-sm text-gray-500">Gross Profit Today</div><div class="text-2xl font-bold text-green-600">TZS {{ number_format($todayProfit ?? 0) }}</div></div>
    <div class="card p-4 rounded-xl"><div class="text-sm text-gray-500">Orders (Total / Paid / Unpaid)</div><div class="text-lg font-bold">{{ $orders }} / {{ $paidOrders }} / {{ $unpaidOrders }}</div></div>
    <div class="card p-4 rounded-xl"><div class="text-sm text-gray-500">Returns / Stock Alerts</div><div class="text-lg font-bold">{{ $returns }} / {{ $lowStock }}</div></div>
    <div class="card p-4 rounded-xl"><div class="text-sm text-gray-500">Stock Variance (submitted)</div><div class="text-2xl font-bold text-yellow-600">{{ $varianceSessions }}</div></div>
    <div class="card p-4 rounded-xl"><div class="text-sm text-gray-500">Avg Rating</div><div class="text-2xl font-bold">{{ number_format($avgRating ?? 0,1) }} ★</div></div>
    <div class="card p-4 rounded-xl"><div class="text-sm text-gray-500">Demands / Competitors</div><div class="text-lg font-bold">{{ $demands }} / {{ $competitors }}</div></div>
    <div class="card p-4 rounded-xl"><div class="text-sm text-gray-500">Avg Service Time</div><div class="text-lg font-bold">{{ $serviceAvg ? gmdate('i:s', (int)$serviceAvg) : '-' }}</div></div>
  </div>
  <div class="grid grid-cols-2 gap-4">
    <div class="card p-4 rounded-xl">
      <h3 class="font-bold mb-2">Quick Links</h3>
      <div class="grid grid-cols-2 gap-2 text-sm">
        <a href="{{ route('stock-verification.index') }}" class="p-2 bg-primary-50 rounded hover:bg-primary-100">Stock Verification</a>
        <a href="{{ route('customer-demands.index') }}" class="p-2 bg-blue-50 rounded">Customer Demands</a>
        <a href="{{ route('competitor-intel.index') }}" class="p-2 bg-yellow-50 rounded">Competitor Intel</a>
        <a href="{{ route('revenue.index') }}" class="p-2 bg-green-50 rounded">Revenue Analytics</a>
        <a href="{{ route('cashier-performance.index') }}" class="p-2 bg-purple-50 rounded">Cashier Performance</a>
        <a href="{{ route('demand-intelligence.index') }}" class="p-2 bg-orange-50 rounded">Demand Dashboard</a>
        <a href="{{ route('offline.status') }}" class="p-2 bg-gray-50 rounded">Offline Sync</a>
        <a href="{{ route('transaction-issues.index') }}" class="p-2 bg-red-50 rounded">Transaction Issues</a>
      </div>
    </div>
    <div class="card p-4 rounded-xl">
      <h3 class="font-bold mb-2">Field Sales Today</h3>
      <div class="text-2xl font-bold">TZS {{ number_format($fieldSales) }}</div>
      <p class="text-xs text-gray-500">In-store + Field + Online channels tracked separately.</p>
      <a href="{{ route('field-sales.dashboard') }}" class="text-primary-600 text-sm">View Field Sales →</a>
    </div>
  </div>
</div>
@endsection
