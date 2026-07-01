@extends('layouts.app')

@section('page-title', $product->name)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('online.catalog') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Catalog
            </a>
            <form action="{{ route('online.catalog.toggle', $product) }}" method="POST">
                @csrf
                <button type="submit" class="px-4 py-2 text-sm rounded-full
                    @if($product->is_available_online) bg-green-100 text-green-800 hover:bg-green-200 @else bg-red-100 text-red-800 hover:bg-red-200 @endif">
                    {{ $product->is_available_online ? 'Online' : 'Offline' }}
                </button>
            </form>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div>
                @php
                    $primaryImage = $product->images->firstWhere('is_primary', true);
                    $imageToShow = $primaryImage ? $primaryImage->image_path : $product->image;
                @endphp
                <div id="main-image-container" class="bg-gray-100 rounded-lg aspect-square flex items-center justify-center overflow-hidden mb-4">
                    @if($imageToShow)
                        <img id="main-image" src="{{ $imageToShow }}" alt="{{ $product->name }}" class="max-h-full max-w-full object-contain">
                    @else
                        <i class="fas fa-box text-8xl text-gray-400"></i>
                    @endif
                </div>

                @if($product->images->count() > 0)
                    <div class="grid grid-cols-4 gap-3">
                        @foreach($product->images as $image)
                            <div class="relative">
                                <div onclick="document.getElementById('main-image').src='{{ $image->image_path }}'" class="bg-gray-100 rounded-lg aspect-square flex items-center justify-center overflow-hidden cursor-pointer hover:ring-2 hover:ring-primary-500 @if($image->is_primary) ring-2 ring-primary-500 @endif">
                                    <img src="{{ $image->image_path }}" alt="{{ $product->name }}" class="max-h-full max-w-full object-contain">
                                </div>
                                @if(!$image->is_primary)
                                    <form action="{{ route('online.catalog.images.primary', [$product, $image]) }}" method="POST" class="absolute -top-2 -right-2">
                                        @csrf
                                        <button type="submit" class="bg-primary-600 text-white p-1 rounded-full text-xs" title="Set as primary">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('online.catalog.images.delete', [$product, $image]) }}" method="POST" class="absolute -bottom-2 -right-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bg-red-600 text-white p-1 rounded-full text-xs" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>
                @endif

                <div class="mt-6">
                    <h3 class="text-lg font-semibold text-gray-900 mb-3">Upload New Image</h3>
                    <form action="{{ route('online.catalog.images.upload', $product) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="flex gap-3">
                            <input type="file" name="image" accept="image/*" required class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                                Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div>
                <h1 class="text-3xl font-bold text-primary-900 mb-2">{{ $product->name }}</h1>
                
                <div class="flex flex-wrap gap-2 mb-4">
                    @if($product->category)
                        <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm">{{ $product->category->name }}</span>
                    @endif
                    @if($product->brand)
                        <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm">{{ $product->brand->name }}</span>
                    @endif
                    @if($product->unit)
                        <span class="bg-gray-100 text-gray-700 px-3 py-1 rounded-full text-sm">{{ $product->unit->name }}</span>
                    @endif
                </div>

                <p class="text-4xl font-bold text-primary-600 mb-6">TZS {{ number_format($product->selling_price, 2) }}</p>

                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-1">Stock Quantity</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $product->quantity }}</p>
                        @if($product->quantity <= $product->reorder_level)
                            <p class="text-sm text-red-600 font-semibold mt-1">Low Stock (Reorder: {{ $product->reorder_level }})</p>
                        @endif
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-600 mb-1">Cost Price</p>
                        <p class="text-2xl font-semibold text-gray-900">TZS {{ number_format($product->cost_price, 2) }}</p>
                    </div>
                </div>

                @if($product->description)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Description</h3>
                        <p class="text-gray-700">{{ $product->description }}</p>
                    </div>
                @endif

                @if($product->sku || $product->barcode || $product->expiry_date || $product->batch_number)
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Additional Details</h3>
                        <div class="grid grid-cols-2 gap-4">
                            @if($product->sku)
                                <div>
                                    <p class="text-sm text-gray-600">SKU</p>
                                    <p class="font-medium text-gray-900">{{ $product->sku }}</p>
                                </div>
                            @endif
                            @if($product->barcode)
                                <div>
                                    <p class="text-sm text-gray-600">Barcode</p>
                                    <p class="font-medium text-gray-900">{{ $product->barcode }}</p>
                                </div>
                            @endif
                            @if($product->expiry_date)
                                <div>
                                    <p class="text-sm text-gray-600">Expiry Date</p>
                                    <p class="font-medium text-gray-900">{{ $product->expiry_date->format('M d, Y') }}</p>
                                </div>
                            @endif
                            @if($product->batch_number)
                                <div>
                                    <p class="text-sm text-gray-600">Batch Number</p>
                                    <p class="font-medium text-gray-900">{{ $product->batch_number }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Product Orders -->
        @if($product->onlineOrderItems->count() > 0)
            <div class="card rounded-2xl p-6 mt-6">
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-xl font-bold text-primary-900">Orders for this product</h2>
                    <span class="text-gray-500">{{ $product->onlineOrderItems->count() }} total</span>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($product->onlineOrderItems->sortByDesc('id') as $item)
                                <tr>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->order->order_number }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $item->order->customer_name }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $item->quantity }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">TZS {{ number_format($item->total, 2) }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="px-2 py-1 text-xs rounded-full {{ $item->order->status === 'delivered' ? 'bg-green-100 text-green-800' : ($item->order->status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800') }}">
                                            {{ ucwords(str_replace('_', ' ', $item->order->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $item->order->created_at->format('M d, Y') }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        <a href="{{ route('online.orders.show', $item->order->id) }}" class="text-primary-600 hover:text-primary-900 font-medium">View</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection