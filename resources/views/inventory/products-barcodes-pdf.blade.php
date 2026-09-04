<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Barcodes</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            padding: 10mm;
        }

        .barcode-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 5mm;
            justify-content: flex-start;
        }

        .barcode-card {
            border: 0.5px solid #ccc;
            padding: 3mm;
            text-align: center;
            border-radius: 2mm;
            page-break-inside: avoid;
        }

        .barcode-card img {
            display: block;
            margin: 0 auto;
        }

        .product-name {
            font-size: 8pt;
            font-weight: bold;
            margin-bottom: 1mm;
            word-wrap: break-word;
            max-width: {{ $size }}mm;
        }
    </style>
</head>
<body>
    <div class="barcode-grid">
        @foreach($barcodes as $item)
        <div class="barcode-card">
            <div class="product-name">{{ $item['product']->name }}</div>
            <img src="{{ $item['barcode_base64'] }}" style="width: {{ $size }}mm; height: auto;">
        </div>
        @endforeach
    </div>
</body>
</html>
