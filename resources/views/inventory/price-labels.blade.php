@extends('layouts.app')

@section('page-title', 'Price Labels')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-6">
            <h2 class="text-xl font-bold text-primary-900">Price Labels</h2>
            <div class="flex flex-col md:flex-row items-center gap-3 w-full md:w-auto">
                <form action="{{ route('inventory.products.price-labels') }}" method="GET" class="w-full md:w-64">
                    <div class="relative">
                        <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Search products..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-primary-600">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </form>
                <div class="flex gap-3">
                    <button type="button" onclick="selectAllLabels()" class="border border-primary-600 text-primary-600 hover:bg-primary-50 px-4 py-2 rounded-lg font-medium transition-colors whitespace-nowrap">
                        <i class="fas fa-check-square mr-2"></i>Select All
                    </button>
                    <button type="button" onclick="deselectAllLabels()" class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-4 py-2 rounded-lg font-medium transition-colors whitespace-nowrap">
                        <i class="fas fa-square mr-2"></i>Deselect All
                    </button>
                    <button type="button" onclick="exportLabelsToPNG()" class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg font-medium transition-colors whitespace-nowrap">
                        <i class="fas fa-file-export mr-2"></i>Export to PNG (ZIP)
                    </button>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-800 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4" id="labelsContainer">
            @foreach($products as $product)
                <div class="label-card border border-gray-200 rounded-xl p-4 bg-white hover:shadow-md transition-shadow" data-product-id="{{ $product->id }}">
                    <div class="flex items-center justify-between mb-2">
                        <input type="checkbox" class="label-checkbox w-4 h-4 text-primary-600 rounded border-gray-300" value="{{ $product->id }}" checked>
                        <span class="text-xs text-gray-400">{{ $product->sku }}</span>
                    </div>
                    
                    <div class="label-preview min-h-[120px] flex flex-col items-center justify-center p-2">
                        <div class="text-center w-full">
                            <div class="font-bold text-primary-900 text-sm mb-1">FEEDTAN STORE</div>
                            <div class="text-xs text-gray-500 mb-2 border-t border-gray-200 pt-1"></div>
                            
                            @if($product->barcode)
                                <div class="barcode-container mb-2 flex justify-center">
                                    <img src="{{ $product->barcode_base64 }}" alt="Barcode" class="h-16 w-auto">
                                </div>
                                <div class="text-xs text-gray-600 mb-2 font-mono">{{ $product->barcode }}</div>
                            @else
                                <div class="text-xs text-gray-400 mb-2">No Barcode</div>
                            @endif
                            
                            <div class="text-sm font-semibold text-gray-900 truncate mb-1">{{ $product->name }}</div>
                            
                            <div class="flex items-center justify-center gap-2">
                                <span class="text-lg font-bold text-primary-600">TZS {{ number_format($product->selling_price, 2) }}</span>
                                @if($product->unit)
                                    <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded">{{ $product->unit->short_name }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 p-3" id="pagination">
            {{ $products->links() }}
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>

