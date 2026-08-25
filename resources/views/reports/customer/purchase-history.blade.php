@extends('layouts.app')

@section('page-title', 'Customer Purchase History')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class='text-xl font-bold text-primary-900'>Customer Purchase History</h2>
            <form method="GET" action="{{ route('reports.customer.purchase-history') }}" class="flex flex-wrap items-center gap-2">
                <select name="customer_id" class="form-input input-field px-3 py-2">
                    <option value="">All Customers</option>
                    @foreach($customers as $customer)
                        <option value="{{ $customer->id }}" {{ request('customer_id') == $customer->id ? 'selected' : '' }}>{{ $customer->name }}</option>
                    @endforeach
                </select>
                <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}" class="form-input input-field px-3 py-2">
                <span class="text-gray-500">to</span>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}" class="form-input input-field px-3 py-2">
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">Filter</button>
                <a href="{{ route('reports.customer.purchase-history.download', array_filter(['customer_id' => request('customer_id'), 'start_date' => request('start_date', $startDate), 'end_date' => request('end_date', $endDate)])) }}" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">Export PDF</a>
            </form>
        </div>

        @if($sales->isEmpty())
        <div class="text-center py-10 text-gray-500">
            <i class="fas fa-receipt text-4xl mb-3"></i>
            <p>No purchases found in this period.</p>
        </div>
        @else
        <div class="space-y-4">
            @foreach($sales as $sale)
            <div class="border border-gray-100 rounded-xl overflow-hidden">
                <div class="bg-gray-50 px-4 py-3 flex flex-wrap items-center justify-between gap-2">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="font-bold text-primary-900">{{ $sale->invoice_number ?? 'Sale #' . $sale->id }}</span>
                        <span class="text-sm text-gray-600">{{ $sale->customer->name ?? 'Walk-in' }}</span>
                    </div>
                    <div class="flex items-center gap-4 text-sm text-gray-600">
                        <span>{{ $sale->created_at->format('M d, Y H:i') }}</span>
                        <span class="font-bold text-primary-900">TZS {{ number_format($sale->total, 2) }}</span>
                    </div>
                </div>
                <table class="w-full text-sm">
                    <tbody class="divide-y divide-gray-50">
                        @foreach($sale->items as $item)
                        <tr>
                            <td class="px-4 py-2">{{ $item->product->name ?? 'Product' }}</td>
                            <td class="px-4 py-2 text-right text-gray-500">{{ number_format($item->quantity) }} x TZS {{ number_format($item->unit_price, 2) }}</td>
                            <td class="px-4 py-2 text-right font-medium w-40">TZS {{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endforeach
        </div>

        <div class="mt-6 bg-gray-50 rounded-xl p-4 flex justify-between items-center">
            <span class="font-semibold text-primary-900">Total: {{ $sales->count() }} transactions</span>
            <span class="font-bold text-lg text-primary-900">TZS {{ number_format($sales->sum('total'), 2) }}</span>
        </div>
        @endif
    </div>
</div>
@endsection
