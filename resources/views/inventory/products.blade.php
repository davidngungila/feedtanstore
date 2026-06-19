@extends('layouts.app')

@section('page-title', 'Products')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
            <h2 class="text-xl font-bold text-primary-900">Products</h2>
            <div class="flex flex-col md:flex-row items-center gap-3 w-full md:w-auto">
                <form action="{{ route('inventory.products') }}" method="GET" class="w-full md:w-64">
                    <div class="relative">
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search products..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary-600">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                <a href="{{ route('inventory.barcodes') }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors whitespace-nowrap">
                    <i class="fas fa-barcode mr-2"></i>Barcode Bulk
                </a>
                <a href="{{ route('inventory.products.create') }}" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors whitespace-nowrap">
                    <i class="fas fa-plus mr-2"></i>Add Product
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if($lowStockCount > 0)
            <div class="mb-4 p-4 bg-yellow-50 border border-yellow-300 rounded-xl flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-yellow-200 flex items-center justify-center text-yellow-700">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-yellow-800">Low Stock Alert</h4>
                        <p class="text-xs text-yellow-700">{{ $lowStockCount }} product(s) are running low and need reordering.</p>
                    </div>
                </div>
                <a href="{{ route('inventory.low-stock') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i class="fa-solid fa-eye"></i> View Details
                </a>
            </div>
        @endif

        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th class="text-left">
                            <input type="checkbox" id="select-all-products" class="w-4 h-4 text-primary-600">
                        </th>
                        <th class="text-left">Name</th>
                        <th class="text-left">SKU</th>
                        <th class="text-left">Category</th>
                        <th class="text-left">Brand</th>
                        <th class="text-left">Quantity</th>
                        <th class="text-left">Cost Price</th>
                        <th class="text-left">Selling Price</th>
                        <th class="text-left">Status</th>
                        <th class="text-left">Actions</th>
                    </tr>
                </thead>
                <tbody id="products-table-body">
                    @foreach($products as $product)
                    <tr data-search="{{ strtolower($product->name . ' ' . ($product->sku ?? '') . ' ' . ($product->barcode ?? '') . ' ' . ($product->category->name ?? '') . ' ' . ($product->brand->name ?? '')) }}">
                        <td class="text-left">
                            <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" form="barcode-bulk-form" class="product-checkbox w-4 h-4 text-primary-600">
                        </td>
                        <td class="font-medium text-primary-900">
                            <a href="{{ route('inventory.products.show', $product) }}" class="hover:underline">{{ $product->name }}</a>
                        </td>
                        <td class="text-gray-600">{{ $product->sku ?? '-' }}</td>
                        <td class="text-gray-600">{{ $product->category->name ?? '-' }}</td>
                        <td class="text-gray-600">{{ $product->brand->name ?? '-' }}</td>
                        <td class="font-semibold {{ $product->quantity <= $product->reorder_level ? 'text-red-600' : 'text-primary-900' }}">
                            {{ $product->quantity }} {{ $product->unit->short_name ?? '' }}
                        </td>
                        <td class="text-gray-600">TZS {{ number_format($product->cost_price, 2) }}</td>
                        <td class="text-gray-600">TZS {{ number_format($product->selling_price, 2) }}</td>
                        <td>
                            <span class="badge {{ $product->is_active ? 'badge-green' : 'badge-gray' }}">
                                {{ $product->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="flex items-center gap-2">
                            <a href="{{ route('inventory.products.show', $product) }}" class="text-primary-600 hover:text-primary-800 p-1" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="{{ route('inventory.products.edit', $product) }}" class="text-primary-600 hover:text-primary-800 p-1" title="Edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('inventory.products.destroy', $product) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this product?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 p-1" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <script>
            const searchInput = document.querySelector('input[name="search"]');
            const tableBody = document.getElementById('products-table-body');
            const rows = tableBody.querySelectorAll('tr');
            const selectAllCheckbox = document.getElementById('select-all-products');
            const productCheckboxes = document.querySelectorAll('.product-checkbox');

            // Search functionality
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase().trim();
                
                rows.forEach(row => {
                    const searchData = row.getAttribute('data-search');
                    if (searchData.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });

            // Select all functionality
            selectAllCheckbox.addEventListener('change', function() {
                productCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

            // Update select all when individual checkboxes change
            productCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const allChecked = Array.from(productCheckboxes).every(cb => cb.checked);
                    selectAllCheckbox.checked = allChecked;
                });
            });
        </script>
    </div>
</div>
@endsection