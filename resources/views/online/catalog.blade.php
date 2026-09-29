@extends('layouts.app')

@section('page-title', 'Product Catalog')

@section('content')
@php
    $settings = \App\Models\StoreSetting::firstOrCreate();
    $baseUrl = $settings->store_url ?? config('app.url');
    $resolveImageUrl = function ($path) use ($baseUrl) {
        if (!$path) {
            return null;
        }

        // If it's already a full URL, clean it to extract the path
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            // Parse URL to get path
            $parsed = parse_url($path);
            if (isset($parsed['path'])) {
                $path = ltrim($parsed['path'], '/');
                // If path starts with storage/, use it directly
                if (str_starts_with($path, 'storage/')) {
                    return rtrim($baseUrl, '/') . '/' . $path;
                }
                return rtrim($baseUrl, '/') . '/storage/' . $path;
            }
        }

        $cleanPath = ltrim($path, '/');
        if (str_starts_with($cleanPath, 'storage/')) {
            return rtrim($baseUrl, '/') . '/' . $cleanPath;
        }

        return rtrim($baseUrl, '/') . '/storage/' . $cleanPath;
    };
@endphp
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        @php
            $totalCount = $products->count();
            $onlineCount = $products->where('is_available_online', true)->count();
            $offlineCount = $totalCount - $onlineCount;
        @endphp
        <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
            <div>
                <h2 class="text-xl font-bold text-primary-900">Product Catalog</h2>
                <p class="text-sm text-gray-500 mt-1">
                    Total: <span class="font-semibold text-gray-800">{{ $totalCount }}</span>
                    <span class="mx-1">•</span> Online: <span class="font-semibold text-green-700">{{ $onlineCount }}</span>
                    <span class="mx-1">•</span> Offline: <span class="font-semibold text-red-700">{{ $offlineCount }}</span>
                </p>
            </div>
            <a href="{{ route('online.catalog') }}" class="text-sm text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-rotate-right mr-1"></i>Refresh
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-800 rounded-lg">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Bulk actions toolbar --}}
        <div class="flex flex-wrap items-center gap-2 mb-5 p-3 bg-gray-50 border border-gray-200 rounded-xl">
            <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer select-none">
                <input type="checkbox" id="select-all-products" class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                Select all
            </label>
            <span id="selected-count" class="text-xs text-gray-500">0 selected</span>
            <div class="flex flex-wrap items-center gap-2 ml-auto">
                <button type="submit" form="bulk-selected-form" name="action" value="activate" id="btn-activate-selected"
                    class="px-3 py-2 text-xs font-semibold rounded-lg bg-green-600 hover:bg-green-700 text-white transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    <i class="fas fa-eye mr-1"></i>Activate selected
                </button>
                <button type="submit" form="bulk-selected-form" name="action" value="deactivate" id="btn-deactivate-selected"
                    class="px-3 py-2 text-xs font-semibold rounded-lg bg-gray-600 hover:bg-gray-700 text-white transition-colors disabled:opacity-50 disabled:cursor-not-allowed" disabled>
                    <i class="fas fa-eye-slash mr-1"></i>Deactivate selected
                </button>
                <span class="hidden sm:inline text-gray-300">|</span>
                <form action="{{ route('online.catalog.bulk-toggle') }}" method="POST" class="inline"
                    onsubmit="return confirm('Activate ALL {{ $totalCount }} products for online shop?');">
                    @csrf
                    <input type="hidden" name="action" value="activate">
                    <button type="submit" class="px-3 py-2 text-xs font-semibold rounded-lg bg-emerald-100 hover:bg-emerald-200 text-emerald-800 transition-colors">
                        <i class="fas fa-bolt mr-1"></i>Activate all ({{ $totalCount }})
                    </button>
                </form>
                <form action="{{ route('online.catalog.bulk-toggle') }}" method="POST" class="inline"
                    onsubmit="return confirm('Deactivate ALL {{ $totalCount }} products from online shop?');">
                    @csrf
                    <input type="hidden" name="action" value="deactivate">
                    <button type="submit" class="px-3 py-2 text-xs font-semibold rounded-lg bg-red-100 hover:bg-red-200 text-red-800 transition-colors">
                        <i class="fas fa-ban mr-1"></i>Deactivate all ({{ $totalCount }})
                    </button>
                </form>
            </div>
        </div>

        {{-- Hidden form for selected-ids bulk action (checkboxes attach via form="bulk-selected-form" to avoid nested <form>) --}}
        <form id="bulk-selected-form" action="{{ route('online.catalog.bulk-toggle') }}" method="POST"
            onsubmit="return confirmBulkSelected(event);" class="hidden">
            @csrf
        </form>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @foreach($products as $product)
            <div class="border rounded-lg p-4 hover:shadow-lg transition-shadow relative">
                <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" form="bulk-selected-form"
                    class="bulk-checkbox absolute top-3 left-3 w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 bg-white shadow">
                <a href="{{ route('online.catalog.show', $product) }}" class="block">
                    <div class="h-40 bg-gray-100 rounded-lg mb-3 flex items-center justify-center overflow-hidden">
                        @php
                            $primaryImage = $product->images->firstWhere('is_primary', true);
                            $imageToShow = $resolveImageUrl($primaryImage?->image_path) ?? $resolveImageUrl($product->image);
                        @endphp
                        @if($imageToShow)
                            <img src="{{ $imageToShow }}" alt="{{ $product->name }}" class="max-h-full max-w-full object-contain">
                        @else
                            <i class="fas fa-box text-4xl text-gray-400"></i>
                        @endif
                    </div>
                </a>
                <div class="flex justify-between items-start gap-2 mb-2 pl-6">
                    <a href="{{ route('online.catalog.show', $product) }}" class="font-semibold text-primary-900 hover:text-primary-700">{{ $product->name }}</a>
                    <form action="{{ route('online.catalog.toggle', $product) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-2 py-1 text-xs rounded-full
                            @if($product->is_available_online) bg-green-100 text-green-800 hover:bg-green-200 @else bg-red-100 text-red-800 hover:bg-red-200 @endif">
                            {{ $product->is_available_online ? 'Online' : 'Offline' }}
                        </button>
                    </form>
                </div>
                @if($product->category)
                    <p class="text-sm text-gray-500 mb-2 pl-6">{{ $product->category->name }}</p>
                @endif
                <p class="text-lg font-bold text-primary-600 mb-2 pl-6">TZS {{ number_format($product->selling_price, 2) }}</p>
                <div class="flex justify-between items-center text-sm pl-6">
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

