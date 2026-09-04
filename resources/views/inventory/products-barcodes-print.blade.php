<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Barcodes</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', Courier, monospace;
            padding: 20px;
        }

        .barcode-container {
            display: block;
        }

        .barcode-card {
            border: 1px solid #ddd;
            padding: 30px;
            text-align: center;
            border-radius: 8px;
            page-break-after: always;
        }

        .barcode-card:last-child {
            page-break-after: avoid;
        }

        .barcode-card img {
            max-width: 100%;
            height: auto;
            margin: 15px 0;
        }

        .product-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 10px;
            word-wrap: break-word;
        }

        .controls {
            text-align: center;
            margin-bottom: 30px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .controls button {
            background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
            color: white;
            border: none;
            padding: 12px 32px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 8px;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .controls button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.4);
        }

        .controls select {
            padding: 10px 16px;
            font-size: 14px;
            font-weight: 600;
            border: 2px solid #22c55e;
            border-radius: 8px;
            background: white;
            color: #166534;
            cursor: pointer;
        }

        .controls .pdf-btn {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        }

        .controls .pdf-btn:hover {
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
        }

        @media print {
            .controls {
                display: none;
            }

            body {
                padding: 0;
            }

            .barcode-card {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <div class="controls">
        <label for="barcodeSize" style="font-weight: 600; font-size: 14px;">Size:</label>
        <select id="barcodeSize" onchange="updateSize()">
            <option value="10">10mm</option>
            <option value="15">15mm</option>
            <option value="20" selected>20mm</option>
            <option value="25">25mm</option>
            <option value="30">30mm</option>
            <option value="35">35mm</option>
        </select>
        <button onclick="window.print()">
            Print All Barcodes
        </button>
        <button class="pdf-btn" onclick="exportPdf()">
            Export PDF
        </button>
    </div>

    <div class="barcode-container" id="barcodeContainer">
        @foreach($barcodes as $item)
        <div class="barcode-card">
            <div class="product-name">{{ $item['product']->name }}</div>
            <img src="{{ $item['barcode_base64'] }}" alt="Product Barcode" style="width: 20mm;">
        </div>
        @endforeach
    </div>

    <script>
        function updateSize() {
            const size = document.getElementById('barcodeSize').value;
            const images = document.querySelectorAll('.barcode-card img');
            images.forEach(img => {
                img.style.width = size + 'mm';
            });
        }

        function exportPdf() {
            const size = document.getElementById('barcodeSize').value;
            const productIds = @json(array_column($barcodes, 'product'));
            const ids = productIds.map(p => p.id);
            
            const params = new URLSearchParams();
            params.set('size', size);
            ids.forEach(id => params.append('product_ids[]', id));
            
            window.location.href = '{{ route("inventory.barcodes.export-pdf") }}?' + params.toString();
        }
    </script>
</body>
</html>
