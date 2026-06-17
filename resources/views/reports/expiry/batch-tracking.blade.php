@extends('layouts.app')

@section('page-title', 'Batch Tracking')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class="text-xl font-bold text-primary-900">Batch Tracking</h2>
            <div class="flex items-center gap-3">
                <a href="{{ route('reports.expiry.batch-tracking.download') }}" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">
                    Export PDF
                </a>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-green-200 flex items-center justify-center">
                        <i class="fas fa-barcode text-green-700"></i>
                    </div>
                </div>
                <p class="text-sm text-green-700 mb-1">Products with Batch Numbers</p>
                <h3 class="text-2xl font-bold text-green-900">{{ $products->count() }}</h3>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-200 flex items-center justify-center">
                        <i class="fas fa-boxes text-blue-700"></i>
                    </div>
                </div>
                <p class="text-sm text-blue-700 mb-1">Total Quantity</p>
                <h3 class="text-2xl font-bold text-blue-900">{{ number_format($products->sum('quantity')) }}</h3>
            </div>
        </div>

        <!-- Products Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Product</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Category</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Brand</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Batch Number</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Quantity</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Expiry Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($products as $product)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $product->name }}</td>
                        <td class="px-4 py-3">{{ $product->category ? $product->category->name : 'N/A' }}</td>
                        <td class="px-4 py-3">{{ $product->brand ? $product->brand->name : 'N/A' }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full bg-primary-100 text-primary-700">
                                {{ $product->batch_number }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right font-semibold">{{ number_format($product->quantity) }}</td>
                        <td class="px-4 py-3">
                            @if($product->expiry_date)
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $product->expiry_date < now() ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $product->expiry_date->format('d F Y') }}
                            </span>
                            @else
                            <span class="text-gray-400">N/A</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                    @if($products->isEmpty())
                    <tr>
                        <td colspan="6" class="px-4 py-8 text-center text-gray-500">
                            No products with batch numbers
                        </td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
