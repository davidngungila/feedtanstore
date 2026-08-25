@extends('layouts.app')

@section('page-title', 'Slow Moving Items')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class='text-xl font-bold text-primary-900'>Slow Moving Items</h2>
            <form method="GET" action="{{ route('reports.inventory.slow-moving') }}" class="flex flex-wrap items-center gap-2">
                <input type="number" name="days" value="{{ request('days', $days) }}" min="1" class="form-input input-field px-3 py-2 w-24" placeholder="Days">
                <input type="number" name="threshold" value="{{ request('threshold', $threshold) }}" min="0" class="form-input input-field px-3 py-2 w-28" placeholder="Max Qty">
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">Filter</button>
                <a href="{{ route('reports.inventory.slow-moving.download', ['days' => request('days', $days), 'threshold' => request('threshold', $threshold)]) }}" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">Export PDF</a>
            </form>
        </div>

        @if($products->isEmpty())
        <div class="text-center py-10 text-gray-500">
            <i class="fas fa-snail text-4xl mb-3"></i>
            <p>No products with sales at or below {{ $threshold }} units in the last {{ $days }} days.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">#</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Product</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Category</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Qty Sold ({{ $days }}d)</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Current Stock</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Stock Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($products as $index => $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-bold text-yellow-600">{{ $index + 1 }}</td>
                        <td class="px-4 py-3 font-medium">{{ $product->name }}</td>
                        <td class="px-4 py-3">{{ $product->category->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-right font-bold">{{ number_format($product->total_sold) }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($product->quantity) }}</td>
                        <td class="px-4 py-3 text-right">TZS {{ number_format($product->quantity * $product->cost_price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
