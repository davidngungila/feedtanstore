@extends('layouts.app')

@section('page-title', 'Bulk Dispatch')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <a href="{{ route('marketing-officer.orders') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Orders
            </a>
            <h1 class="text-2xl font-bold text-primary-900 mt-2">Bulk Dispatch</h1>
            <p class="text-gray-600">Select multiple orders and group nearby customers into a single delivery batch.</p>
        </div>
        @if(! $orders->isEmpty())
        <div class="flex items-center gap-3">
            <span class="text-sm text-gray-600">Selected: <strong id="selected-count">0</strong></span>
            <button type="button" onclick="selectAll()" class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">Select All</button>
            <button type="button" onclick="clearSelection()" class="px-3 py-2 text-sm border border-gray-300 rounded-lg hover:bg-gray-50">Clear</button>
        </div>
        @endif
    </div>

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
            <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
        </div>
    @endif

    @include('marketing-officer.partials.bulk-dispatch-form', [
        'bulkOrders' => $orders,
        'ordersForMap' => $ordersForMap,
        'riders' => $riders,
        'storeLat' => $storeLat,
        'storeLng' => $storeLng,
        'defaultRadius' => $defaultRadius,
        'bulkCheckedIds' => $preselected,
    ])
</div>
@endsection