<script>
(function () {
    const selectAll = document.getElementById('select-all-products');
    const checkboxes = () => Array.from(document.querySelectorAll('.bulk-checkbox'));
    const countEl = document.getElementById('selected-count');
    const btnActivate = document.getElementById('btn-activate-selected');
    const btnDeactivate = document.getElementById('btn-deactivate-selected');

    function refresh() {
        const boxes = checkboxes();
        const selected = boxes.filter(b => b.checked).length;
        countEl.textContent = selected + ' selected';
        const hasAny = selected > 0;
        btnActivate.disabled = !hasAny;
        btnDeactivate.disabled = !hasAny;
        if (selectAll) {
            selectAll.checked = boxes.length > 0 && selected === boxes.length;
            selectAll.indeterminate = selected > 0 && selected < boxes.length;
        }
    }

    if (selectAll) {
        selectAll.addEventListener('change', () => {
            checkboxes().forEach(b => { b.checked = selectAll.checked; });
            refresh();
        });
    }
    document.querySelectorAll('.bulk-checkbox').forEach(b => b.addEventListener('change', refresh));
    refresh();

    window.confirmBulkSelected = function (e) {
        const n = checkboxes().filter(b => b.checked).length;
        if (n === 0) {
            alert('Select at least one product first (or use Activate all / Deactivate all).');
            return false;
        }
        const action = e.submitter?.value === 'deactivate' ? 'deactivate' : 'activate';
        return confirm((action === 'deactivate' ? 'Deactivate ' : 'Activate ') + n + ' selected product(s)?');
    };
})();
</script>
@endsection