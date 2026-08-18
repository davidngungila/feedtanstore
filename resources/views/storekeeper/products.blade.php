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
                <input type="text" id="searchInput" placeholder="Search products..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" oninput="filterProducts()">
            </div>
            <select id="categoryFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500" onchange="filterProducts()">
                <option value="">All Categories</option>
                @foreach($categories as $category)
                    <option value="{{ $category->name }}">{{ $category->name }}</option>
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
                    @foreach($products as $product)
                    <tr class="hover:bg-gray-50 product-row" data-search="{{ strtolower($product->name . ' ' . $product->sku . ' ' . ($product->barcode ?? '') . ' ' . ($product->category->name ?? '')) }}" data-category="{{ $product->category->name ?? '' }}">
                        <td class="px-6 py-4">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-primary-100 flex items-center justify-center">
                                        <i class="fas fa-box text-primary-600"></i>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="font-semibold text-gray-900">{{ $product->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $product->barcode }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $product->category->name ?? 'N/A' }}</td>
                        <td class="px-6 py-4 text-sm text-gray-600">{{ $product->sku }}</td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full 
                                {{ $product->quantity <= 0 ? 'bg-red-100 text-red-700' : 
                                   ($product->quantity <= 10 ? 'bg-yellow-100 text-yellow-700' : 
                                   'bg-green-100 text-green-700') }}">
                                {{ $product->quantity }} {{ $product->unit?->short_name ?? 'pcs' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            <a href="{{ route('storekeeper.products.show', $product->id) }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $products->links() }}
        </div>
    </div>
</div>

<script>
function filterProducts() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const category = document.getElementById('categoryFilter').value;
    const rows = document.querySelectorAll('.product-row');
    rows.forEach(row => {
        const text = row.getAttribute('data-search');
        const cat = row.getAttribute('data-category');
        const matchSearch = !search || text.includes(search);
        const matchCategory = !category || cat === category;
        row.style.display = (matchSearch && matchCategory) ? '' : 'none';
    });
}
</script>
@endsection
