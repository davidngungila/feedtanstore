@extends('layouts.app')

@section('page-title', 'Add Product')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Add New Product</h2>
            <a href="{{ route('inventory.products') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Products
            </a>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-800 rounded-lg">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('inventory.products.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Product Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                    <input type="text" name="sku" id="sku" value="{{ old('sku', $generatedSku) }}" readonly class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-700 focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <p class="mt-1 text-xs text-gray-500">Auto-generated when creating the product.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Barcode / QR Code</label>
                    <div class="flex flex-col sm:flex-row gap-2">
                        <input type="text" name="barcode" id="barcode" value="{{ old('barcode') }}" placeholder="Scan or type barcode/QR code (optional)" class="w-full min-w-0 sm:flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <button type="button" onclick="startScanner()" class="w-full sm:w-auto px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors flex items-center justify-center gap-2 whitespace-nowrap">
                            <i class="fas fa-camera"></i> Scan
                        </button>
                    </div>
                    <div id="barcodeStatus" class="mt-1 text-xs hidden"></div>
                    <p class="mt-1 text-xs text-gray-500">Leave blank if the barcode is not known yet - it can be scanned and linked later during verification.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                    <select name="category_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Select Category</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Brand</label>
                    <select name="brand_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Select Brand</option>
                        @foreach($brands as $brand)
                            <option value="{{ $brand->id }}" {{ old('brand_id') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Unit *</label>
                    <select name="unit_id" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Select Unit</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }} ({{ $unit->short_name }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Cost Price (TZS) *</label>
                    <input type="number" step="0.01" name="cost_price" id="cost_price" value="{{ old('cost_price', 0) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Pricing Method</label>
                    <select name="pricing_method" id="pricing_method" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="percentage">Percentage (%)</option>
                        <option value="flat">Flat Amount</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Profit Value</label>
                    <input type="number" step="0.01" name="profit_value" id="profit_value" value="{{ old('profit_value', 0) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Selling Price (TZS) *</label>
                    <input type="number" step="0.01" name="selling_price" id="selling_price" value="{{ old('selling_price', 0) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div class="md:col-span-2">
                    <div class="flex gap-4 mt-2">
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Profit per Unit</label>
                            <div class="px-4 py-2 bg-green-50 border border-green-200 rounded-lg text-green-800 font-medium" id="profit_per_unit">
                                0.00
                            </div>
                        </div>
                        <div class="flex-1">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Profit Margin (%)</label>
                            <div class="px-4 py-2 bg-blue-50 border border-blue-200 rounded-lg text-blue-800 font-medium" id="profit_percentage">
                                0.00
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Quantity *</label>
                    <input type="number" name="quantity" value="{{ old('quantity', 0) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Reorder Level *</label>
                    <input type="number" name="reorder_level" value="{{ old('reorder_level', 0) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Expiry Date</label>
                    <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="flex items-center gap-2 mt-6">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm font-medium text-gray-700">Active</span>
                    </label>
                    <label class="flex items-center gap-2 mt-3">
                        <input type="checkbox" name="is_available_online" value="1" {{ old('is_available_online', true) ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm font-medium text-gray-700">Available Online</span>
                    </label>
                </div>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('description') }}</textarea>
            </div>

            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-1">Specifications</label>
                <textarea name="specifications" rows="4" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('specifications') }}</textarea>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('inventory.products') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Save Product
                </button>
            </div>
        </form>
    </div>
