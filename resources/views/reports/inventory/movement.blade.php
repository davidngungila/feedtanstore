@extends('layouts.app')

@section('page-title', 'Stock Movement')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class='text-xl font-bold text-primary-900'>Stock Movement</h2>
            <form method="GET" action="{{ route('reports.inventory.movement') }}" class="flex flex-wrap items-center gap-2">
                <select name="product_id" class="form-input input-field px-3 py-2">
                    <option value="">All Products</option>
                    @foreach($allProducts as $p)
                        <option value="{{ $p->id }}" {{ request('product_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
                <input type="date" name="start_date" value="{{ request('start_date', $startDate) }}" class="form-input input-field px-3 py-2">
                <span class="text-gray-500">to</span>
                <input type="date" name="end_date" value="{{ request('end_date', $endDate) }}" class="form-input input-field px-3 py-2">
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">Filter</button>
                <a href="{{ route('reports.inventory.movement.download', ['start_date' => request('start_date', $startDate), 'end_date' => request('end_date', $endDate), 'product_id' => request('product_id')]) }}" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">Export PDF</a>
            </form>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5">
                <p class="text-sm text-green-700 mb-1">Stock In (GRN)</p>
                <h3 class="text-2xl font-bold text-green-900">{{ number_format($products->sum('qty_in')) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-5">
                <p class="text-sm text-red-700 mb-1">Sold</p>
                <h3 class="text-2xl font-bold text-red-900">{{ number_format($products->sum('qty_sold')) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5">
                <p class="text-sm text-blue-700 mb-1">Adjusted (+/-)</p>
                <h3 class="text-2xl font-bold text-blue-900">{{ number_format($products->sum('qty_adjusted')) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl p-5">
                <p class="text-sm text-purple-700 mb-1">Transferred Out</p>
                <h3 class="text-2xl font-bold text-purple-900">{{ number_format($products->sum('qty_transferred')) }}</h3>
            </div>
        </div>

        @if($products->isEmpty())
        <div class="text-center py-10 text-gray-500">
            <i class="fas fa-exchange-alt text-4xl mb-3"></i>
            <p>No stock movements recorded in this period.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Product</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Current Stock</th>
                        <th class="px-4 py-3 text-right text-green-700 font-medium">In (GRN)</th>
                        <th class="px-4 py-3 text-right text-red-700 font-medium">Sold</th>
                        <th class="px-4 py-3 text-right text-blue-700 font-medium">Adjusted</th>
                        <th class="px-4 py-3 text-right text-purple-700 font-medium">Transferred</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Net Change</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $product->name }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($product->quantity) }}</td>
                        <td class="px-4 py-3 text-right text-green-700">+{{ number_format($product->qty_in) }}</td>
                        <td class="px-4 py-3 text-right text-red-700">-{{ number_format($product->qty_sold) }}</td>
                        <td class="px-4 py-3 text-right text-blue-700">{{ number_format($product->qty_adjusted) }}</td>
                        <td class="px-4 py-3 text-right text-purple-700">-{{ number_format($product->qty_transferred) }}</td>
                        <td class="px-4 py-3 text-right font-bold {{ $product->net_change >= 0 ? 'text-green-700' : 'text-red-700' }}">
                            {{ $product->net_change >= 0 ? '+' : '' }}{{ number_format($product->net_change) }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
