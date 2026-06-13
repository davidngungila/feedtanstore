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
                <a href="{{ route('inventory.products') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Back to Products
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-500 mb-1">SKU</p>
                <p class="font-medium">{{ $product->sku ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Barcode</p>
                <p class="font-medium">{{ $product->barcode ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Category</p>
                <p class="font-medium">{{ $product->category->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Brand</p>
                <p class="font-medium">{{ $product->brand->name ?? '-' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Unit</p>
                <p class="font-medium">{{ $product->unit->name ?? '-' }} ({{ $product->unit->short_name ?? '-' }})</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Quantity in Stock</p>
                <p class="font-medium {{ $product->quantity <= $product->reorder_level ? 'text-red-600' : '' }}">
                    {{ $product->quantity }} {{ $product->unit->short_name ?? '' }}
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Cost Price</p>
                <p class="font-medium">TZS {{ number_format($product->cost_price, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Selling Price</p>
                <p class="font-medium">TZS {{ number_format($product->selling_price, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Reorder Level</p>
                <p class="font-medium">{{ $product->reorder_level }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Expiry Date</p>
                <p class="font-medium">{{ $product->expiry_date ? date('M d, Y', strtotime($product->expiry_date)) : '-' }}</p>
            </div>
            <div class="md:col-span-2">
                <p class="text-sm text-gray-500 mb-1">Description</p>
                <p>{{ $product->description ?? '-' }}</p>
            </div>

            <!-- Barcode Section -->
        @if($product->barcode || $product->sku)
            <div class="md:col-span-2 mt-4 p-4 bg-gray-50 rounded-lg">
                <h3 class="text-lg font-semibold text-primary-900 mb-4">Product Barcode</h3>
                <div class="flex items-start gap-6">
                    <div class="bg-white p-4 rounded-lg border border-gray-200">
                        <svg id="barcodeContainer" style="width: 300px; height: 120px;"></svg>
                        <p class="text-center mt-2 font-mono text-sm text-gray-700">{{ $product->barcode ?? $product->sku }}</p>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm text-gray-600 mb-2">
                            Barcode contains: <strong>{{ $product->barcode ?? $product->sku }}</strong>
                        </p>
                        <p class="text-sm text-gray-600 mb-4">
                            Print this barcode and attach it to your product for quick scanning at checkout.
                        </p>
                        <button onclick="printBarcode()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                            <i class="fas fa-print mr-2"></i>Print Barcode
                        </button>
                    </div>
                </div>
            </div>
        @endif
        </div>
    </div>

    <div class="card rounded-2xl p-6 mb-6">
        <h3 class="text-lg font-bold text-primary-900 mb-6">Purchase History (GRNs)</h3>
        @if($product->grnItems->count() > 0)
            <div class="overflow-x-auto">
                <table class="data-table w-full">
                    <thead>
                        <tr>
                            <th class="text-left">GRN Number</th>
                            <th class="text-left">Supplier</th>
                            <th class="text-left">Date Received</th>
                            <th class="text-left">Quantity</th>
                            <th class="text-left">Unit Price</th>
                            <th class="text-left">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($product->grnItems as $item)
                        <tr>
                            <td class="font-medium">{{ $item->goodsReceivedNote->grn_number }}</td>
                            <td>{{ $item->goodsReceivedNote->supplier->name ?? '-' }}</td>
                            <td>{{ $item->goodsReceivedNote->received_date ? date('M d, Y', strtotime($item->goodsReceivedNote->received_date)) : '-' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>TZS {{ number_format($item->unit_price, 2) }}</td>
                            <td>TZS {{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-center py-8">No purchase history available for this product.</p>
        @endif
    </div>

    <div class="card rounded-2xl p-6">
        <h3 class="text-lg font-bold text-primary-900 mb-6">Sales History</h3>
        @if($product->saleItems->count() > 0)
            <div class="overflow-x-auto">
                <table class="data-table w-full">
                    <thead>
                        <tr>
                            <th class="text-left">Invoice Number</th>
                            <th class="text-left">Customer</th>
                            <th class="text-left">Date</th>
                            <th class="text-left">Quantity</th>
                            <th class="text-left">Unit Price</th>
                            <th class="text-left">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($product->saleItems as $item)
                        <tr>
                            <td class="font-medium">
                                <a href="{{ route('sales.show', $item->sale) }}" class="text-primary-600 hover:text-primary-800">
                                    {{ $item->sale->invoice_number }}
                                </a>
                            </td>
                            <td>{{ $item->sale->customer->name ?? 'Walk-in Customer' }}</td>
                            <td>{{ $item->sale->created_at ? date('M d, Y H:i', strtotime($item->sale->created_at)) : '-' }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>TZS {{ number_format($item->unit_price, 2) }}</td>
                            <td>TZS {{ number_format($item->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <p class="text-gray-500 text-center py-8">No sales history available for this product.</p>
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
                    lineColor: '#000000',
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
                        lineColor: '#000000',
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
                }
                .product-price {
                    font-size: 16px;
                    margin-bottom: 20px;
                }
                .barcode-container {
                    display: inline-block;
                    padding: 10px;
                    border: 1px solid #ccc;
                }
                .barcode-text {
                    font-family: monospace;
                    font-size: 14px;
                    margin-top: 10px;
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
</script>
@endsection
