<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt {{ $sale->invoice_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Manrope', 'Courier New', Courier, monospace;
            font-size: 14px;
            line-height: 1.6;
            color: #000000;
            font-weight: 700;
            padding: 15px;
        }
        
        .receipt {
            max-width: 300px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 12px;
            padding-bottom: 12px;
            border-bottom: 2px dashed #22c55e;
        }
        
        .header h1 {
            font-size: 22px;
            font-weight: 700;
            color: #15803d;
            margin-bottom: 4px;
            letter-spacing: 1px;
        }
        
        .header p {
            color: #4ade80;
            font-size: 12px;
            font-weight: 500;
        }
        
        .details {
            margin: 12px 0;
            padding: 8px 0;
            border-bottom: 1px dashed #86efac;
        }
        
        .details p {
            margin: 4px 0;
            font-size: 13px;
        }
        
        .details .label {
            color: #166534;
            font-weight: 600;
        }
        
        .details .value {
            font-weight: 700;
            color: #15803d;
        }
        
        .items {
            margin: 12px 0;
            padding-bottom: 12px;
            border-bottom: 1px dashed #86efac;
        }
        
        .item-row {
            margin: 8px 0;
        }
        
        .item-name {
            font-weight: 700;
            color: #15803d;
            font-size: 14px;
        }
        
        .item-details {
            color: #166534;
            font-size: 13px;
            font-weight: 700;
        }
        
        .totals {
            margin: 12px 0;
        }
        
        .totals p {
            display: flex;
            justify-content: space-between;
            margin: 4px 0;
            font-size: 13px;
        }
        
        .totals .value {
            font-weight: 700;
            color: #15803d;
        }
        
        .totals .total-amount {
            font-size: 16px;
            font-weight: 700;
            color: #15803d;
            padding-top: 8px;
            margin-top: 8px;
            border-top: 2px solid #22c55e;
        }
        
        .footer {
            text-align: center;
            margin-top: 12px;
            padding-top: 12px;
            border-top: 2px dashed #22c55e;
        }
        
        .footer p {
            color: #4ade80;
            font-size: 12px;
            font-weight: 500;
        }
        
        .qr-code {
            text-align: center;
            margin-top: 12px;
            padding-top: 12px;
        }
        
        .qr-code img {
            max-width: 100px;
            height: auto;
        }
        
        .print-button {
            margin: 20px auto;
            text-align: center;
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
            font-family: 'Manrope', sans-serif;
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
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <img src="{{ asset('feedtanstorelogo.png') }}" alt="FEEDTAN STORE" style="max-width: 150px; margin: 0 auto 8px auto; filter: grayscale(100%) brightness(0);">
        </div>
        
        <div class="details">
            <p><span class="label">Invoice #:</span> <span class="value">{{ $sale->invoice_number }}</span></p>
            <p><span class="label">Date:</span> <span class="value">{{ $sale->created_at->format('d/m/Y H:i') }}</span></p>
            <p><span class="label">Customer:</span> <span class="value">{{ $sale->customer->name ?? 'Walk-in Customer' }}</span></p>
            <p><span class="label">Cashier:</span> <span class="value">{{ $sale->user->name ?? '-' }}</span></p>
        </div>
        
        <div class="items">
            @foreach($sale->items as $item)
                <div class="item-row">
                    <div class="item-name">{{ $item->product->name ?? 'Product' }}</div>
                    <div class="item-details">{{ $item->quantity }} x {{ number_format($item->unit_price, 2) }} = {{ number_format($item->total, 2) }}</div>
                </div>
            @endforeach
        </div>
        
        <div class="totals">
            <p><span>Subtotal :</span><span class="value">{{ number_format($sale->subtotal, 2) }}</span></p>
            @if($sale->discount > 0)
                <p><span>Discount :</span><span class="value">-{{ number_format($sale->discount, 2) }}</span></p>
            @endif
            <p class="total-amount"><span>TOTAL :</span><span class="value">{{ number_format($sale->total, 2) }}</span></p>
            <div style="border-top: 1px dashed #86efac; margin: 8px 0;"></div>
            <p><span>Paid :</span><span class="value">{{ number_format($sale->paid, 2) }}</span></p>
            <p><span>Change :</span><span class="value">{{ number_format($sale->change, 2) }}</span></p>
        </div>
        
        <div class="qr-code">
            <img src="{{ $qrCodeBase64 }}" alt="QR Code" style="max-width: 100px; height: auto;">
            <p style="font-size: 11px; margin-top: 4px; color: #166534;">Scan to verify</p>
        </div>
        
        <div class="footer">
            <p>Thank you for your purchase!</p>
            <p>Please come again!</p>
        </div>
    </div>
    
    <div class="print-button">
        <button onclick="window.print()">
            🖨️ Print Receipt
        </button>
    </div>
</body>
</html>