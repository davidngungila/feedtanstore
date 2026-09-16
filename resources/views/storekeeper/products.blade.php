@extends('layouts.app')

@section('page-title', 'Products Management')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-primary-900">Products Management</h1>
        <p class="text-gray-600">Manage store inventory and products</p>
    </div>

    <!-- Search and Filter -->
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" id="searchInput" value="{{ $search ?? '' }}" placeholder="Search products..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
            </div>
            <select id="categoryFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500">
                <option value="">All Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->name }}" {{ $selectedCategory === $cat->name ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Products Table -->
    <div class="card rounded-2xl overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Category</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200" id="productsTableBody">
                    @include('storekeeper.partials._product_rows', ['products' => $products])
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200" id="productsPagination">
            {{ $products->links() }}
        </div>
    </div>
</div>

<script>
    const searchInput = document.getElementById('searchInput');
    const categoryFilter = document.getElementById('categoryFilter');
    const tableBody = document.getElementById('productsTableBody');

    let searchTimer;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => performSearch(), 150);
    });
    categoryFilter.addEventListener('change', performSearch);

    function performSearch() {
        const url = new URL('{{ route('storekeeper.products') }}');
        if (searchInput.value.trim()) url.searchParams.set('search', searchInput.value.trim());
        if (categoryFilter.value) url.searchParams.set('category', categoryFilter.value);
        history.replaceState(null, '', url);

        url.searchParams.set('fetch', '1');
        fetch(url, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(data => {
            tableBody.innerHTML = data.rows_html;
            document.getElementById('productsPagination').innerHTML = data.pagination_html;
        })
        .catch(() => {});
    }
</script>
@endsection
