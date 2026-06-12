@extends('layouts.app')

@section('page-title', 'Product Catalog')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Product Catalog</h2>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($products as $product)
            <div class="border rounded-lg p-4 hover:shadow-lg transition-shadow">
                <div class="flex justify-between items-start mb-2">
                    <h3 class="font-semibold text-primary-900">{{ $product->name }}</h3>
                    <form action="{{ route('online.catalog.toggle', $product) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-2 py-1 text-xs rounded-full
                            @if($product->is_available_online) bg-green-100 text-green-800 hover:bg-green-200 @else bg-red-100 text-red-800 hover:bg-red-200 @endif">
                            {{ $product->is_available_online ? 'Online' : 'Offline' }}
                        </button>
                    </form>
                </div>
                @if($product->category)
                    <p class="text-sm text-gray-500 mb-2">{{ $product->category->name }}</p>
                @endif
                <p class="text-lg font-bold text-primary-600 mb-2">TZS {{ number_format($product->selling_price, 2) }}</p>
                <div class="flex justify-between items-center text-sm">
                    <span class="text-gray-500">Stock: {{ $product->quantity }}</span>
                    @if($product->quantity <= $product->reorder_level)
                        <span class="text-red-600 text-xs font-semibold">Low Stock</span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection