@extends('layouts.app')

@section('page-title', 'Damaged Goods')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Damaged Goods</h2>
            <button class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                <i class="fas fa-plus mr-2"></i>Report Damaged Goods
            </button>
        </div>
        @if(count($damagedGoods) > 0)
            <div class="overflow-x-auto">
                <table class="data-table w-full">
                    <thead>
                        <tr>
                            <th class="text-left">Ref. Number</th>
                            <th class="text-left">Product</th>
                            <th class="text-left">Quantity</th>
                            <th class="text-left">Reason</th>
                            <th class="text-left">Location</th>
                            <th class="text-left">Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($damagedGoods as $item)
                        <tr>
                            <td class="font-medium text-primary-900">{{ $item->reference_number }}</td>
                            <td class="text-gray-600">
                                @if($item->product)
                                    <a href="{{ route('inventory.products.show', $item->product) }}" class="hover:underline">{{ $item->product->name }}</a>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="text-gray-600">{{ $item->quantity }}</td>
                            <td class="text-gray-600">{{ $item->reason }}</td>
                            <td class="text-gray-600">{{ $item->location->name ?? 'N/A' }}</td>
                            <td class="text-gray-600">{{ $item->date }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <i class="fas fa-check-circle text-6xl text-green-500 mb-4"></i>
                <h3 class="text-lg font-semibold text-primary-900 mb-2">No damaged goods reported!</h3>
                <p class="text-gray-600">All inventory is in good condition.</p>
            </div>
        @endif
    </div>
</div>
@endsection