@extends('layouts.app')

@section('page-title', 'Sales by Brand')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class='text-xl font-bold text-primary-900'>Sales by Brand</h2>
            <form method="GET" action="{{ route('reports.sales.by-brand') }}" class="flex flex-wrap items-center gap-2">
                <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}" class="form-input input-field px-3 py-2">
                <span class="text-gray-500">to</span>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}" class="form-input input-field px-3 py-2">
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">Filter</button>
                <a href="{{ route('reports.sales.by-brand.download', ['start_date' => request('start_date', $startDate), 'end_date' => request('end_date', $endDate)]) }}" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">Export PDF</a>
            </form>
        </div>

        @if($brands->isEmpty())
        <div class="text-center py-10 text-gray-500">
            <i class="fas fa-tags text-4xl mb-3"></i>
            <p>No sales recorded in this period.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Brand</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Qty Sold</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Total Sales</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($brands as $brand)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $brand->name }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($brand->total_qty) }}</td>
                        <td class="px-4 py-3 text-right font-semibold">TZS {{ number_format($brand->total_sales, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-bold">
                    <tr>
                        <td class="px-4 py-3 text-right">Total</td>
                        <td class="px-4 py-3 text-right">{{ number_format($brands->sum('total_qty')) }}</td>
                        <td class="px-4 py-3 text-right">TZS {{ number_format($brands->sum('total_sales'), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
