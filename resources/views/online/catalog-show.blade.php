@extends('layouts.app')

@section('page-title', $product->name)

@section('content')
@php
    // Host-relative storage URLs: images always load from whichever
    // host/port serves this page (local dev, admin domain, etc.).
    $resolveImageUrl = function ($path) {
        if (!$path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            $parsed = parse_url($path);
            $urlPath = isset($parsed['path']) ? ltrim($parsed['path'], '/') : '';
            if (str_starts_with($urlPath, 'storage/')) {
                return '/' . $urlPath;
            }
            return $path;
        }

        $cleanPath = ltrim($path, '/');
        if (!str_starts_with($cleanPath, 'storage/')) {
            $cleanPath = 'storage/' . $cleanPath;
        }

        return '/' . $cleanPath;
    };
@endphp
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <a href="{{ route('online.catalog') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Catalog
            </a>
            <form action="{{ route('online.catalog.toggle', $product->encrypted_key) }}" method="POST">
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
                    $imageToShow = $resolveImageUrl($primaryImage?->image_path) ?? $resolveImageUrl($product->image);
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
                                <div onclick="document.getElementById('main-image').src='{{ $resolveImageUrl($image->image_path) }}'" class="bg-gray-100 rounded-lg aspect-square flex items-center justify-center overflow-hidden cursor-pointer hover:ring-2 hover:ring-primary-500 @if($image->is_primary) ring-2 ring-primary-500 @endif">
                                    <img src="{{ $resolveImageUrl($image->image_path) }}" alt="{{ $product->name }}" class="max-h-full max-w-full object-contain">
                                </div>
                                @if(!$image->is_primary)
                                    <form action="{{ route('online.catalog.images.primary', [$product->encrypted_key, $image->encrypted_key]) }}" method="POST" class="absolute -top-2 -right-2">
                                        @csrf
                                        <button type="submit" class="bg-primary-600 text-white p-1 rounded-full text-xs" title="Set as primary">
                                            <i class="fas fa-star"></i>
                                        </button>
                                    </form>
                                @endif
                                <form action="{{ route('online.catalog.images.delete', [$product->encrypted_key, $image->encrypted_key]) }}" method="POST" class="absolute -bottom-2 -right-2">
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
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Add Images</h3>
                    <p class="text-xs text-gray-500 mb-3">Select multiple files at once, or take photos with the camera. Images are resized and saved as WebP automatically.</p>
                    <form id="multi-upload-form" action="{{ route('online.catalog.images.upload', $product->encrypted_key) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="flex flex-col sm:flex-row gap-3">
                            <label for="images-input" class="flex-1 cursor-pointer px-4 py-2 border border-dashed border-gray-300 rounded-lg text-sm text-gray-600 hover:border-primary-500 hover:text-primary-700 transition-colors text-center">
                                <i class="fas fa-images mr-2"></i><span id="images-input-label">Choose images (multiple allowed)</span>
                                <input id="images-input" type="file" name="images[]" accept="image/*" multiple class="hidden">
                            </label>
                            <button type="button" id="open-camera" class="px-4 py-2 bg-gray-800 hover:bg-gray-900 text-white rounded-lg text-sm transition-colors">
                                <i class="fas fa-camera mr-2"></i>Take photo
                            </button>
                        </div>
                        <div id="picked-previews" class="hidden grid-cols-4 gap-2 mt-3"></div>
                        <button type="submit" id="picked-upload-btn" class="hidden mt-3 px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors text-sm">
                            <i class="fas fa-upload mr-2"></i>Upload selected
                        </button>
                    </form>

                    {{-- Live camera modal --}}
                    <div id="camera-modal" class="hidden fixed inset-0 z-[9999] items-center justify-center p-4" style="background:rgba(0,0,0,0.7);">
                        <div class="bg-white rounded-2xl w-full max-w-lg overflow-hidden">
                            <div class="flex items-center justify-between px-4 py-3 border-b">
                                <h4 class="font-semibold text-gray-900"><i class="fas fa-camera mr-2"></i>Take photo</h4>
                                <button type="button" id="close-camera" class="text-gray-400 hover:text-gray-700 text-xl leading-none">&times;</button>
                            </div>
                            <div class="p-4">
                                <p id="camera-error" class="hidden mb-3 p-2 bg-red-100 text-red-700 text-sm rounded-lg"></p>
                                <video id="camera-video" autoplay playsinline muted class="w-full rounded-lg bg-black aspect-video object-cover"></video>
                                <canvas id="camera-canvas" class="hidden"></canvas>
                                <div id="captured-strip" class="hidden grid-cols-4 gap-2 mt-3"></div>
                                <div class="flex flex-wrap gap-2 mt-4">
                                    <button type="button" id="capture-photo" class="flex-1 px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm">
                                        <i class="fas fa-circle-dot mr-2"></i>Capture
                                    </button>
                                    <button type="button" id="upload-captured" class="hidden flex-1 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm">
                                        <i class="fas fa-upload mr-2"></i>Upload photos
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
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
                    @if($product->color)
                        <span class="bg-purple-100 text-purple-800 px-3 py-1 rounded-full text-sm"><i class="fas fa-palette mr-1"></i>{{ $product->color }}</span>
                    @endif
                    @if($product->variant)
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm"><i class="fas fa-tags mr-1"></i>{{ $product->variant }}</span>
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
                        <p class="text-gray-700 whitespace-pre-line">{{ $product->description }}</p>
                    </div>
                @endif

                @if($product->specifications)
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-2">Specifications</h3>
                        <p class="text-gray-700 whitespace-pre-line">{{ $product->specifications }}</p>
                    </div>
                @endif

                <div class="mb-6 border border-gray-200 rounded-xl overflow-hidden">
                    <button type="button" onclick="document.getElementById('details-form-wrap').classList.toggle('hidden')"
                        class="w-full flex items-center justify-between px-4 py-3 bg-gray-50 hover:bg-gray-100 transition-colors">
                        <span class="font-semibold text-gray-900"><i class="fas fa-pen-to-square mr-2 text-primary-600"></i>Edit Color / Variety / Description</span>
                        <i class="fas fa-chevron-down text-gray-400 text-sm"></i>
                    </button>
                    <div id="details-form-wrap" class="hidden p-4">
                        <form action="{{ route('online.catalog.details', $product->encrypted_key) }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Color</label>
                                    <input type="text" name="color" list="color-options" value="{{ old('color', $product->color) }}"
                                        placeholder="e.g. Red, Blue, Mixed"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <datalist id="color-options">
                                        @foreach($colorOptions ?? [] as $c)
                                            <option value="{{ $c }}"></option>
                                        @endforeach
                                    </datalist>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-1">Variety</label>
                                    <input type="text" name="variant" list="variant-options" value="{{ old('variant', $product->variant) }}"
                                        placeholder="e.g. 500g pack, Large, Vanilla"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                                    <datalist id="variant-options">
                                        @foreach($variantOptions ?? [] as $v)
                                            <option value="{{ $v }}"></option>
                                        @endforeach
                                    </datalist>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Description</label>
                                <textarea name="description" rows="3" placeholder="Customer-facing product description..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('description', $product->description) }}</textarea>
                            </div>
                            <div class="mb-4">
                                <label class="block text-sm font-semibold text-gray-700 mb-1">Specifications</label>
                                <textarea name="specifications" rows="3" placeholder="Size, weight, ingredients, usage..."
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('specifications', $product->specifications) }}</textarea>
                            </div>
                            <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm transition-colors">
                                <i class="fas fa-save mr-2"></i>Save details
                            </button>
                        </form>
                    </div>
                </div>

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

