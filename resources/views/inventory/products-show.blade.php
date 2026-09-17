@extends('layouts.app')

@section('page-title', $product->name)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-3">
            <h2 class="text-xl font-bold text-primary-900">{{ $product->name }}</h2>
            <div class="flex gap-3">
                @if($product->quantity <= $product->reorder_level)
                <a href="{{ route('purchasing.orders.create', ['product' => $product->id]) }}" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-shopping-cart mr-2"></i>Reorder
                </a>
                @endif
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
            <div class="flex flex-wrap items-center justify-between mb-6 gap-3">
                <h3 class="text-lg font-bold text-primary-900 flex items-center gap-2"><i class="fas fa-barcode text-primary-600"></i> Product Barcode</h3>
                <div class="flex flex-wrap items-center gap-2">
                    @if($product->barcode)
                    <div class="flex items-center gap-2">
                        <label for="barcodeSize" class="text-sm font-medium text-gray-700">Size:</label>
                        <select id="barcodeSize" onchange="updateBarcodeSize()" class="px-3 py-1.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="40">40mm</option>
                            <option value="50">50mm</option>
                            <option value="60" selected>60mm</option>
                            <option value="70">70mm</option>
                            <option value="80">80mm</option>
                            <option value="90">90mm</option>
                        </select>
                    </div>
                    @endif
                    <button type="button" onclick="startScanner()" class="px-3 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors text-sm">
                        <i class="fas fa-camera mr-1"></i>Scan &amp; Link
                    </button>
                    @if($product->barcode)
                    <button onclick="printBarcode()" class="px-3 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors text-sm">
                        <i class="fas fa-print mr-1"></i>Print
                    </button>
                    <button onclick="downloadBarcodePNG(event)" class="px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors text-sm">
                        <i class="fas fa-file-image mr-1"></i>PNG
                    </button>
                    <button onclick="downloadBarcodePDF(event)" class="px-3 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg transition-colors text-sm">
                        <i class="fas fa-file-pdf mr-1"></i>PDF
                    </button>
                    @endif
                </div>
            </div>
            <div id="barcodeStatus" class="hidden mb-3 text-sm"></div>
            @if($product->barcode)
            <div id="barcode-print-area" class="flex flex-col items-center justify-center p-8 bg-white rounded-xl border border-gray-200 shadow-sm">
                <img id="barcodeImage" src="{{ $barcodeBase64 }}" alt="Barcode {{ $product->barcode }}" style="width: 60mm; height:auto; image-rendering: pixelated;">
                <p id="barcodeNumbers" class="barcode-numbers mt-4 font-mono text-lg tracking-[0.28em] font-bold text-gray-900 select-all">{{ $product->barcode }}</p>
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
<script src="https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jspdf@2.5.1/dist/jspdf.umd.min.js"></script>
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

        fetch('{{ route("inventory.products.link-barcode", $product) }}', {
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
                    <title>Barcode - {{ $product->barcode }}</title>
                    <style>
                        @import url('https://fonts.googleapis.com/css2?family=JetBrains+Mono:wght@700&display=swap');
                        body {
                            font-family: Arial, sans-serif;
                            display: flex;
                            justify-content: center;
                            align-items: center;
                            min-height: 100vh;
                            margin: 0;
                            padding: 20px;
                            background: #fff;
                        }
                        .barcode-container {
                            text-align: center;
                            padding: 30px 40px;
                            border: 1px solid #e5e7eb;
                            border-radius: 12px;
                            background: #fff;
                        }
                        .barcode-container img {
                            width: ${size}mm;
                            height: auto;
                            image-rendering: pixelated;
                        }
                        .barcode-numbers {
                            margin-top: 14px;
                            font-family: 'JetBrains Mono', monospace;
                            font-size: 16px;
                            letter-spacing: 0.28em;
                            font-weight: 700;
                            color: #111827;
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
            setTimeout(function(){ printWindow.print(); }, 300);
        };
    }

    // --- Exact label size for retail POS scanners: 37.29mm x 25.91mm at 300 DPI ---
    const LABEL_W_MM = 37.29;
    const LABEL_H_MM = 25.91;
    const LABEL_DPI = 300;
    const LABEL_W_PX = Math.round(LABEL_W_MM * LABEL_DPI / 25.4); // ~440
    const LABEL_H_PX = Math.round(LABEL_H_MM * LABEL_DPI / 25.4); // ~306

    function generateLabelCanvas(callback) {
        const codeEl = document.getElementById('barcodeNumbers');
        const code = codeEl ? codeEl.innerText.trim() : '{{ $product->barcode }}';
        const imgEl = document.getElementById('barcodeImage');
        const src = imgEl ? imgEl.src : '{{ $barcodeBase64 }}';

        const canvas = document.createElement('canvas');
        canvas.width = LABEL_W_PX;
        canvas.height = LABEL_H_PX;
        const ctx = canvas.getContext('2d');

        // white background
        ctx.fillStyle = '#ffffff';
        ctx.fillRect(0, 0, LABEL_W_PX, LABEL_H_PX);

        // thin border as cut guide (0.18mm)
        ctx.strokeStyle = '#e5e7eb';
        ctx.lineWidth = Math.max(1, Math.round(0.18 * LABEL_DPI / 25.4));
        ctx.strokeRect(ctx.lineWidth/2, ctx.lineWidth/2, LABEL_W_PX - ctx.lineWidth, LABEL_H_PX - ctx.lineWidth);

        const margin = Math.round(1.6 * LABEL_DPI / 25.4); // 1.6mm
        const gap = Math.round(1.1 * LABEL_DPI / 25.4);    // gap barcode -> numbers
        const textSizePx = Math.round(2.4 * LABEL_DPI / 25.4); // 2.4mm font
        const letterSpacingPx = Math.round(0.55 * LABEL_DPI / 25.4);

        const availW = LABEL_W_PX - 2 * margin;
        const availH = LABEL_H_PX - 2 * margin - textSizePx - gap;

        const img = new Image();
        img.onload = function() {
            // preserve aspect, fit within availW x availH
            const ratio = img.width / img.height;
            let drawW = availW;
            let drawH = drawW / ratio;
            if (drawH > availH) { drawH = availH; drawW = drawH * ratio; }
            // never upscale beyond 92% of availW to keep quiet zones
            const maxW = availW * 0.96;
            if (drawW > maxW) { drawW = maxW; drawH = drawW / ratio; }
            const drawX = (LABEL_W_PX - drawW) / 2;
            const drawY = margin + (availH - drawH) / 2;

            // crisp barcode: disable smoothing
            ctx.imageSmoothingEnabled = false;
            ctx.drawImage(img, drawX, drawY, drawW, drawH);

            // numbers
            ctx.fillStyle = '#111827';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'top';
            const textY = drawY + drawH + gap;
            // use letterSpacing if supported (Chrome 99+)
            try {
                if ('letterSpacing' in ctx) {
                    ctx.letterSpacing = letterSpacingPx + 'px';
                    ctx.font = '700 ' + textSizePx + 'px "JetBrains Mono", monospace';
                    ctx.fillText(code, LABEL_W_PX / 2, textY);
                } else {
                    throw new Error('no letterSpacing');
                }
            } catch (e) {
                // manual spaced fallback
                ctx.font = '700 ' + textSizePx + 'px "JetBrains Mono", monospace';
                let totalW = 0;
                const widths = code.split('').map(ch => ctx.measureText(ch).width);
                totalW = widths.reduce((a,b)=>a+b,0) + letterSpacingPx * (code.length - 1);
                let curX = (LABEL_W_PX - totalW) / 2;
                code.split('').forEach((ch, i) => {
                    const w = widths[i];
                    ctx.fillText(ch, curX + w/2, textY);
                    curX += w + letterSpacingPx;
                });
            }
            callback(canvas, code);
        };
        img.onerror = function() {
            alert('Failed to load barcode image.');
            callback(null, code);
        };
        img.src = src;
    }

    function downloadBarcodePNG(e) {
        const evt = e || window.event;
        const btn = evt && evt.currentTarget ? evt.currentTarget : null;
        const origText = btn ? btn.innerHTML : '';
        if (btn) btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>...';
        generateLabelCanvas(function(canvas, code){
            if (!canvas) { if (btn) btn.innerHTML = origText; return; }
            canvas.toBlob(function(blob){
                if (!blob) { alert('Failed to export PNG.'); if (btn) btn.innerHTML = origText; return; }
                const url = URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'barcode-' + code + '-' + LABEL_W_MM + 'x' + LABEL_H_MM + 'mm.png';
                document.body.appendChild(a);
                a.click();
                setTimeout(function(){ URL.revokeObjectURL(url); a.remove(); }, 500);
                if (btn) btn.innerHTML = origText;
            }, 'image/png');
        });
    }

    function downloadBarcodePDF(e) {
        const evt = e || window.event;
        if (typeof window.jspdf === 'undefined') {
            alert('PDF library not loaded. Please try again.');
            return;
        }
        const btn = evt && evt.currentTarget ? evt.currentTarget : null;
        const origText = btn ? btn.innerHTML : '';
        if (btn) btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>...';
        generateLabelCanvas(function(canvas, code){
            if (!canvas) { if (btn) btn.innerHTML = origText; return; }
            const imgData = canvas.toDataURL('image/png');
            const { jsPDF } = window.jspdf;
            const pdf = new jsPDF({
                orientation: LABEL_W_MM > LABEL_H_MM ? 'landscape' : 'portrait',
                unit: 'mm',
                format: [LABEL_W_MM, LABEL_H_MM]
            });
            // fill background white (jsPDF default transparent)
            pdf.setFillColor(255,255,255);
            pdf.rect(0, 0, LABEL_W_MM, LABEL_H_MM, 'F');
            pdf.addImage(imgData, 'PNG', 0, 0, LABEL_W_MM, LABEL_H_MM);
            pdf.save('barcode-' + code + '-' + LABEL_W_MM + 'x' + LABEL_H_MM + 'mm.pdf');
            if (btn) btn.innerHTML = origText;
        });
    }
</script>
@endsection
