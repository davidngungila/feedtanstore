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
                <div class="flex flex-wrap gap-3">
                    <button type="button" onclick="selectAllLabels()" class="border border-primary-600 text-primary-600 hover:bg-primary-50 px-4 py-2 rounded-lg font-medium transition-colors whitespace-nowrap">
                        <i class="fas fa-check-square mr-2"></i>Select Page
                    </button>
                    <button type="button" onclick="selectAllProducts()" class="border border-primary-600 text-primary-600 hover:bg-primary-50 px-4 py-2 rounded-lg font-medium transition-colors whitespace-nowrap">
                        <i class="fas fa-list-check mr-2"></i>Select All Products
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

        <div id="selectAllStatus" class="hidden mb-4 p-3 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg text-sm"></div>

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
                            <div class="font-bold text-primary-900 text-sm mb-1">{{ $product->name }}</div>
                            <div class="mt-2 pt-2 border-t border-gray-200 w-full"></div>
                            <div class="mt-2">
                                <span class="text-lg font-bold text-primary-600">TZS {{ number_format($product->selling_price, 2) }}</span>
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
    let selectedIds = new Set();

    document.addEventListener('DOMContentLoaded', function () {
        bindCheckboxes();
    });

    function bindCheckboxes() {
        const checkboxes = document.querySelectorAll('.label-checkbox');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', () => {
                if (checkbox.checked) selectedIds.add(Number(checkbox.value));
                else selectedIds.delete(Number(checkbox.value));
                updateExportButton();
            });
            if (checkbox.checked) selectedIds.add(Number(checkbox.value));
        });
        updateExportButton();
    }

    function updateExportButton() {
        const checkedCount = selectedIds.size;
        const exportBtn = document.querySelector('[onclick="exportLabelsToPNG()"]');
        if (exportBtn) {
            exportBtn.innerHTML = checkedCount > 0 
                ? `<i class="fas fa-file-export mr-2"></i>Export ${checkedCount} Label(s) as ZIP`
                : `<i class="fas fa-file-export mr-2"></i>Export to PNG (ZIP)`;
        }
    }

    function selectAllLabels() {
        document.querySelectorAll('.label-checkbox').forEach(cb => {
            cb.checked = true;
            selectedIds.add(Number(cb.value));
        });
        updateExportButton();
    }

    function deselectAllLabels() {
        document.querySelectorAll('.label-checkbox').forEach(cb => cb.checked = false);
        selectedIds.clear();
        document.getElementById('selectAllStatus').classList.add('hidden');
        updateExportButton();
    }

    async function selectAllProducts() {
        const btn = document.querySelector('[onclick="selectAllProducts()"]');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Fetching...';
        btn.disabled = true;

        try {
            const search = new URLSearchParams(window.location.search).get('search') || '';
            const response = await fetch('{{ route('inventory.products.price-labels.ids') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ search: search })
            });
            const data = await response.json();
            const ids = data.ids || [];

            selectedIds = new Set(ids.map(Number));
            document.querySelectorAll('.label-checkbox').forEach(cb => {
                cb.checked = selectedIds.has(Number(cb.value));
            });

            const statusEl = document.getElementById('selectAllStatus');
            statusEl.classList.remove('hidden');
            statusEl.innerHTML = `<i class="fas fa-info-circle mr-2"></i>Selected all <strong>${selectedIds.size}</strong> product(s) matching the current search across the entire database.`;
            updateExportButton();
        } catch (error) {
            console.error('Select all failed:', error);
            alert('Failed to load all products. Please try again.');
        } finally {
            btn.innerHTML = originalText;
            btn.disabled = false;
        }
    }

    async function exportLabelsToPNG() {
        if (selectedIds.size === 0) {
            alert('Please select at least one product');
            return;
        }

        const btn = document.querySelector('[onclick="exportLabelsToPNG()"]');
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Generating ZIP...';
        btn.disabled = true;

        try {
            const labels = Array.from(selectedIds);
            
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
        
        // Exact label dimensions: 37.29mm x 25.91mm at 300 DPI (matches barcode label)
        const mmToPx = (mm) => Math.round(mm * 300 / 25.4);
        const labelWidth = mmToPx(37.29);   // 441px
        const labelHeight = mmToPx(25.91);  // 306px
        
        // Generate individual PNG for each product
        for (const product of products) {
            const canvas = document.createElement('canvas');
            canvas.width = labelWidth;
            canvas.height = labelHeight;
            const ctx = canvas.getContext('2d');
            
            // White background
            ctx.fillStyle = '#FFFFFF';
            ctx.fillRect(0, 0, labelWidth, labelHeight);
            
            // Draw single label centered
            drawLabel(ctx, product, 0, 0, labelWidth, labelHeight);
            
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

    function wrapLabelText(ctx, text, maxWidth, maxLines) {
        const words = String(text).split(' ');
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
        return lines.slice(0, maxLines || 2);
    }

    function drawLabel(ctx, product, x, y, width, height) {
        // White background
        ctx.fillStyle = '#FFFFFF';
        ctx.fillRect(0, 0, width, height);
        
        // Thin cut-guide border (0.18mm) like the barcode label
        const mmToPx = (mm) => Math.round(mm * 300 / 25.4);
        ctx.strokeStyle = '#E5E7EB';
        ctx.lineWidth = Math.max(1, mmToPx(0.18));
        ctx.strokeRect(ctx.lineWidth / 2, ctx.lineWidth / 2, width - ctx.lineWidth, height - ctx.lineWidth);
        
        const margin = mmToPx(2);
        const availW = width - margin * 2;
        const price = 'TZS ' + Number(product.selling_price).toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        
        ctx.textAlign = 'center';
        ctx.textBaseline = 'top';
        
        // 1. Price (prominent)
        let priceFont = mmToPx(4.5);
        ctx.font = '700 ' + priceFont + 'px Arial';
        while (ctx.measureText(price).width > availW && priceFont > 8) {
            priceFont--;
            ctx.font = '700 ' + priceFont + 'px Arial';
        }
        
        // 2. Product name (max 2 lines), keeps price clamped below it
        let nameFont = mmToPx(3.0);
        const gap = mmToPx(0.8);
        const maxNameH = height - margin * 2 - gap - priceFont;
        const lineH = Math.round(nameFont * 1.15);
        
        function wrapName() {
            ctx.font = '700 ' + nameFont + 'px Arial';
            return wrapLabelText(ctx, product.name, availW, 2);
        }
        
        let nameLines = wrapName();
        while (nameLines.length > 1 && (nameLines.length * lineH > maxNameH || ctx.measureText(nameLines[0]).width > availW) && nameFont > 6) {
            nameFont--;
            nameLines = wrapName();
        }
        
        // group name + price as a single centered block with a small gap
        const blockH = nameLines.length * lineH + gap + priceFont;
        const blockY = (height - blockH) / 2;
        
        ctx.font = '700 ' + nameFont + 'px Arial';
        let nameY = blockY;
        ctx.fillStyle = '#111827';
        nameLines.forEach(line => {
            ctx.fillText(line, width / 2, nameY);
            nameY += lineH;
        });
        
        ctx.fillStyle = '#1E3A8A';
        ctx.fillText(price, width / 2, blockY + nameLines.length * lineH + gap);
    }
</script>
@endsection