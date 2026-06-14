@extends('layouts.app')

@section('page-title', 'Product Barcodes')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Product Barcodes</h2>
            @if(count($products) > 0)
                <form action="{{ route('inventory.barcodes.print-all') }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-print mr-2"></i>Print All Barcodes
                    </button>
                </form>
            @endif
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
                        </tr>
                    </thead>
                    <tbody>
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
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.product-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
</script>
@endsection
