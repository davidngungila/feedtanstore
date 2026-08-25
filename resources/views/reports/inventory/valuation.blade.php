@extends('layouts.app')

@section('page-title', 'Inventory Valuation')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class='text-xl font-bold text-primary-900'>Inventory Valuation</h2>
            <a href="{{ route('reports.inventory.valuation.download') }}" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">Export PDF</a>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-xl p-5">
                <p class="text-sm text-primary-700 mb-1">Total Cost Value</p>
                <h3 class="text-2xl font-bold text-primary-900">TZS {{ number_format($totalCostValue, 2) }}</h3>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5">
                <p class="text-sm text-green-700 mb-1">Total Retail Value</p>
                <h3 class="text-2xl font-bold text-green-900">TZS {{ number_format($totalRetailValue, 2) }}</h3>
            </div>
        </div>

        @if($products->isEmpty())
        <div class="text-center py-10 text-gray-500">
            <i class="fas fa-boxes-stacked text-4xl mb-3"></i>
            <p>No products found.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Product</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Category</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Qty</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Cost Price</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Cost Value</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Selling Price</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Retail Value</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium">{{ $product->name }}</td>
                        <td class="px-4 py-3">{{ $product->category->name ?? '-' }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($product->quantity) }}</td>
                        <td class="px-4 py-3 text-right">TZS {{ number_format($product->cost_price, 2) }}</td>
                        <td class="px-4 py-3 text-right">TZS {{ number_format($product->quantity * $product->cost_price, 2) }}</td>
                        <td class="px-4 py-3 text-right">TZS {{ number_format($product->selling_price, 2) }}</td>
                        <td class="px-4 py-3 text-right font-semibold">TZS {{ number_format($product->quantity * $product->selling_price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-gray-50 font-bold">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-right">Total</td>
                        <td class="px-4 py-3 text-right">TZS {{ number_format($totalCostValue, 2) }}</td>
                        <td></td>
                        <td class="px-4 py-3 text-right">TZS {{ number_format($totalRetailValue, 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
