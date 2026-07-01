@extends('layouts.app')

@section('page-title', $product->name)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    @if(session('success'))
        <div class="mb-6 px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-2xl flex items-center gap-3">
            <i class="fas fa-check-circle text-green-600 text-2xl"></i>
            <p class="text-green-800 font-medium">{{ session('success') }}</p>
        </div>
    @endif

    <div class="grid grid-cols-1 xl:grid-cols-3 gap-6">
        <!-- Left Column - Image Gallery -->
        <div class="xl:col-span-2">
            <div class="card rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-6">
                    <a href="{{ route('online.catalog') }}" class="flex items-center gap-2 text-primary-600 hover:text-primary-800 font-medium transition-colors">
                        <i class="fas fa-arrow-left"></i>
                        Back to Catalog
                    </a>
                    <div class="flex items-center gap-3">
                        <form action="{{ route('online.catalog.toggle', $product) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-5 py-2 text-sm font-medium rounded-full transition-all duration-200
                                @if($product->is_available_online) 
                                    bg-gradient-to-r from-green-500 to-emerald-600 text-white shadow-md shadow-green-200 hover:shadow-lg hover:from-green-600 hover:to-emerald-700 
                                @else 
                                    bg-gradient-to-r from-red-500 to-rose-600 text-white shadow-md shadow-red-200 hover:shadow-lg hover:from-red-600 hover:to-rose-700 
                                @endif">
                                <i class="fas fa-{{ $product->is_available_online ? 'globe' : 'ban' }} mr-2"></i>
                                {{ $product->is_available_online ? 'Online' : 'Offline' }}
                            </button>
                        </form>
                        <a href="{{ route('inventory.products.edit', $product) }}" class="px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-full font-medium transition-all duration-200 shadow-md shadow-primary-200 hover:shadow-lg">
                            <i class="fas fa-edit mr-2"></i>
                            Edit Product
                        </a>
                    </div>
                </div>

                @php
                    $baseUrl = $settings->store_url ?? config('app.url');
                    $resolveImageUrl = function ($path) use ($baseUrl) {
                        if (!$path) return null;
                        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
                            $parsed = parse_url($path);
                            if (isset($parsed['path'])) {
                                $path = ltrim($parsed['path'], '/');
                            }
                        }
                        $cleanPath = ltrim($path, '/');
                        if (str_starts_with($cleanPath, 'storage/')) {
                            return rtrim($baseUrl, '/') . '/' . $cleanPath;
                        }
                        return rtrim($baseUrl, '/') . '/storage/' . $cleanPath;
                    };
                    $primaryImage = $product->images->firstWhere('is_primary', true);
                    $imageToShow = $primaryImage ? $resolveImageUrl($primaryImage->image_path) : $resolveImageUrl($product->image);
                @endphp

                <!-- Main Image -->
                <div class="mb-6">
                    <div id="main-image-container" class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl aspect-square flex items-center justify-center overflow-hidden border-2 border-dashed border-gray-200">
                        @if($imageToShow)
                            <img id="main-image" src="{{ $imageToShow }}" alt="{{ $product->name }}" class="max-h-full max-w-full object-contain drop-shadow-2xl transition-all duration-300">
                        @else
                            <div class="text-center text-gray-400">
                                <i class="fas fa-box-open text-9xl mb-4 block"></i>
                                <p class="text-xl">No image uploaded</p>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Thumbnails -->
                @if($product->images->count() > 0)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                            <i class="fas fa-images text-primary-500"></i>
                            Product Images
                        </h3>
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-4" id="image-thumbnails">
                            @foreach($product->images as $index => $image)
                                <div class="image-thumbnail group relative" data-id="{{ $image->id }}">
                                    <div onclick="document.getElementById('main-image').src='{{ $resolveImageUrl($image->image_path) }}'" 
                                         class="bg-white rounded-xl p-3 aspect-square flex items-center justify-center overflow-hidden cursor-pointer hover:ring-4 hover:ring-primary-100 border-2 transition-all duration-300 @if($image->is_primary) ring-4 ring-primary-500 border-primary-500 shadow-md @else border-gray-200 hover:border-primary-300 @endif">
                                        <img src="{{ $resolveImageUrl($image->image_path) }}" alt="{{ $product->name }}" class="max-h-full max-w-full object-contain">
                                    </div>
                                    <div class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 transition-all duration-300 flex gap-1">
                                        @if(!$image->is_primary)
                                            <form action="{{ route('online.catalog.images.primary', [$product, $image]) }}" method="POST" class="z-10">
                                                @csrf
                                                <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-white p-2 rounded-full shadow-lg transition-all duration-200" title="Set as primary">
                                                    <i class="fas fa-star"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form action="{{ route('online.catalog.images.delete', [$product, $image]) }}" method="POST" class="z-10">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="bg-red-500 hover:bg-red-600 text-white p-2 rounded-full shadow-lg transition-all duration-200" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    @if($image->is_primary)
                                        <div class="absolute -top-3 left-1/2 -translate-x-1/2 bg-primary-600 text-white text-xs px-3 py-1 rounded-full shadow-md z-20">
                                            <i class="fas fa-crown mr-1"></i>
                                            Primary
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Upload Section -->
                <div class="mt-6 border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-cloud-upload-alt text-primary-500"></i>
                        Upload New Image
                    </h3>
                    <form action="{{ route('online.catalog.images.upload', $product) }}" method="POST" enctype="multipart/form-data" class="bg-gradient-to-br from-primary-50 to-blue-50 p-6 rounded-2xl border border-primary-100">
                        @csrf
                        <div class="flex flex-col sm:flex-row gap-4">
                            <div class="flex-1">
                                <label class="block mb-2 text-sm font-medium text-gray-700">Select Image</label>
                                <div class="relative">
                                    <input type="file" name="image" accept="image/*" required 
                                           class="w-full px-5 py-4 border-2 border-dashed border-gray-300 rounded-xl bg-white hover:border-primary-400 focus:border-primary-500 focus:ring-4 focus:ring-primary-100 transition-all duration-200 file:mr-4 file:py-2 file:px-6 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary-600 file:text-white hover:file:bg-primary-700">
                                </div>
                            </div>
                            <div class="flex items-end">
                                <button type="submit" class="w-full sm:w-auto px-8 py-4 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white rounded-xl font-semibold transition-all duration-300 shadow-lg shadow-primary-200 hover:shadow-xl hover:-translate-y-0.5">
                                    <i class="fas fa-upload mr-2"></i>
                                    Upload Image
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Right Column - Product Details -->
        <div class="xl:col-span-1 space-y-6">
            <!-- Product Info Card -->
            <div class="card rounded-2xl p-6 shadow-sm">
                <h1 class="text-3xl font-bold text-gray-900 mb-4 leading-tight">{{ $product->name }}</h1>
                
                <div class="flex flex-wrap gap-2 mb-6">
                    @if($product->category)
                        <span class="bg-gradient-to-r from-blue-100 to-indigo-100 text-blue-800 px-4 py-2 rounded-full text-sm font-medium flex items-center gap-2">
                            <i class="fas fa-folder-open"></i>
                            {{ $product->category->name }}
                        </span>
                    @endif
                    @if($product->brand)
                        <span class="bg-gradient-to-r from-purple-100 to-pink-100 text-purple-800 px-4 py-2 rounded-full text-sm font-medium flex items-center gap-2">
                            <i class="fas fa-tags"></i>
                            {{ $product->brand->name }}
                        </span>
                    @endif
                    @if($product->unit)
                        <span class="bg-gradient-to-r from-orange-100 to-amber-100 text-orange-800 px-4 py-2 rounded-full text-sm font-medium flex items-center gap-2">
                            <i class="fas fa-weight-hanging"></i>
                            {{ $product->unit->name }}
                        </span>
                    @endif
                </div>

                <div class="mb-6 p-6 bg-gradient-to-r from-primary-500 via-primary-600 to-primary-700 rounded-2xl text-white shadow-lg shadow-primary-200">
                    <p class="text-sm opacity-90 mb-1">Selling Price</p>
                    <p class="text-4xl font-bold tracking-tight">TZS {{ number_format($product->selling_price, 0) }}</p>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-2 gap-4 mb-6">
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 hover:shadow-md transition-all duration-200">
                        <p class="text-sm text-gray-600 mb-1 flex items-center gap-1">
                            <i class="fas fa-cubes text-gray-400"></i>
                            Stock Quantity
                        </p>
                        <p class="text-2xl font-bold text-gray-900">{{ $product->quantity }}</p>
                        @if($product->quantity <= $product->reorder_level)
                            <div class="mt-2 flex items-center gap-1 text-xs font-semibold text-red-600 bg-red-50 px-2 py-1 rounded-full">
                                <i class="fas fa-exclamation-triangle"></i>
                                Low Stock (Reorder: {{ $product->reorder_level }})
                            </div>
                        @endif
                    </div>
                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 hover:shadow-md transition-all duration-200">
                        <p class="text-sm text-gray-600 mb-1 flex items-center gap-1">
                            <i class="fas fa-dollar-sign text-gray-400"></i>
                            Cost Price
                        </p>
                        <p class="text-2xl font-bold text-gray-900">TZS {{ number_format($product->cost_price, 0) }}</p>
                        <div class="mt-2 text-xs font-medium text-gray-500">
                            Profit: TZS {{ number_format($product->selling_price - $product->cost_price, 0) }}
                        </div>
                    </div>
                </div>

                @if($product->description)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-3 flex items-center gap-2">
                            <i class="fas fa-info-circle text-primary-500"></i>
                            Description
                        </h3>
                        <p class="text-gray-600 leading-relaxed">{{ $product->description }}</p>
                    </div>
                @endif

                <!-- Additional Details -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                        <i class="fas fa-list-alt text-primary-500"></i>
                        Product Details
                    </h3>
                    <div class="space-y-4">
                        @if($product->sku)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                <span class="text-sm text-gray-600 font-medium flex items-center gap-2">
                                    <i class="fas fa-hashtag text-gray-400"></i>
                                    SKU
                                </span>
                                <span class="text-gray-900 font-semibold">{{ $product->sku }}</span>
                            </div>
                        @endif
                        @if($product->barcode)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                <span class="text-sm text-gray-600 font-medium flex items-center gap-2">
                                    <i class="fas fa-barcode text-gray-400"></i>
                                    Barcode
                                </span>
                                <span class="text-gray-900 font-semibold">{{ $product->barcode }}</span>
                            </div>
                        @endif
                        @if($product->expiry_date)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                <span class="text-sm text-gray-600 font-medium flex items-center gap-2">
                                    <i class="fas fa-calendar-alt text-gray-400"></i>
                                    Expiry Date
                                </span>
                                <span class="text-gray-900 font-semibold">{{ $product->expiry_date->format('M d, Y') }}</span>
                            </div>
                        @endif
                        @if($product->batch_number)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-xl">
                                <span class="text-sm text-gray-600 font-medium flex items-center gap-2">
                                    <i class="fas fa-layer-group text-gray-400"></i>
                                    Batch Number
                                </span>
                                <span class="text-gray-900 font-semibold">{{ $product->batch_number }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card rounded-2xl p-6 shadow-sm">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <i class="fas fa-bolt text-yellow-500"></i>
                    Quick Actions
                </h3>
                <div class="space-y-3">
                    <a href="{{ route('shop.product', $product) }}" target="_blank" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-md shadow-emerald-200">
                        <i class="fas fa-external-link-alt"></i>
                        View on Public Shop
                    </a>
                    <a href="{{ route('inventory.products.edit', $product) }}" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white rounded-xl font-semibold transition-all duration-200 shadow-md shadow-blue-200">
                        <i class="fas fa-edit"></i>
                        Edit Product Details
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Orders -->
    @if($product->onlineOrderItems->count() > 0)
        <div class="card rounded-2xl p-6 mt-6 shadow-sm">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6 gap-4">
                <h2 class="text-2xl font-bold text-gray-900 flex items-center gap-3">
                    <i class="fas fa-shopping-cart text-primary-500"></i>
                    Online Orders for this Product
                </h2>
                <span class="text-sm text-gray-500 bg-gray-100 px-4 py-2 rounded-full font-medium">
                    <i class="fas fa-list mr-2"></i>
                    {{ $product->onlineOrderItems->count() }} orders
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Order #</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Quantity</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($product->onlineOrderItems->sortByDesc('id') as $item)
                            <tr class="hover:bg-gray-50 transition-colors duration-150">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-bold text-gray-900">{{ $item->order->order_number }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <i class="fas fa-user mr-2 text-gray-400"></i>
                                    {{ $item->order->customer_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">
                                    <i class="fas fa-box mr-2 text-gray-400"></i>
                                    {{ $item->quantity }} pcs
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                                    TZS {{ number_format($item->total, 0) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full 
                                        @if($item->order->status === 'delivered') bg-green-100 text-green-800 
                                        @elseif($item->order->status === 'cancelled') bg-red-100 text-red-800 
                                        @elseif($item->order->status === 'in_transit') bg-blue-100 text-blue-800 
                                        @else bg-yellow-100 text-yellow-800 @endif">
                                        @if($item->order->status === 'delivered')
                                            <i class="fas fa-check-circle mr-1"></i>
                                        @elseif($item->order->status === 'cancelled')
                                            <i class="fas fa-times-circle mr-1"></i>
                                        @elseif($item->order->status === 'in_transit')
                                            <i class="fas fa-truck mr-1"></i>
                                        @else
                                            <i class="fas fa-clock mr-1"></i>
                                        @endif
                                        {{ ucwords(str_replace('_', ' ', $item->order->status)) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <i class="fas fa-calendar mr-2 text-gray-400"></i>
                                    {{ $item->order->created_at->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('online.orders.show', $item->order->id) }}" class="text-primary-600 hover:text-primary-800 font-semibold flex items-center gap-1 transition-colors">
                                        <i class="fas fa-eye"></i>
                                        View Order
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
