@extends('layouts.app')

@section('page-title', 'Stock Accuracy')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <h2 class="text-xl font-bold text-primary-900 mb-6">Stock Accuracy</h2>

        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Product</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Category</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Current Stock</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Reorder Level</th>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($products as $product)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $product->name }}</td>
                        <td class="px-4 py-3">{{ $product->category ? $product->category->name : 'N/A' }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($product->quantity) }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($product->reorder_level) }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                @if($product->quantity == 0)
                                    bg-red-100 text-red-700
                                @elseif($product->quantity <= $product->reorder_level)
                                    bg-yellow-100 text-yellow-700
                                @else
                                    bg-green-100 text-green-700
                                @endif
                            ">
                                @if($product->quantity == 0)
                                    Out of Stock
                                @elseif($product->quantity <= $product->reorder_level)
                                    Low Stock
                                @else
                                    OK
                                @endif
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
