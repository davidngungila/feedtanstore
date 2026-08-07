@extends('layouts.app')

@section('page-title', 'Product Details')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="mb-6">
        <a href="{{ route('storekeeper.products') }}" class="text-primary-600 hover:text-primary-700 text-sm font-medium">
            <i class="fas fa-arrow-left mr-2"></i>Back to Products
        </a>
    </div>

    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-start justify-between mb-6">
            <div class="flex items-start">
                <div class="flex-shrink-0 h-20 w-20 bg-primary-100 rounded-xl flex items-center justify-center mr-4">
                    <i class="fas fa-box text-3xl text-primary-600"></i>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-primary-900">{{ $product->name }}</h1>
                    <p class="text-gray-600">{{ $product->sku }}</p>
                </div>
            </div>
            <button onclick="printBarcode()" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                <i class="fas fa-print mr-2"></i>Print Barcode
            </button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">Category</p>
                <p class="font-semibold">{{ $product->category->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Brand</p>
                <p class="font-semibold">{{ $product->brand->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Unit</p>
                <p class="font-semibold">{{ $product->unit->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Barcode</p>
                <p class="font-semibold font-mono">{{ $product->barcode }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Stock Quantity</p>
                <p class="font-semibold">{{ $product->quantity }} {{ $product->unit?->short_name ?? 'pcs' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Reorder Level</p>
                <p class="font-semibold">{{ $product->reorder_level ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Status</p>
                <span class="px-2 py-1 text-xs font-semibold rounded-full {{ $product->is_active ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-700' }}">
                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            @if($product->expiry_date)
            <div>
                <p class="text-sm text-gray-500 mb-1">Expiry Date</p>
                <p class="font-semibold">{{ $product->expiry_date->format('M d, Y') }}</p>
            </div>
            @endif
        </div>

        @if($product->description)
        <div class="mt-6">
            <p class="text-sm text-gray-500 mb-1">Description</p>
            <p class="text-gray-600">{{ $product->description }}</p>
        </div>
        @endif
    </div>

    <!-- Barcode Section for Printing -->
    <div id="barcodeSection" class="card rounded-2xl p-6 mb-6">
        <h2 class="text-lg font-bold text-primary-900 mb-4">Barcode</h2>
        <div class="flex items-center justify-center p-8 bg-white border-2 border-dashed border-gray-300 rounded-lg">
            <div class="text-center">
                <div class="text-6xl font-mono font-bold text-gray-900 mb-2">{{ $product->barcode }}</div>
                <div class="text-xl font-semibold text-gray-700">{{ $product->name }}</div>
                <div class="text-sm text-gray-500 mt-1">{{ $product->sku }}</div>
            </div>
        </div>
    </div>

    <!-- Stock Information -->
    <div class="card rounded-2xl p-6">
        <h2 class="text-lg font-bold text-primary-900 mb-4">Stock Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-600">Current Stock</p>
                <p class="text-2xl font-bold {{ $product->quantity <= 0 ? 'text-red-600' : ($product->quantity <= 10 ? 'text-yellow-600' : 'text-green-600') }}">
                    {{ $product->quantity }} {{ $product->unit?->short_name ?? 'pcs' }}
                </p>
            </div>
            <div class="bg-gray-50 rounded-lg p-4">
                <p class="text-sm text-gray-600">Reorder Level</p>
                <p class="text-2xl font-bold text-gray-900">{{ $product->reorder_level ?? 'N/A' }}</p>
            </div>
        </div>
    </div>
</div>

<script>
function printBarcode() {
    const barcodeSection = document.getElementById('barcodeSection');
    const printWindow = window.open('', '_blank');
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Barcode - {{ $product->name }}</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    min-height: 100vh;
                    margin: 0;
                    padding: 20px;
                }
                .barcode-container {
                    text-align: center;
                    border: 2px solid #000;
                    padding: 20px;
                    width: 300px;
                }
                .barcode {
                    font-family: 'Courier New', monospace;
                    font-size: 24px;
                    font-weight: bold;
                    margin-bottom: 10px;
                }
                .product-name {
                    font-size: 18px;
                    font-weight: bold;
                    margin-bottom: 5px;
                }
                .sku {
                    font-size: 14px;
                    color: #666;
                }
            </style>
        </head>
        <body>
            <div class="barcode-container">
                <div class="barcode">{{ $product->barcode }}</div>
                <div class="product-name">{{ $product->name }}</div>
                <div class="sku">{{ $product->sku }}</div>
            </div>
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.print();
}
</script>
@endsection
