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

    <!-- Barcode Display -->
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-bold text-primary-900">Product Barcode</h3>
            <div class="flex flex-wrap items-center gap-3">
                @if($product->barcode)
                <div class="flex items-center gap-2">
                    <label for="barcodeSize" class="text-sm font-medium text-gray-700">Size:</label>
                    <select id="barcodeSize" onchange="updateBarcodeSize()" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="10">10mm</option>
                        <option value="15">15mm</option>
                        <option value="20" selected>20mm</option>
                        <option value="25">25mm</option>
                        <option value="30">30mm</option>
                        <option value="35">35mm</option>
                    </select>
                </div>
                @endif
                <button type="button" onclick="startScanner()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-camera mr-2"></i>Scan &amp; Link
                </button>
                @if($product->barcode)
                <button onclick="printBarcode()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-print mr-2"></i>Print
                </button>
                @endif
            </div>
        </div>
        <div id="barcodeStatus" class="hidden mb-3 text-sm"></div>
        @if($product->barcode)
        <div id="barcode-print-area" class="flex flex-col items-center justify-center p-4 bg-gray-50 rounded-lg">
            <h4 class="font-semibold text-gray-900 mb-2">{{ $product->name }}</h4>
            <img id="barcodeImage" src="{{ $barcodeBase64 }}" alt="Barcode for {{ $product->name }}" style="width: 20mm;">
        </div>
        @else
        <div class="flex flex-col items-center justify-center p-8 bg-gray-50 rounded-lg border-2 border-dashed border-gray-300">
            <div class="w-16 h-16 rounded-full bg-amber-100 flex items-center justify-center mb-3">
                <i class="fas fa-barcode text-2xl text-amber-600"></i>
            </div>
            <h4 class="font-semibold text-gray-700 mb-1">Not Linked</h4>
            <p class="text-sm text-gray-500 text-center mb-4">This product has no barcode yet. Scan the physical product to link it.</p>
            <button type="button" onclick="startScanner()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors flex items-center gap-2 whitespace-nowrap">
                <i class="fas fa-camera"></i> Scan Product Now
            </button>
        </div>
        @endif
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