<script>
    let productsData = [];

    document.addEventListener('DOMContentLoaded', function () {
        bindCheckboxes();
    });

    function bindCheckboxes() {
        const checkboxes = document.querySelectorAll('.label-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateExportButton);
        });
        updateExportButton();
    }

    function updateExportButton() {
        const checkedCount = document.querySelectorAll('.label-checkbox:checked').length;
        const exportBtn = document.querySelector('[onclick="exportLabelsToPNG()"]');
        if (exportBtn) {
            exportBtn.innerHTML = checkedCount > 0 
                ? `<i class="fas fa-file-export mr-2"></i>Export ${checkedCount} Label(s) as ZIP`
                : `<i class="fas fa-file-export mr-2"></i>Export to PNG (ZIP)`;
        }
    }

    function selectAllLabels() {
        document.querySelectorAll('.label-checkbox').forEach(cb => cb.checked = true);
        updateExportButton();
    }

    function deselectAllLabels() {
        document.querySelectorAll('.label-checkbox').forEach(cb => cb.checked = false);
        updateExportButton();
    }

    async function exportLabelsToPNG() {
        const checkedBoxes = document.querySelectorAll('.label-checkbox:checked');
        if (checkedBoxes.length === 0) {
            alert('Please select at least one product');
            return;
        }

        const btn = document.querySelector('[onclick="exportLabelsToPNG()"]');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generating ZIP...';
        btn.disabled = true;

        try {
            const labels = Array.from(checkedBoxes).map(cb => cb.value);
            
            const response = await fetch('{{ route('inventory.products.price-labels.data') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ product_ids: labels })
            });
            
            const data = await response.json();
            productsData = data.products;
            
            await generateIndividualLabelsZIP(productsData);
            
        } catch (error) {
            console.error('Export failed:', error);
            alert('Failed to export labels. Please try again.');
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }

    async function generateIndividualLabelsZIP(products) {
        const zip = new JSZip();
        
        // Exact label dimensions: 37.29mm x 25.79mm at 300 DPI
        const mmToPx = (mm) => Math.round(mm * 300 / 25.4);
        const labelWidth = mmToPx(37.29);   // 441px
        const labelHeight = mmToPx(25.79);  // 305px
        
        // Load all barcode images first
        const barcodePromises = products.map(product => {
            if (product.barcode_base64) {
                return new Promise((resolve) => {
                    const img = new Image();
                    img.onload = () => resolve({ product, img });
                    img.onerror = () => resolve({ product, img: null });
                    img.src = product.barcode_base64;
                });
            }
            return Promise.resolve({ product, img: null });
        });
        
        const barcodeImages = await Promise.all(barcodePromises);
        
        // Generate individual PNG for each product
        for (const { product, img } of barcodeImages) {
            const canvas = document.createElement('canvas');
            canvas.width = labelWidth;
            canvas.height = labelHeight;
            const ctx = canvas.getContext('2d');
            
            // White background
            ctx.fillStyle = '#FFFFFF';
            ctx.fillRect(0, 0, labelWidth, labelHeight);
            
            // Draw single label centered
            drawLabel(ctx, product, img, 0, 0, labelWidth, labelHeight);
            
            // Convert to blob and add to ZIP
            const blob = await canvasToBlob(canvas);
            const fileName = `label_${product.sku}_${product.name.replace(/[^a-zA-Z0-9]/g, '_').slice(0, 30)}.png`;
            zip.file(fileName, blob);
        }
        
        // Generate and download ZIP
        const content = await zip.generateAsync({ type: 'blob' });
        const link = document.createElement('a');
        link.download = `price_labels_${new Date().toISOString().slice(0, 10)}.zip`;
        link.href = URL.createObjectURL(content);
        link.click();
        URL.revokeObjectURL(link.href);
    }

    function canvasToBlob(canvas) {
        return new Promise(resolve => {
            canvas.toBlob(resolve, 'image/png', 1.0);
        });
    }

    function drawLabel(ctx, product, barcodeImg, x, y, width, height) {
        // Small margins for print safety (1mm each side)
        const marginMm = 1;
        const mmToPx = (mm) => Math.round(mm * 300 / 25.4);
        const margin = mmToPx(marginMm);
        const innerWidth = width - margin * 2;
        const innerHeight = height - margin * 2;
        const centerX = x + width / 2;
        
        let currentY = y + margin;
        
        // 1. FEEDTAN STORE header (top)
        ctx.font = 'bold 11px Arial';
        ctx.fillStyle = '#1E3A8A';
        ctx.textAlign = 'center';
        ctx.fillText('FEEDTAN STORE', centerX, currentY);
        currentY += 16;
        
        // Thin separator line
        ctx.strokeStyle = '#E5E7EB';
        ctx.lineWidth = 0.5;
        ctx.beginPath();
        ctx.moveTo(x + margin, currentY);
        ctx.lineTo(x + width - margin, currentY);
        ctx.stroke();
        currentY += 6;
        
        // 2. Barcode (centered, scaled to fit width)
        if (barcodeImg) {
            const maxBarcodeWidth = innerWidth * 0.9;
            const maxBarcodeHeight = innerHeight * 0.35;
            const barcodeAspect = barcodeImg.width / barcodeImg.height;
            
            let barcodeWidth = maxBarcodeWidth;
            let barcodeHeight = barcodeWidth / barcodeAspect;
            
            if (barcodeHeight > maxBarcodeHeight) {
                barcodeHeight = maxBarcodeHeight;
                barcodeWidth = barcodeHeight * barcodeAspect;
            }
            
            const barcodeX = centerX - barcodeWidth / 2;
            ctx.drawImage(barcodeImg, barcodeX, currentY, barcodeWidth, barcodeHeight);
            currentY += barcodeHeight + 4;
            
            // Barcode number text (small, centered)
            ctx.font = '8px monospace';
            ctx.fillStyle = '#4B5563';
            ctx.fillText(product.barcode, centerX, currentY);
            currentY += 12;
        }
        
        // 3. Product name (centered, max 2 lines, ellipsis if needed)
        ctx.font = 'bold 10px Arial';
        ctx.fillStyle = '#111827';
        ctx.textAlign = 'center';
        
        const maxNameWidth = innerWidth - 4;
        const nameLines = wrapText(ctx, product.name, maxNameWidth);
        const linesToShow = nameLines.slice(0, 2);
        
        linesToShow.forEach((line, i) => {
            ctx.fillText(line, centerX, currentY);
            currentY += 13;
        });
        
        // 4. Price (prominent, centered at bottom)
        currentY += 4;
        ctx.font = 'bold 16px Arial';
        ctx.fillStyle = '#1E3A8A';
        const priceText = 'TZS ' + Number(product.selling_price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        
        // Ensure price fits, reduce font if needed
        const priceMetrics = ctx.measureText(priceText);
        if (priceMetrics.width > innerWidth) {
            ctx.font = 'bold 13px Arial';
        }
        ctx.fillText(priceText, centerX, currentY);
        
        // 5. Unit (small, below price)
        if (product.unit_short_name) {
            ctx.font = '8px Arial';
            ctx.fillStyle = '#6B7280';
            ctx.fillText(product.unit_short_name, centerX, currentY + 14);
        }
    }

    function wrapText(ctx, text, maxWidth) {
        const words = text.split(' ');
        const lines = [];
        let currentLine = '';
        
        for (const word of words) {
            const testLine = currentLine ? currentLine + ' ' + word : word;
            const metrics = ctx.measureText(testLine);
            if (metrics.width > maxWidth && currentLine) {
                lines.push(currentLine);
                currentLine = word;
            } else {
                currentLine = testLine;
            }
        }
        if (currentLine) lines.push(currentLine);
        return lines;
    }
</script>
@endsection