@extends('layouts.app')

@section('page-title', 'Dead Stock')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class='text-xl font-bold text-primary-900'>Dead Stock (No sales in {{ $days }} days)</h2>
            <form method="GET" action="{{ route('reports.inventory.dead-stock') }}" class="flex flex-wrap items-center gap-2">
                <input type="number" name="days" value="{{ request('days', $days) }}" min="1" class="form-input input-field px-3 py-2 w-24" placeholder="Days">
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">Filter</button>
                <a href="{{ route('reports.inventory.dead-stock.download', ['days' => request('days', $days)]) }}" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">Export PDF</a>
            </form>
        </div>

        @if($products->isEmpty())
        <div class="text-center py-10 text-gray-500">
            <i class="fas fa-circle-check text-4xl mb-3"></i>
            <p>Great — no dead stock found in the last {{ $days }} days.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Product</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Category</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Qty in Stock</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Stock Value</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Expiry</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $product->name }}</td>
                        <td class="px-4 py-3">{{ $product->category->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-right font-bold">{{ number_format($product->quantity) }}</td>
                        <td class="px-4 py-3 text-right">TZS {{ number_format($product->quantity * $product->cost_price, 2) }}</td>
                        <td class="px-4 py-3 text-right">{{ $product->expiry_date ? $product->expiry_date->format('M d, Y') : '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-bold">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right">Total Dead Stock Value</td>
                        <td colspan="2" class="px-4 py-3 text-right">TZS {{ number_format($products->sum(fn($p) => $p->quantity * $p->cost_price), 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
