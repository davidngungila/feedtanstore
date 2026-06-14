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
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 20px;
        }

        .barcode-card {
            border: 1px solid #ddd;
            padding: 15px;
            text-align: center;
            border-radius: 8px;
        }

        .barcode-card img {
            max-width: 100%;
            height: auto;
            margin: 10px 0;
        }

        .product-name {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
            word-wrap: break-word;
        }

        .product-price {
            font-size: 12px;
            color: #333;
            margin-bottom: 10px;
        }

        .barcode-value {
            font-size: 12px;
            font-family: 'Courier New', Courier, monospace;
            letter-spacing: 1px;
        }

        .print-button {
            text-align: center;
            margin-bottom: 30px;
        }

        .print-button button {
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

        .print-button button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.4);
        }

        @media print {
            .print-button {
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
    <div class="print-button">
        <button onclick="window.print()">
            🖨️ Print All Barcodes
        </button>
    </div>

    <div class="barcode-container">
        @foreach($barcodes as $item)
        <div class="barcode-card">
            <div class="product-name">{{ $item['product']->name }}</div>
            <img src="{{ $item['barcode_base64'] }}" alt="Product Barcode">
            <div class="barcode-value">{{ $item['barcode_value'] }}</div>
            <div class="product-price">TZS {{ number_format($item['product']->selling_price, 2) }}</div>
        </div>
        @endforeach
    </div>
</body>
</html>
