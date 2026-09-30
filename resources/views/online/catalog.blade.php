@extends('layouts.app')

@section('page-title', 'Product Catalog')

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

        {{-- Live search (filters as you type) --}}
        <div class="relative mb-4">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-gray-400 pointer-events-none">
                <i class="fas fa-search"></i>
            </span>
            <input type="text" id="catalog-search" placeholder="Search products by name, category or price..."
                autocomplete="off"
                class="w-full pl-10 pr-10 py-2.5 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
            <button type="button" id="catalog-search-clear" class="absolute inset-y-0 right-0 hidden items-center pr-3 text-gray-400 hover:text-gray-600" title="Clear search">
                <i class="fas fa-times-circle"></i>
            </button>
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
            <span id="visible-count" class="text-xs text-gray-400"></span>
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
            <div class="product-card border rounded-lg p-4 hover:shadow-lg transition-shadow relative"
                data-search="{{ strtolower($product->name . ' ' . ($product->category?->name ?? '') . ' ' . $product->selling_price) }}">
                <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" form="bulk-selected-form"
                    class="bulk-checkbox absolute top-3 left-3 w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500 bg-white shadow">
                <a href="{{ route('online.catalog.show', $product->encrypted_key) }}" class="block">
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
                    <a href="{{ route('online.catalog.show', $product->encrypted_key) }}" class="font-semibold text-primary-900 hover:text-primary-700">{{ $product->name }}</a>
                    <form action="{{ route('online.catalog.toggle', $product->encrypted_key) }}" method="POST">
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
        <div id="catalog-empty" class="hidden text-center py-12 text-gray-500">
            <i class="fas fa-search text-4xl text-gray-300 mb-3"></i>
            <p class="font-medium">No products match "<span id="catalog-empty-term"></span>"</p>
            <p class="text-sm mt-1">Try a different name, category or price.</p>
        </div>
    </div>
</div>

<script>
(function () {
    const selectAll = document.getElementById('select-all-products');
    const checkboxes = () => Array.from(document.querySelectorAll('.bulk-checkbox'));
    const cards = () => Array.from(document.querySelectorAll('.product-card'));
    const countEl = document.getElementById('selected-count');
    const visibleEl = document.getElementById('visible-count');
    const btnActivate = document.getElementById('btn-activate-selected');
    const btnDeactivate = document.getElementById('btn-deactivate-selected');
    const searchInput = document.getElementById('catalog-search');
    const searchClear = document.getElementById('catalog-search-clear');
    const emptyBox = document.getElementById('catalog-empty');
    const emptyTerm = document.getElementById('catalog-empty-term');
    const totalCards = cards().length;

    const isVisible = (card) => card.style.display !== 'none';
    const visibleBoxes = () => checkboxes().filter(b => {
        const card = b.closest('.product-card');
        return !card || isVisible(card);
    });

    function refresh() {
        const boxes = checkboxes();
        const visible = visibleBoxes();
        const selected = boxes.filter(b => b.checked).length;
        countEl.textContent = selected + ' selected';
        const hasAny = selected > 0;
        btnActivate.disabled = !hasAny;
        btnDeactivate.disabled = !hasAny;
        if (selectAll) {
            const visibleSelected = visible.filter(b => b.checked).length;
            selectAll.checked = visible.length > 0 && visibleSelected === visible.length;
            selectAll.indeterminate = visibleSelected > 0 && visibleSelected < visible.length;
        }
    }

    function applyFilter() {
        const term = searchInput.value.trim().toLowerCase();
        let shown = 0;
        cards().forEach(card => {
            const hay = (card.dataset.search || '');
            const show = term === '' || hay.includes(term);
            card.style.display = show ? '' : 'none';
            if (show) shown++;
        });
        emptyBox.classList.toggle('hidden', shown > 0);
        emptyTerm.textContent = searchInput.value.trim();
        visibleEl.textContent = term === '' ? '' : (shown + ' of ' + totalCards + ' shown');
        const hasTerm = term !== '';
        searchClear.classList.toggle('hidden', !hasTerm);
        searchClear.classList.toggle('flex', hasTerm);
        refresh();
    }

    if (selectAll) {
        selectAll.addEventListener('change', () => {
            // Select-all only affects the currently visible (filtered) cards.
            visibleBoxes().forEach(b => { b.checked = selectAll.checked; });
            refresh();
        });
    }
    document.querySelectorAll('.bulk-checkbox').forEach(b => b.addEventListener('change', refresh));
    searchInput.addEventListener('input', applyFilter);
    searchClear.addEventListener('click', () => {
        searchInput.value = '';
        applyFilter();
        searchInput.focus();
    });
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