@extends('layouts.app')
@section('page-title','Store Supervisor Dashboard')
@section('content')
<div class="p-4 space-y-4">
  <div class="flex justify-between items-center">
    <h1 class="text-2xl font-bold text-primary-900">Store Supervisor – Operational Control Center</h1>
    <span class="badge badge-green">Manages most issues</span>
  </div>

  <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
    <div class="card p-4 rounded-xl"><div class="text-sm text-gray-500">Open Issues</div><div class="text-2xl font-bold text-red-600">{{ $stats['open_issues'] }}</div><a href="{{ route('store-supervisor.issues',['status'=>'open']) }}" class="text-xs text-primary-600 hover:underline">Manage →</a></div>
    <div class="card p-4 rounded-xl"><div class="text-sm text-gray-500">Investigating</div><div class="text-2xl font-bold text-yellow-600">{{ $stats['investigating_issues'] }}</div></div>
    <div class="card p-4 rounded-xl"><div class="text-sm text-gray-500">Pending Returns</div><div class="text-2xl font-bold text-orange-600">{{ $stats['pending_returns'] }}</div><a href="{{ route('sales.returns') }}" class="text-xs text-primary-600 hover:underline">Review →</a></div>
    <div class="card p-4 rounded-xl"><div class="text-sm text-gray-500">Pending Verifications</div><div class="text-2xl font-bold text-purple-600">{{ $stats['pending_verifications'] }}</div><a href="{{ route('store-supervisor.verifications') }}" class="text-xs text-primary-600 hover:underline">Review →</a></div>
    <div class="card p-4 rounded-xl"><div class="text-sm text-gray-500">Low Stock Alerts</div><div class="text-2xl font-bold">{{ $stats['low_stock'] }}</div><a href="{{ route('inventory.low-stock') }}" class="text-xs text-primary-600 hover:underline">View</a></div>
    <div class="card p-4 rounded-xl"><div class="text-sm text-gray-500">New Customer Demands</div><div class="text-2xl font-bold text-blue-600">{{ $stats['pending_demands'] }}</div><a href="{{ route('store-supervisor.demands') }}" class="text-xs text-primary-600 hover:underline">Manage →</a></div>
    <div class="card p-4 rounded-xl"><div class="text-sm text-gray-500">Offline Pending / Failed</div><div class="text-lg font-bold">{{ $stats['pending_offline'] }} / <span class="text-red-600">{{ $stats['failed_offline'] }}</span></div><a href="{{ route('offline.status') }}" class="text-xs text-primary-600 hover:underline">Sync →</a></div>
    <div class="card p-4 rounded-xl"><div class="text-sm text-gray-500">Avg Rating / Today Sales</div><div class="text-lg font-bold">{{ number_format($stats['recent_ratings_avg']??0,1) }} ★ / TZS {{ number_format($stats['today_sales']) }}</div></div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="card rounded-xl p-4">
      <h3 class="font-bold mb-2">Recent Transaction Issues <span class="text-xs text-gray-500">Open → Investigating → Resolved → Closed</span></h3>
      <div class="space-y-2">
        @forelse($recentIssues as $issue)
          <div class="flex justify-between items-center p-2 bg-gray-50 rounded">
            <div><div class="font-mono text-sm">{{ $issue->issue_number }} <span class="badge badge-yellow text-[10px]">{{ $issue->status }}</span></div><div class="text-xs text-gray-500">{{ $issue->issue_type }} – {{ Str::limit($issue->description,60) }}</div></div>
            <a href="{{ route('transaction-issues.show',$issue) }}" class="text-primary-600 text-sm">View</a>
          </div>
        @empty <p class="text-sm text-gray-500">No recent issues</p> @endforelse
      </div>
      <a href="{{ route('store-supervisor.issues') }}" class="inline-block mt-2 text-sm text-primary-700 hover:underline">Manage all issues →</a>
    </div>
    <div class="card rounded-xl p-4">
      <h3 class="font-bold mb-2">Pending Returns (Approval Queue)</h3>
      @forelse($pendingReturns as $ret)
        <div class="flex justify-between items-center p-2 border-b text-sm">
          <div>{{ $ret->return_number }} – {{ $ret->sale->invoice_number ?? '' }} <span class="text-gray-500">TZS {{ number_format($ret->total) }}</span></div>
          <div class="flex gap-1">
            <form method="POST" action="{{ route('store-supervisor.returns.approve',$ret) }}">@csrf<button class="px-2 py-1 bg-green-600 text-white rounded text-xs">Approve</button></form>
            <form method="POST" action="{{ route('store-supervisor.returns.reject',$ret) }}">@csrf<input type="hidden" name="reason" value="Rejected by supervisor"><button class="px-2 py-1 bg-red-600 text-white rounded text-xs">Reject</button></form>
          </div>
        </div>
      @empty <p class="text-sm text-gray-500">No pending returns</p> @endforelse
    </div>
    <div class="card rounded-xl p-4">
      <h3 class="font-bold mb-2">Stock Verifications Awaiting Review</h3>
      @forelse($pendingVerifications as $sv)
        <div class="flex justify-between items-center p-2 border-b text-sm"><span>{{ $sv->session_number }} – {{ $sv->branch->name ?? 'No branch' }} ({{ $sv->status }})</span><a href="{{ route('stock-verification.show',$sv) }}" class="text-primary-600">Review</a></div>
      @empty <p class="text-sm text-gray-500">No pending verifications</p> @endforelse
    </div>
    <div class="card rounded-xl p-4">
      <h3 class="font-bold mb-2">Recent Customer Demands</h3>
      @forelse($recentDemands as $d)
        <div class="flex justify-between text-sm p-1 border-b"><span>{{ $d->product_requested }} (x{{ $d->requested_quantity }}) @if($d->was_out_of_stock)<span class="badge badge-red text-[10px]">OOS</span>@endif</span><span class="badge badge-blue text-[10px]">{{ $d->status }}</span></div>
      @empty <p class="text-sm text-gray-500">No demands</p> @endforelse
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="card rounded-xl p-4">
      <h3 class="font-bold mb-2">Cashier Performance (Today)</h3>
      @forelse($cashierPerf as $p)
        <div class="flex justify-between text-sm p-1"><span>{{ $p->cashier->name ?? 'Unknown' }}</span><span>{{ gmdate('i:s',(int)$p->avg_duration) }} avg • {{ $p->cnt }} customers</span></div>
      @empty <p class="text-sm text-gray-500">No service data today</p> @endforelse
      <a href="{{ route('cashier-performance.index') }}" class="text-xs text-primary-600 hover:underline">View full analytics →</a>
    </div>
    <div class="card rounded-xl p-4">
      <h3 class="font-bold mb-2">Offline Sync Queue (Last 5)</h3>
      @forelse($offlineQueue as $q)
        <div class="flex justify-between text-xs p-1 border-b"><span class="font-mono">{{ Str::limit($q->local_transaction_id,18) }}</span><span class="badge {{ $q->sync_status==='synced'?'badge-green':($q->sync_status==='failed'?'badge-red':'badge-yellow') }}">{{ $q->sync_status }}</span></div>
      @empty <p class="text-sm text-gray-500">Queue empty – all synced</p> @endforelse
    </div>
  </div>

  <div class="card p-4 rounded-xl">
    <h3 class="font-bold mb-2">Quick Actions</h3>
    <div class="grid grid-cols-2 md:grid-cols-4 gap-2 text-sm">
      <a href="{{ route('store-supervisor.issues') }}" class="p-2 bg-red-50 rounded hover:bg-red-100">Manage Issues</a>
      <a href="{{ route('sales.returns') }}" class="p-2 bg-orange-50 rounded">Sale Returns</a>
      <a href="{{ route('stock-verification.index') }}" class="p-2 bg-purple-50 rounded">Verifications</a>
      <a href="{{ route('customer-demands.index') }}" class="p-2 bg-blue-50 rounded">Demands</a>
      <a href="{{ route('competitor-intel.index') }}" class="p-2 bg-yellow-50 rounded">Competitor Intel</a>
      <a href="{{ route('offline.status') }}" class="p-2 bg-gray-50 rounded">Offline Sync</a>
      <a href="{{ route('customer-ratings.index') }}" class="p-2 bg-green-50 rounded">Customer Ratings</a>
      <a href="{{ route('revenue.index') }}" class="p-2 bg-emerald-50 rounded">Revenue</a>
    </div>
  </div>
</div>
@endsection