</div>
<script>
    function slugCodeSegment(value) {
        const cleaned = (value || '').replace(/[^A-Za-z0-9]+/g, '').toUpperCase();
        return (cleaned || 'PRD').slice(0, 4);
    }

    function randomHex(length) {
        const chars = '0123456789ABCDEF';
        let output = '';
        for (let i = 0; i < length; i++) {
            output += chars[Math.floor(Math.random() * chars.length)];
        }
        return output;
    }

    function generateSkuPreview() {
        const name = document.querySelector('input[name="name"]').value;
        const now = new Date();
        const y = String(now.getFullYear()).slice(-2);
        const m = String(now.getMonth() + 1).padStart(2, '0');
        const d = String(now.getDate()).padStart(2, '0');
        document.getElementById('sku').value = `${slugCodeSegment(name)}-${y}${m}${d}-${randomHex(6)}`;
    }

    function calculateProfit() {
        const costPrice = parseFloat(document.getElementById('cost_price').value) || 0;
        const sellingPrice = parseFloat(document.getElementById('selling_price').value) || 0;
        const profitPerUnit = sellingPrice - costPrice;
        const profitPercentage = costPrice > 0 ? ((profitPerUnit / costPrice) * 100) : 0;
        
        document.getElementById('profit_per_unit').textContent = profitPerUnit.toFixed(2);
        document.getElementById('profit_percentage').textContent = profitPercentage.toFixed(2) + '%';
    }

    function calculateSellingPrice() {
        const costPrice = parseFloat(document.getElementById('cost_price').value) || 0;
        const pricingMethod = document.getElementById('pricing_method').value;
        const profitValue = parseFloat(document.getElementById('profit_value').value) || 0;
        
        let sellingPrice;
        if (pricingMethod === 'percentage') {
            sellingPrice = costPrice * (1 + profitValue / 100);
        } else {
            sellingPrice = costPrice + profitValue;
        }
        
        document.getElementById('selling_price').value = sellingPrice.toFixed(2);
        calculateProfit();
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('input[name="name"]').addEventListener('input', generateSkuPreview);
        document.getElementById('cost_price').addEventListener('input', calculateSellingPrice);
        document.getElementById('pricing_method').addEventListener('change', calculateSellingPrice);
        document.getElementById('profit_value').addEventListener('input', calculateSellingPrice);
        document.getElementById('selling_price').addEventListener('input', calculateProfit);
    });
</script>

<div id="scannerModal" class="fixed inset-0 z-50 bg-black bg-opacity-75 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-lg mx-4 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Scan Barcode / QR Code</h3>
            <button type="button" onclick="stopScanner()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <div class="relative mb-4">
            <div id="productScannerViewport" class="w-full rounded-lg overflow-hidden" style="min-height: 250px;"></div>
            <button type="button" onclick="stopScanner()" class="absolute top-2 right-2 z-10 w-10 h-10 rounded-full bg-white/90 hover:bg-white text-gray-800 shadow-lg flex items-center justify-center text-xl font-bold leading-none transition-colors">
                &times;
            </button>
        </div>
        <div id="scannerStatusText" class="text-sm text-gray-500 text-center mb-3">Initializing camera...</div>
        <div class="flex gap-3">
            <input type="text" id="manualBarcodeInput" placeholder="Or type barcode manually" class="flex-1 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            <button type="button" onclick="submitManualBarcode()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">OK</button>
        </div>
        <button type="button" onclick="stopScanner()" class="mt-4 w-full px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Close Scanner</button>
    </div>
</div>

