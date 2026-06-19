@extends('layouts.app')

@section('page-title', 'Barcode Bulk')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex flex-col md:flex-row items-center justify-between mb-6 gap-4">
            <h2 class="text-xl font-bold text-primary-900">Barcode Bulk</h2>
            <div class="flex flex-wrap items-center gap-4">
                <div class="relative">
                    <form action="{{ route('inventory.barcodes') }}" method="GET">
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search products..." class="w-full md:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400"></i>
                    </form>
                </div>
                @if(count($products) > 0)
                    <form action="{{ route('inventory.barcodes.print-all') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                            <i class="fas fa-print mr-2"></i>Print All Barcodes
                        </button>
                    </form>
                @endif
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('inventory.barcodes.print') }}" method="POST" id="barcodeForm">
            @csrf
            <div class="mb-4 flex flex-wrap items-center gap-4">
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="selectAll" class="w-5 h-5 text-primary-600 rounded">
                    <label for="selectAll" class="text-sm font-medium text-gray-700">Select All</label>
                </div>
                <div class="flex items-center gap-2">
                    <label for="quantityAll" class="text-sm font-medium text-gray-700">Quantity for All:</label>
                    <input type="number" id="quantityAll" min="1" value="1" class="w-20 px-2 py-1 border border-gray-300 rounded">
                    <button type="button" id="applyQuantityAll" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-3 py-1 rounded-lg text-sm font-medium transition-colors">
                        Apply
                    </button>
                </div>
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-print mr-2"></i>Print Selected Barcodes
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="data-table w-full">
                    <thead>
                        <tr>
                            <th class="text-left">Select</th>
                            <th class="text-left">Name</th>
                            <th class="text-left">SKU</th>
                            <th class="text-left">Barcode</th>
                            <th class="text-left">Category</th>
                            <th class="text-left">Selling Price</th>
                            <th class="text-left">Quantity</th>
                        </tr>
                    </thead>
                    <tbody id="barcode-table-body">
                        @foreach($products as $product)
                        <tr>
                            <td>
                                <input type="checkbox" name="product_ids[]" value="{{ $product->id }}" class="product-checkbox w-5 h-5 text-primary-600 rounded">
                            </td>
                            <td class="font-medium text-primary-900">{{ $product->name }}</td>
                            <td class="text-gray-600">{{ $product->sku ?? '-' }}</td>
                            <td class="text-gray-600">{{ $product->barcode ?? '-' }}</td>
                            <td class="text-gray-600">{{ $product->category->name ?? '-' }}</td>
                            <td class="text-gray-600">TZS {{ number_format($product->selling_price, 2) }}</td>
                            <td>
                                <input type="number" name="quantities[{{ $product->id }}]" min="1" value="1" class="product-quantity w-20 px-2 py-1 border border-gray-300 rounded">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<script>
    // Select All functionality
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.product-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Update select all when individual checkboxes change
    document.querySelectorAll('.product-checkbox').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(document.querySelectorAll('.product-checkbox')).every(cb => cb.checked);
            document.getElementById('selectAll').checked = allChecked;
        });
    });

    // Apply quantity to all
    document.getElementById('applyQuantityAll').addEventListener('click', function() {
        const quantity = parseInt(document.getElementById('quantityAll').value) || 1;
        const quantityInputs = document.querySelectorAll('.product-quantity');
        quantityInputs.forEach(input => {
            input.value = quantity;
        });
    });
</script>
@endsection
