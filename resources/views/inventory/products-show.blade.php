@extends('layouts.app')

@section('page-title', $product->name)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">{{ $product->name }}</h2>
            <div class="flex gap-3">
                <a href="{{ route('inventory.products.edit', $product) }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Edit
                </a>
                <a href="{{ route('inventory.products') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Products
                </a>
            </div>
        </div>

        <!-- Product Info -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
            <div>
                <span class="text-sm text-gray-600">Status:</span>
                <span class="ml-2 px-3 py-1 rounded-full text-xs font-semibold {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ $product->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div>
                <span class="text-sm text-gray-600">SKU:</span>
                <span class="ml-2">{{ $product->sku ?? '-' }}</span>
            </div>
            <div>
                <span class="text-sm text-gray-600">Barcode:</span>
                <span class="ml-2">{{ $product->barcode ?? '-' }}</span>
            </div>
        </div>

        <!-- Details -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6 p-4 bg-gray-50 rounded-lg">
            <div>
                <h4 class="font-semibold text-primary-900 mb-2">Basic Info</h4>
                <p class="mb-1"><strong>Category:</strong> {{ $product->category->name ?? '-' }}</p>
                <p class="mb-1"><strong>Brand:</strong> {{ $product->brand->name ?? '-' }}</p>
                <p class="mb-1"><strong>Unit:</strong> {{ $product->unit->name ?? '-' }}</p>
                <p class="mb-1"><strong>Quantity in Stock:</strong> {{ $product->quantity }}</p>
                <p class="mb-1"><strong>Reorder Level:</strong> {{ $product->reorder_level }}</p>
            </div>
            <div>
                <h4 class="font-semibold text-primary-900 mb-2">Pricing</h4>
                <p class="mb-1"><strong>Cost Price:</strong> TZS {{ number_format($product->cost_price, 2) }}</p>
                <p class="mb-1"><strong>Selling Price:</strong> TZS {{ number_format($product->selling_price, 2) }}</p>
                <p class="mb-1"><strong>Expiry Date:</strong> {{ $product->expiry_date ? date('M d, Y', strtotime($product->expiry_date)) : '-' }}</p>
                <p class="mb-1"><strong>Available Online:</strong> {{ ($product->is_available_online ?? false) ? 'Yes' : 'No' }}</p>
            </div>
        </div>

        @if($product->description)
        <div class="p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded mb-6">
            <h4 class="font-semibold text-yellow-800 mb-1">Description</h4>
            <p class="text-sm text-yellow-700">{{ $product->description }}</p>
        </div>
        @endif

        <!-- Recent Transactions -->
        <div class="card rounded-2xl p-6 mb-6">
            <h3 class="text-lg font-bold text-primary-900 mb-4 flex items-center gap-2">
                <i class="fas fa-history text-primary-600"></i> Recent Transactions
            </h3>
            @if($product->grnItems->count() > 0 || $product->saleItems->count() > 0)
                <div class="space-y-4">
                    @foreach($product->grnItems->sortByDesc('created_at')->take(5) as $item)
                        <div class="flex gap-4 items-start border-l-2 border-green-200 pl-4 pb-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-green-100 flex items-center justify-center text-green-700 font-semibold text-sm">
                                <i class="fas fa-arrow-down"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-3 mb-1">
                                    <span class="font-semibold text-green-700">Stock In</span>
                                    <span class="text-xs text-gray-500">{{ $item->created_at->format('d/m/Y H:i:s') }}</span>
                                </div>
                                <p class="text-sm text-gray-600">
                                    <a href="{{ route('purchasing.grn.show', $item->goodsReceivedNote) }}" class="hover:underline text-primary-600">
                                        {{ $item->goodsReceivedNote->grn_number }}
                                    </a>
                                    - Qty: +{{ $item->quantity }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                    @foreach($product->saleItems->sortByDesc('created_at')->take(5) as $item)
                        <div class="flex gap-4 items-start border-l-2 border-red-200 pl-4 pb-4">
                            <div class="flex-shrink-0 w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-700 font-semibold text-sm">
                                <i class="fas fa-arrow-up"></i>
                            </div>
                            <div class="flex-1">
                                <div class="flex flex-wrap items-center gap-3 mb-1">
                                    <span class="font-semibold text-red-700">Stock Out</span>
                                    <span class="text-xs text-gray-500">{{ $item->created_at->format('d/m/Y H:i:s') }}</span>
                                </div>
                                <p class="text-sm text-gray-600">
                                    @if($item->sale)
                                        <a href="{{ route('sales.show', $item->sale) }}" class="hover:underline text-primary-600">
                                            Sale #{{ $item->sale->id }}
                                        </a>
                                    @else
                                        N/A
                                    @endif
                                    - Qty: -{{ $item->quantity }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-center py-8">No transactions yet.</p>
            @endif
        </div>
        
        <!-- Barcode Display -->
        <div class="card rounded-2xl p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-bold text-primary-900">Product Barcode</h3>
                <button onclick="printBarcode()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-print mr-2"></i>Print Barcode
                </button>
            </div>
            <div id="barcode-print-area" class="flex flex-col items-center justify-center p-4 bg-gray-50 rounded-lg">
                <h4 class="font-semibold text-gray-900 mb-2">{{ $product->name }}</h4>
                <img src="{{ $barcodeBase64 }}" alt="Barcode for {{ $product->name }}" class="mb-2" style="width: 400px;">
                <p class="text-sm text-gray-600 font-mono">{{ $barcodeValue }}</p>
                <p class="text-lg font-bold text-primary-600 mt-1">TZS {{ number_format($product->selling_price, 2) }}</p>
            </div>
        </div>
    </div>
</div>

<script>
    function printBarcode() {
        const printContent = document.getElementById('barcode-print-area').innerHTML;
        const originalContent = document.body.innerHTML;
        
        document.body.innerHTML = `
            <html>
                <head>
                    <title>Product Barcode</title>
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
                            padding: 20px;
                            border: 2px solid #000;
                            width: 450px;
                        }
                    </style>
                </head>
                <body>
                    <div class="barcode-container">${printContent}</div>
                </body>
            </html>
        `;
        
        window.print();
        document.body.innerHTML = originalContent;
        location.reload();
    }
</script>
@endsection