<script src="{{ asset('js/html5-qrcode.min.js') }}"></script>
<script>
    let productScanner = null;
    let scannerActive = false;

    async function startScanner() {
        document.getElementById('scannerModal').classList.remove('hidden');
        document.getElementById('scannerStatusText').textContent = 'Initializing camera...';
        document.getElementById('manualBarcodeInput').value = '';

        if (typeof Html5Qrcode === 'undefined') {
            document.getElementById('scannerStatusText').textContent = 'Scanner library failed to load. Type barcode manually.';
            return;
        }

        try {
            const cameras = await Html5Qrcode.getCameras();
            if (!cameras || cameras.length === 0) {
                document.getElementById('scannerStatusText').textContent = 'No camera found. Type barcode manually.';
                return;
            }

            productScanner = new Html5Qrcode('productScannerViewport');
            const backCamera = cameras.find(c => /back|rear|environment/i.test(c.label)) || cameras[cameras.length - 1];
            const cameraId = backCamera.id;

            await productScanner.start(
                cameraId,
                {
                    fps: 10,
                    qrbox: { width: 280, height: 150 },
                    aspectRatio: 1.7778,
                    formatsToSupport: [
                        Html5QrcodeSupportedFormats.CODE_128,
                        Html5QrcodeSupportedFormats.CODE_39,
                        Html5QrcodeSupportedFormats.CODE_93,
                        Html5QrcodeSupportedFormats.EAN_13,
                        Html5QrcodeSupportedFormats.EAN_8,
                        Html5QrcodeSupportedFormats.UPC_A,
                        Html5QrcodeSupportedFormats.UPC_E,
                        Html5QrcodeSupportedFormats.QR_CODE,
                        Html5QrcodeSupportedFormats.ITF,
                        Html5QrcodeSupportedFormats.CODABAR
                    ]
                },
                (decodedText) => {
                    document.getElementById('barcode').value = decodedText;
                    checkBarcodeUniqueness(decodedText);
                    stopScanner();
                },
                () => {}
            );

            document.getElementById('scannerStatusText').textContent = 'Camera active. Point at a barcode or QR code.';
            scannerActive = true;
        } catch (err) {
            console.error('Scanner start error:', err);
            document.getElementById('scannerStatusText').textContent = 'Unable to start camera. Check permissions or type manually.';
        }
    }

    async function stopScanner() {
        if (productScanner && scannerActive) {
            try {
                await productScanner.stop();
                productScanner.clear();
            } catch (e) {
                console.error('Scanner stop error:', e);
            }
            scannerActive = false;
            productScanner = null;
        }
        document.getElementById('scannerModal').classList.add('hidden');
    }

    function submitManualBarcode() {
        const val = document.getElementById('manualBarcodeInput').value.trim();
        if (val) {
            document.getElementById('barcode').value = val;
            checkBarcodeUniqueness(val);
            stopScanner();
        }
    }

    function checkBarcodeUniqueness(barcode) {
        const statusEl = document.getElementById('barcodeStatus');
        if (!barcode || barcode.length < 3) {
            statusEl.classList.add('hidden');
            return;
        }

        fetch('{{ route("inventory.check-barcode") }}?barcode=' + encodeURIComponent(barcode))
            .then(r => r.json())
            .then(data => {
                statusEl.classList.remove('hidden');
                if (data.exists) {
                    statusEl.className = 'mt-1 text-xs text-red-600';
                    statusEl.innerHTML = '<i class="fas fa-exclamation-triangle mr-1"></i>Barcode already linked to: <strong>' + data.product.name + '</strong> (SKU: ' + data.product.sku + ')';
                } else {
                    statusEl.className = 'mt-1 text-xs text-green-600';
                    statusEl.innerHTML = '<i class="fas fa-check-circle mr-1"></i>Barcode is available.';
                }
            })
            .catch(() => {
                statusEl.classList.add('hidden');
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const barcodeInput = document.getElementById('barcode');
        let debounceTimer;
        barcodeInput.addEventListener('input', function() {
            clearTimeout(debounceTimer);
            debounceTimer = setTimeout(() => {
                checkBarcodeUniqueness(this.value.trim());
            }, 500);
        });

        document.getElementById('manualBarcodeInput').addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                submitManualBarcode();
            }
        });

        // Global physical barcode scanner listener (USB/keyboard scanners type fast then send Enter)
        let barcodeBuffer = '';
        let lastKeyTime = 0;
        document.addEventListener('keydown', function(e) {
            const now = Date.now();
            if (now - lastKeyTime > 100) {
                barcodeBuffer = '';
            }
            lastKeyTime = now;

            if (e.key === 'Enter') {
                if (barcodeBuffer.length > 0) {
                    e.preventDefault();
                    const scanned = barcodeBuffer.trim();
                    barcodeBuffer = '';
                    if (scanned) {
                        document.getElementById('barcode').value = scanned;
                        document.getElementById('barcode').dispatchEvent(new Event('input'));
                        checkBarcodeUniqueness(scanned);
                        const modal = document.getElementById('scannerModal');
                        if (modal && !modal.classList.contains('hidden')) {
                            stopScanner();
                        }
                    }
                }
            } else if (e.key.length === 1 && !e.ctrlKey && !e.metaKey && !e.altKey) {
                barcodeBuffer += e.key;
            }
        });
    });
</script>
@endsection