<script>
(function () {
    const uploadUrl = @json(route('online.catalog.images.upload', $product->encrypted_key));
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

    // ---- Multi-file picker previews ----
    const imagesInput = document.getElementById('images-input');
    const imagesLabel = document.getElementById('images-input-label');
    const pickedPreviews = document.getElementById('picked-previews');
    const pickedBtn = document.getElementById('picked-upload-btn');

    imagesInput?.addEventListener('change', () => {
        const files = Array.from(imagesInput.files || []);
        pickedPreviews.innerHTML = '';
        if (files.length === 0) {
            pickedPreviews.classList.add('hidden');
            pickedPreviews.classList.remove('grid');
            pickedBtn.classList.add('hidden');
            imagesLabel.textContent = 'Choose images (multiple allowed)';
            return;
        }
        imagesLabel.textContent = files.length + ' image(s) selected';
        files.slice(0, 10).forEach(f => {
            const img = document.createElement('img');
            img.src = URL.createObjectURL(f);
            img.className = 'w-full aspect-square object-cover rounded-lg bg-gray-100';
            pickedPreviews.appendChild(img);
        });
        pickedPreviews.classList.remove('hidden');
        pickedPreviews.classList.add('grid');
        pickedBtn.classList.remove('hidden');
    });

    // ---- Live camera ----
    const modal = document.getElementById('camera-modal');
    const video = document.getElementById('camera-video');
    const canvas = document.getElementById('camera-canvas');
    const errBox = document.getElementById('camera-error');
    const strip = document.getElementById('captured-strip');
    const uploadCapturedBtn = document.getElementById('upload-captured');
    let stream = null;
    let shots = [];

    function showError(msg) {
        errBox.textContent = msg;
        errBox.classList.remove('hidden');
    }

    async function openCamera() {
        errBox.classList.add('hidden');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        if (!navigator.mediaDevices?.getUserMedia) {
            showError('Camera is not supported in this browser. Use "Choose images" instead.');
            return;
        }
        try {
            stream = await navigator.mediaDevices.getUserMedia({
                video: { facingMode: 'environment', width: { ideal: 1920 }, height: { ideal: 1080 } },
                audio: false,
            });
            video.srcObject = stream;
            await video.play().catch(() => {});
        } catch (e) {
            showError('Could not access the camera (' + (e.name || 'denied') + '). Check permission or use "Choose images".');
        }
    }

    function closeCamera() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        if (stream) {
            stream.getTracks().forEach(t => t.stop());
            stream = null;
        }
        video.srcObject = null;
    }

    function renderStrip() {
        strip.innerHTML = '';
        shots.forEach((shot, i) => {
            const wrap = document.createElement('div');
            wrap.className = 'relative';
            const img = document.createElement('img');
            img.src = shot.url;
            img.className = 'w-full aspect-square object-cover rounded-lg bg-gray-100';
            const del = document.createElement('button');
            del.type = 'button';
            del.className = 'absolute -top-2 -right-2 bg-red-600 text-white rounded-full w-6 h-6 text-xs';
            del.innerHTML = '<i class="fas fa-times"></i>';
            del.title = 'Remove';
            del.addEventListener('click', () => {
                URL.revokeObjectURL(shots[i].url);
                shots.splice(i, 1);
                renderStrip();
            });
            wrap.appendChild(img);
            wrap.appendChild(del);
            strip.appendChild(wrap);
        });
        const has = shots.length > 0;
        strip.classList.toggle('hidden', !has);
        strip.classList.toggle('grid', has);
        uploadCapturedBtn.classList.toggle('hidden', !has);
    }

    document.getElementById('open-camera')?.addEventListener('click', openCamera);
    document.getElementById('close-camera')?.addEventListener('click', closeCamera);
    modal?.addEventListener('click', (e) => { if (e.target === modal) closeCamera(); });

    document.getElementById('capture-photo')?.addEventListener('click', () => {
        if (!stream || video.videoWidth === 0) {
            showError('Camera is not ready yet. Please wait a moment and try again.');
            return;
        }
        const maxSide = 1600;
        const scale = Math.min(1, maxSide / Math.max(video.videoWidth, video.videoHeight));
        canvas.width = Math.round(video.videoWidth * scale);
        canvas.height = Math.round(video.videoHeight * scale);
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
        canvas.toBlob((blob) => {
            if (!blob) {
                showError('Failed to capture the photo. Try again.');
                return;
            }
            shots.push({ blob, url: URL.createObjectURL(blob) });
            renderStrip();
        }, 'image/webp', 0.82);
    });

    uploadCapturedBtn?.addEventListener('click', async () => {
        if (shots.length === 0) return;
        uploadCapturedBtn.disabled = true;
        uploadCapturedBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Uploading...';
        try {
            const form = new FormData();
            shots.forEach((shot, i) => form.append('images[]', shot.blob, 'camera-' + Date.now() + '-' + i + '.webp'));
            const res = await fetch(uploadUrl, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'text/html' },
                body: form,
            });
            if (!res.ok) throw new Error('Upload failed (' + res.status + ')');
            window.location.reload();
        } catch (e) {
            showError(e.message + '. Please try again.');
            uploadCapturedBtn.disabled = false;
            uploadCapturedBtn.innerHTML = '<i class="fas fa-upload mr-2"></i>Upload photos';
        }
    });
})();
</script>
@endsection