@extends('layouts.app')

@section('page-title', $product->name)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <!-- Product Header -->
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-xl bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-box text-2xl text-white"></i>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-primary-900">{{ $product->name }}</h2>
                    @if($product->quantity <= $product->reorder_level)
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-100 text-red-700 text-xs font-semibold mt-1">
                            <i class="fas fa-exclamation-triangle"></i> Low Stock Alert
                        </span>
                    @else
                        <span class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-green-100 text-green-700 text-xs font-semibold mt-1">
                            <i class="fas fa-check-circle"></i> In Stock
                        </span>
                    @endif
                </div>
            </div>
            <div class="flex gap-3">
                <a href="{{ route('inventory.products.edit', $product) }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-xl transition-all duration-300 shadow-sm hover:shadow-md">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('inventory.products') }}" class="px-4 py-2 border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-all duration-300">
                    <i class="fas fa-arrow-left mr-2"></i>Back
                </a>
            </div>
        </div>

        <!-- Product Info Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
            <!-- SKU & Basic Info -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-4 rounded-xl border border-blue-200">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-hashtag text-blue-700"></i>
                    <p class="text-xs font-semibold text-blue-700 uppercase tracking-wider">SKU</p>
                </div>
                <p class="font-bold text-blue-900">{{ $product->sku ?? '-' }}</p>
            </div>

            <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-4 rounded-xl border border-purple-200">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-barcode text-purple-700"></i>
                    <p class="text-xs font-semibold text-purple-700 uppercase tracking-wider">Barcode</p>
                </div>
                <p class="font-bold text-purple-900">{{ $product->barcode ?? '-' }}</p>
            </div>

            <div class="bg-gradient-to-br from-indigo-50 to-indigo-100 p-4 rounded-xl border border-indigo-200">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-layer-group text-indigo-700"></i>
                    <p class="text-xs font-semibold text-indigo-700 uppercase tracking-wider">Category</p>
                </div>
                <p class="font-bold text-indigo-900">{{ $product->category->name ?? '-' }}</p>
            </div>

            <div class="bg-gradient-to-br from-amber-50 to-amber-100 p-4 rounded-xl border border-amber-200">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-tag text-amber-700"></i>
                    <p class="text-xs font-semibold text-amber-700 uppercase tracking-wider">Brand</p>
                </div>
                <p class="font-bold text-amber-900">{{ $product->brand->name ?? '-' }}</p>
            </div>

            <div class="bg-gradient-to-br from-emerald-50 to-emerald-100 p-4 rounded-xl border border-emerald-200">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-weight text-emerald-700"></i>
                    <p class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Unit</p>
                </div>
                <p class="font-bold text-emerald-900">{{ $product->unit->name ?? '-' }}</p>
            </div>

            <!-- Stock Status -->
            <div class="bg-gradient-to-br {{ $product->quantity <= $product->reorder_level ? 'from-red-50 to-red-100 border-red-200' : 'from-green-50 to-green-100 border-green-200' }} p-4 rounded-xl border">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-warehouse {{ $product->quantity <= $product->reorder_level ? 'text-red-700' : 'text-green-700' }}"></i>
                    <p class="text-xs font-semibold {{ $product->quantity <= $product->reorder_level ? 'text-red-700' : 'text-green-700' }} uppercase tracking-wider">Stock Level</p>
                </div>
                <p class="font-bold {{ $product->quantity <= $product->reorder_level ? 'text-red-900' : 'text-green-900' }}">
                    {{ $product->quantity }} {{ $product->unit->short_name ?? '' }}
                </p>
            </div>

            <!-- Prices -->
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 p-4 rounded-xl border border-orange-200">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-coins text-orange-700"></i>
                    <p class="text-xs font-semibold text-orange-700 uppercase tracking-wider">Cost Price</p>
                </div>
                <p class="font-bold text-orange-900">TZS {{ number_format($product->cost_price, 2) }}</p>
            </div>

            <div class="bg-gradient-to-br from-teal-50 to-teal-100 p-4 rounded-xl border border-teal-200">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-dollar-sign text-teal-700"></i>
                    <p class="text-xs font-semibold text-teal-700 uppercase tracking-wider">Selling Price</p>
                </div>
                <p class="font-bold text-teal-900">TZS {{ number_format($product->selling_price, 2) }}</p>
            </div>

            <!-- Reorder Level & Expiry -->
            <div class="bg-gradient-to-br from-cyan-50 to-cyan-100 p-4 rounded-xl border border-cyan-200">
                <div class="flex items-center gap-2 mb-2">
                    <i class="fas fa-redo text-cyan-700"></i>
                    <p class="text-xs font-semibold text-cyan-700 uppercase tracking-wider">Reorder Level</p>
                </div>
                <p class="font-bold text-cyan-900">{{ $product->reorder_level }}</p>
            </div>
        </div>

        <!-- Description -->
        @if($product->description)
        <div class="mt-6 p-5 bg-gray-50 rounded-xl border border-gray-200">
            <div class="flex items-center gap-2 mb-3">
                <i class="fas fa-file-alt text-gray-600"></i>
                <p class="text-sm font-semibold text-gray-700 uppercase tracking-wider">Description</p>
            </div>
            <p class="text-gray-800">{{ $product->description }}</p>
        </div>
        @endif

        <!-- Expiry Date -->
        @if($product->expiry_date)
        <div class="mt-6 p-5 bg-gradient-to-r {{ ($product->expiry_date <= today() ? 'from-red-50 to-red-100 border-red-200' : ($product->expiry_date <= now()->addDays(30) ? 'from-orange-50 to-orange-100 border-orange-200' : 'from-blue-50 to-blue-100 border-blue-200')) }} rounded-xl border">
            <div class="flex items-center gap-2 mb-3">
                <i class="fas fa-calendar-alt {{ ($product->expiry_date <= today() ? 'text-red-700' : ($product->expiry_date <= now()->addDays(30) ? 'text-orange-700' : 'text-blue-700')) }}"></i>
                <p class="text-sm font-semibold {{ ($product->expiry_date <= today() ? 'text-red-700' : ($product->expiry_date <= now()->addDays(30) ? 'text-orange-700' : 'text-blue-700')) }} uppercase tracking-wider">
                    {{ $product->expiry_date <= today() ? 'Expired' : ($product->expiry_date <= now()->addDays(30) ? 'Expiring Soon' : 'Expiry Date') }}
                </p>
            </div>
            <p class="font-bold {{ ($product->expiry_date <= today() ? 'text-red-900' : ($product->expiry_date <= now()->addDays(30) ? 'text-orange-900' : 'text-blue-900')) }}">
                {{ date('M d, Y', strtotime($product->expiry_date)) }}
            </p>
        </div>
        @endif

        <!-- Barcode Section -->
        @if($product->barcode || $product->sku)
            <div class="mt-6 p-6 bg-gradient-to-br from-primary-50 to-emerald-50 rounded-xl border border-primary-200">
                <div class="flex items-center justify-between flex-wrap gap-4 mb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-primary-500 to-primary-700 flex items-center justify-center">
                            <i class="fas fa-qrcode text-white"></i>
                        </div>
                        <h3 class="text-lg font-bold text-primary-900">Product Barcode</h3>
                    </div>
                    <button onclick="printBarcode()" class="px-4 py-2 bg-gradient-to-r from-primary-600 to-primary-700 hover:from-primary-700 hover:to-primary-800 text-white rounded-xl transition-all duration-300 shadow-sm hover:shadow-md">
                        <i class="fas fa-print mr-2"></i>Print Barcode
                    </button>
                </div>
                <div class="flex flex-col lg:flex-row items-start gap-6">
                    <div class="bg-white p-6 rounded-xl border-2 border-primary-200 shadow-sm">
                        <svg id="barcodeContainer" style="width: 320px; height: 140px;"></svg>
                        <p class="text-center mt-3 font-mono text-sm text-primary-800 font-bold">{{ $product->barcode ?? $product->sku }}</p>
                    </div>
                    <div class="flex-1">
                        <p class="text-gray-700 mb-3">
                            Barcode for quick scanning at checkout.
                        </p>
                        <ul class="text-sm text-gray-600 space-y-1">
                            <li class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-primary-600"></i>Compatible with most scanners
                            </li>
                            <li class="flex items-center gap-2">
                                <i class="fas fa-check-circle text-primary-600"></i>High resolution for clear printing
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- History Tabs -->
    <div class="flex gap-4 mb-4">
        <button id="tab-purchase" class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 bg-primary-600 text-white shadow-md" onclick="switchTab('purchase')">
            <i class="fas fa-truck mr-2"></i>Purchase History
        </button>
        <button id="tab-sales" class="px-6 py-3 rounded-xl font-semibold transition-all duration-300 bg-white text-gray-700 border border-gray-300 hover:bg-gray-50" onclick="switchTab('sales')">
            <i class="fas fa-cash-register mr-2"></i>Sales History
        </button>
    </div>

    <!-- Purchase History -->
    <div id="panel-purchase" class="card rounded-2xl p-6 mb-6">
        <h3 class="text-lg font-bold text-primary-900 mb-6 flex items-center gap-2">
            <i class="fas fa-file-invoice-dollar text-primary-600"></i> Goods Received Notes (GRNs)
        </h3>
        @if($product->grnItems->count() > 0)
            <div class="overflow-x-auto">
                <table class="data-table w-full">
                    <thead class="bg-primary-50">
                        <tr>
                            <th class="text-left text-primary-700">GRN Number</th>
                            <th class="text-left text-primary-700">Supplier</th>
                            <th class="text-left text-primary-700">Date Received</th>
                            <th class="text-left text-primary-700">Quantity</th>
                            <th class="text-left text-primary-700">Unit Price</th>
                            <th class="text-left text-primary-700">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($product->grnItems as $item)
                        <tr class="hover:bg-primary-50 transition-colors">
                            <td class="font-medium">
                                <a href="{{ route('purchasing.grn.show', $item->goodsReceivedNote) }}" class="text-primary-600 hover:text-primary-800">
                                    {{ $item->goodsReceivedNote->grn_number }}
                                </a>
                            </td>
                            <td>{{ $item->goodsReceivedNote->supplier->name ?? '-' }}</td>
                            <td>{{ $item->goodsReceivedNote->received_date ? date('M d, Y', strtotime($item->goodsReceivedNote->received_date)) : '-' }}</td>
                            <td class="font-semibold text-primary-800">{{ $item->quantity }}</td>
                            <td>TZS {{ number_format($item->unit_price, 2) }}</td>
                            <td class="font-semibold text-primary-800">TZS {{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-inbox text-4xl text-gray-400"></i>
                </div>
                <p class="text-gray-500 font-medium">No purchase history available for this product.</p>
            </div>
        @endif
    </div>

    <!-- Sales History -->
    <div id="panel-sales" class="card rounded-2xl p-6 hidden">
        <h3 class="text-lg font-bold text-primary-900 mb-6 flex items-center gap-2">
            <i class="fas fa-chart-line text-primary-600"></i> Sales History
        </h3>
        @if($product->saleItems->count() > 0)
            <div class="overflow-x-auto">
                <table class="data-table w-full">
                    <thead class="bg-primary-50">
                        <tr>
                            <th class="text-left text-primary-700">Invoice Number</th>
                            <th class="text-left text-primary-700">Customer</th>
                            <th class="text-left text-primary-700">Date</th>
                            <th class="text-left text-primary-700">Quantity</th>
                            <th class="text-left text-primary-700">Unit Price</th>
                            <th class="text-left text-primary-700">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($product->saleItems as $item)
                        <tr class="hover:bg-primary-50 transition-colors">
                            <td class="font-medium">
                                <a href="{{ route('sales.show', $item->sale) }}" class="text-primary-600 hover:text-primary-800">
                                    {{ $item->sale->invoice_number }}
                                </a>
                            </td>
                            <td>{{ $item->sale->customer->name ?? 'Walk-in Customer' }}</td>
                            <td>{{ $item->sale->created_at ? date('M d, Y H:i', strtotime($item->sale->created_at)) : '-' }}</td>
                            <td class="font-semibold text-primary-800">{{ $item->quantity }}</td>
                            <td>TZS {{ number_format($item->unit_price, 2) }}</td>
                            <td class="font-semibold text-primary-800">TZS {{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-12">
                <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-shopping-bag text-4xl text-gray-400"></i>
                </div>
                <p class="text-gray-500 font-medium">No sales history available for this product.</p>
            </div>
        @endif
    </div>
</div>

<!-- Barcode Library -->
<script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.6/dist/JsBarcode.all.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const barcodeContainer = document.getElementById('barcodeContainer');
    if (barcodeContainer) {
        const barcodeContent = '{{ addslashes($product->barcode ?? $product->sku) }}';
        console.log('Barcode content:', barcodeContent);
        if (barcodeContent) {
            try {
                JsBarcode(barcodeContainer, barcodeContent, {
                    format: 'CODE128',
                    lineColor: '#064e3b',
                    width: 2,
                    height: 100,
                    margin: 10,
                    displayValue: false
                });
            } catch (error) {
                console.error('Error generating barcode:', error);
                try {
                    // Try auto format
                    JsBarcode(barcodeContainer, barcodeContent, {
                        lineColor: '#064e3b',
                        width: 2,
                        height: 100,
                        margin: 10,
                        displayValue: false
                    });
                } catch (error2) {
                    console.error('Error generating barcode with auto format:', error2);
                }
            }
        }
    }
});

function printBarcode() {
    const barcodeContainer = document.getElementById('barcodeContainer');
    if (!barcodeContainer) return;

    const printWindow = window.open('', '', 'width=400,height=400');
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Print Barcode</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    text-align: center;
                    padding: 20px;
                }
                .product-name {
                    font-size: 18px;
                    font-weight: bold;
                    margin-bottom: 10px;
                    color: #064e3b;
                }
                .product-price {
                    font-size: 16px;
                    margin-bottom: 20px;
                    color: #065f46;
                }
                .barcode-container {
                    display: inline-block;
                    padding: 15px;
                    border: 2px solid #d1fae5;
                    border-radius: 8px;
                }
                .barcode-text {
                    font-family: monospace;
                    font-size: 14px;
                    margin-top: 10px;
                    color: #064e3b;
                }
                @media print {
                    body {
                        padding: 0;
                    }
                }
            </style>
        </head>
        <body>
            <div class="product-name">{{ $product->name }}</div>
            <div class="product-price">TZS {{ number_format($product->selling_price, 2) }}</div>
            <div class="barcode-container">
                ${barcodeContainer.outerHTML}
                <div class="barcode-text">{{ $product->barcode ?? $product->sku }}</div>
            </div>
        </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
}

function switchTab(tab) {
    // Hide both panels
    document.getElementById('panel-purchase').classList.add('hidden');
    document.getElementById('panel-sales').classList.add('hidden');
    
    // Reset all tab styles
    document.getElementById('tab-purchase').className = 'px-6 py-3 rounded-xl font-semibold transition-all duration-300 bg-white text-gray-700 border border-gray-300 hover:bg-gray-50';
    document.getElementById('tab-sales').className = 'px-6 py-3 rounded-xl font-semibold transition-all duration-300 bg-white text-gray-700 border border-gray-300 hover:bg-gray-50';
    
    // Show selected panel and active tab
    if (tab === 'purchase') {
        document.getElementById('panel-purchase').classList.remove('hidden');
        document.getElementById('tab-purchase').className = 'px-6 py-3 rounded-xl font-semibold transition-all duration-300 bg-primary-600 text-white shadow-md';
    } else {
        document.getElementById('panel-sales').classList.remove('hidden');
        document.getElementById('tab-sales').className = 'px-6 py-3 rounded-xl font-semibold transition-all duration-300 bg-primary-600 text-white shadow-md';
    }
}
</script>
@endsection