<!-- Barcode Scanner Modal -->
<div id="scannerModal" class="fixed inset-0 z-50 bg-black bg-opacity-75 flex items-center justify-center hidden">
    <div class="bg-white rounded-2xl p-6 w-full max-w-lg mx-4 shadow-2xl">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-bold text-gray-900">Scan Barcode / QR Code</h3>
            <button type="button" onclick="stopScanner()" class="text-gray-400 hover:text-gray-600 text-2xl">&times;</button>
        </div>
        <div id="scanPanel">
            <div class="relative mb-4">
                <div id="productScannerViewport" class="w-full rounded-lg overflow-hidden" style="min-height: 250px;"></div>
                <button type="button" onclick="stopScanner()" class="absolute top-2 right-2 z-10 w-10 h-10 rounded-full bg-white/90 hover:bg-white text-gray-800 shadow-lg flex items-center justify-center text-xl font-bold leading-none transition-colors">
                    &times;
                </button>
            </div>
            <div id="scannerStatusText" class="text-sm text-gray-500 text-center mb-3">Initializing camera...</div>
            <div class="flex gap-3">
                <input type="text" id="manualBarcodeInput" placeholder="Or type barcode manually" class="flex-1 min-w-0 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
                <button type="button" onclick="submitManualBarcode()" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">OK</button>
            </div>
        </div>
        <div id="confirmPanel" class="hidden">
            <div class="mb-4 p-4 bg-gray-50 rounded-lg">
                <p class="text-sm text-gray-500 mb-1">Scanned Barcode</p>
                <p id="scannedBarcodeValue" class="font-mono font-semibold text-lg text-gray-900 break-all"></p>
            </div>
            <input type="hidden" id="confirmBarcode">
            <div class="mb-4">
                <label for="scannerExpiryDate" class="block text-sm font-medium text-gray-700 mb-1">Expiry Date (optional)</label>
                <input type="date" id="scannerExpiryDate" value="{{ $product->expiry_date ? $product->expiry_date->format('Y-m-d') : '' }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500">
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="confirmLinkBarcode()" class="flex-1 px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors whitespace-nowrap">
                    <i class="fas fa-link mr-2"></i>Link Barcode &amp; Save
                </button>
                <button type="button" onclick="rescan()" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">Rescan</button>
            </div>
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
        document.getElementById('scanPanel').classList.remove('hidden');
        document.getElementById('confirmPanel').classList.add('hidden');
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
                    handleScannedBarcode(decodedText);
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

    async function stopCamera() {
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
    }

    async function stopScanner() {
        await stopCamera();
        document.getElementById('scannerModal').classList.add('hidden');
    }

    async function handleScannedBarcode(barcode) {
        await stopCamera();
        document.getElementById('scannerModal').classList.remove('hidden');
        document.getElementById('confirmBarcode').value = barcode;
        document.getElementById('scannedBarcodeValue').textContent = barcode;
        document.getElementById('scanPanel').classList.add('hidden');
        document.getElementById('confirmPanel').classList.remove('hidden');
        setTimeout(() => document.getElementById('scannerExpiryDate').focus(), 60);
    }

    function rescan() {
        document.getElementById('confirmPanel').classList.add('hidden');
        document.getElementById('scanPanel').classList.remove('hidden');
        startScanner();
    }

    function confirmLinkBarcode() {
        const barcode = document.getElementById('confirmBarcode').value.trim();
        if (!barcode) return;
        linkBarcode(barcode);
        stopScanner();
    }

    function submitManualBarcode() {
        const val = document.getElementById('manualBarcodeInput').value.trim();
        if (val) {
            handleScannedBarcode(val);
        }
    }

    function linkBarcode(barcode) {
        const statusEl = document.getElementById('barcodeStatus');
        statusEl.classList.remove('hidden');
        const expiryInput = document.getElementById('scannerExpiryDate');
        const expiryDate = expiryInput ? expiryInput.value : '';

        fetch('{{ route("storekeeper.products.link-barcode", $product->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ barcode: barcode, expiry_date: expiryDate || null })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    statusEl.className = 'mb-3 text-sm p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg';
                    statusEl.textContent = 'Barcode linked successfully: ' + data.barcode + (expiryDate ? ' (expiry: ' + expiryDate + ')' : '');
                    setTimeout(() => window.location.reload(), 900);
                } else {
                    statusEl.className = 'mb-3 text-sm p-3 bg-red-100 border border-red-400 text-red-800 rounded-lg';
                    statusEl.textContent = data.message || data.barcode || 'Failed to link barcode.';
                }
            })
            .catch(err => {
                console.error(err);
                statusEl.className = 'mb-3 text-sm p-3 bg-red-100 border border-red-400 text-red-800 rounded-lg';
                statusEl.textContent = 'Error linking barcode. Please try again.';
            });
    }

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
                    handleScannedBarcode(scanned);
                }
            }
        } else if (e.key.length === 1 && !e.ctrlKey && !e.metaKey && !e.altKey) {
            barcodeBuffer += e.key;
        }
    });

    document.getElementById('manualBarcodeInput').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            submitManualBarcode();
        }
    });
</script>
<script>
    function updateBarcodeSize() {
        const img = document.getElementById('barcodeImage');
        if (!img) return;
        const size = document.getElementById('barcodeSize').value;
        img.style.width = size + 'mm';
    }

    function printBarcode() {
        const img = document.getElementById('barcodeImage');
        if (!img) {
            alert('This product has no barcode linked yet.');
            return;
        }
        const size = document.getElementById('barcodeSize').value;
        const printContent = document.getElementById('barcode-print-area').innerHTML;

        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
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
                        }
                        img {
                            width: ${size}mm;
                            height: auto;
                        }
                    </style>
                </head>
                <body>
                    <div class="barcode-container">${printContent}</div>
                </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.onload = function() {
            printWindow.print();
        };
    }
</script>
@endsection
