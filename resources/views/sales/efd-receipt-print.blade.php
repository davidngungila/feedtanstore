<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EFD Receipt {{ $sale->invoice_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Manrope', 'Courier New', Courier, monospace;
            font-size: 13px;
            line-height: 1.5;
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
            margin-bottom: 10px;
            padding-bottom: 10px;
            border-bottom: 2px dashed #000000;
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: 700;
            color: #000000;
            margin-bottom: 2px;
            letter-spacing: 1px;
        }
        
        .header .subtitle {
            font-size: 11px;
            font-weight: 500;
            color: #000000;
            margin-bottom: 2px;
        }
        
        .header .tin-info {
            font-size: 11px;
            font-weight: 600;
            color: #000000;
        }
        
        .details {
            margin: 8px 0;
            padding: 6px 0;
            border-bottom: 1px dashed #000000;
        }
        
        .details p {
            margin: 3px 0;
            font-size: 12px;
        }
        
        .details .label {
            color: #000000;
            font-weight: 600;
        }
        
        .details .value {
            font-weight: 700;
            color: #000000;
        }
        
        .items {
            margin: 8px 0;
            padding-bottom: 8px;
            border-bottom: 1px dashed #000000;
        }
        
        .item-header {
            display: flex;
            justify-content: space-between;
            font-size: 11px;
            font-weight: 700;
            border-bottom: 1px solid #000;
            padding-bottom: 4px;
            margin-bottom: 4px;
        }
        
        .item-row {
            margin: 6px 0;
        }
        
        .item-name {
            font-weight: 700;
            color: #000000;
            font-size: 12px;
        }
        
        .item-details {
            color: #000000;
            font-size: 11px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
        }
        
        .totals {
            margin: 8px 0;
        }
        
        .totals p {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
            font-size: 12px;
        }
        
        .totals .value {
            font-weight: 700;
            color: #000000;
        }
        
        .totals .total-amount {
            font-size: 15px;
            font-weight: 700;
            color: #000000;
            padding-top: 6px;
            margin-top: 6px;
            border-top: 2px solid #000000;
        }
        
        .vat-breakdown {
            margin: 6px 0;
            padding: 4px 0;
            border: 1px dashed #000;
            font-size: 11px;
        }
        
        .vat-breakdown p {
            display: flex;
            justify-content: space-between;
            margin: 2px 4px;
        }
        
        .footer {
            text-align: center;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px dashed #000000;
        }
        
        .footer p {
            color: #000000;
            font-size: 11px;
            font-weight: 500;
            margin: 2px 0;
        }
        
        .qr-code {
            text-align: center;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #000;
        }
        
        .qr-code img {
            max-width: 120px;
            height: auto;
        }
        
        .verification-link {
            text-align: center;
            margin-top: 6px;
            word-break: break-all;
        }
        
        .verification-link a {
            font-size: 9px;
            color: #000;
            text-decoration: none;
        }
        
        .fiscal-info {
            text-align: center;
            margin-top: 8px;
            padding: 4px;
            border: 1px solid #000;
            font-size: 10px;
            font-weight: 700;
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
            <h1>{{ $settings->store_name ?? 'FEEDTAN STORE' }}</h1>
            <p class="subtitle">{{ $settings->store_address ?? 'Moshi, Tanzania' }}</p>
            <p class="subtitle">{{ $settings->store_phone ?? '' }}</p>
            <p class="tin-info">TIN: {{ $settings->tra_tin_number ?? '' }}</p>
            <p class="subtitle">VFD Serial: {{ $settings->tra_vfd_serial ?? '' }}</p>
        </div>
        
        <div class="details">
            <p><span class="label">Receipt #:</span> <span class="value">{{ $sale->tra_receipt_number ?? $sale->invoice_number }}</span></p>
            <p><span class="label">Invoice #:</span> <span class="value">{{ $sale->invoice_number }}</span></p>
            <p><span class="label">Date:</span> <span class="value">{{ $sale->created_at->format('d/m/Y H:i') }}</span></p>
            <p><span class="label">Customer:</span> <span class="value">{{ $sale->customer->name ?? 'Walk-in Customer' }}</span></p>
            @if($sale->customer && $sale->customer->tin_number)
                <p><span class="label">Cust TIN:</span> <span class="value">{{ $sale->customer->tin_number }}</span></p>
            @endif
            <p><span class="label">Cashier:</span> <span class="value">{{ $sale->user->name ?? '-' }}</span></p>
            <p><span class="label">Payment:</span> <span class="value">{{ strtoupper($sale->payment_method ?? 'CASH') }}</span></p>
        </div>
        
        <div class="items">
            <div class="item-header">
                <span>Item</span>
                <span>Qty x Price</span>
            </div>
            @foreach($sale->items as $item)
                <div class="item-row">
                    <div class="item-name">{{ $item->product->name ?? 'Product' }}</div>
                    <div class="item-details">
                        <span>{{ $item->quantity }} x {{ number_format($item->unit_price, 2) }}</span>
                        <span>{{ number_format($item->total, 2) }}</span>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="totals">
            <p><span>Subtotal :</span><span class="value">{{ number_format($sale->subtotal, 2) }}</span></p>
            @if($sale->discount > 0)
                <p><span>Discount :</span><span class="value">-{{ number_format($sale->discount, 2) }}</span></p>
            @endif
            <p class="total-amount"><span>TOTAL :</span><span class="value">{{ number_format($sale->total, 2) }}</span></p>
        </div>
        
        @if($vatAmount > 0)
        <div class="vat-breakdown">
            <p><span>VAT (18%) :</span><span>{{ number_format($vatAmount, 2) }}</span></p>
            <p><span>Incl. VAT :</span><span>{{ number_format($sale->total, 2) }}</span></p>
        </div>
        @endif
        
        <div class="totals">
            <div style="border-top: 1px dashed #000000; margin: 6px 0;"></div>
            <p><span>Paid :</span><span class="value">{{ number_format($sale->paid, 2) }}</span></p>
            <p><span>Change :</span><span class="value">{{ number_format($sale->change, 2) }}</span></p>
        </div>
        
        @if(!empty($verificationLink) || !empty($qrCode))
        <div class="qr-code">
            @if(!empty($qrCode))
                <img src="{{ $qrCode }}" alt="TRA QR Code" style="max-width: 120px; height: auto;">
            @elseif(!empty($verificationLink))
                <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode($verificationLink) }}" alt="TRA QR Code" style="max-width: 120px; height: auto;">
            @endif
            @if(!empty($verificationLink))
                <div class="verification-link">
                    <a href="{{ $verificationLink }}">{{ $verificationLink }}</a>
                </div>
            @endif
            <p style="font-size: 10px; margin-top: 4px; color: #000;">Scan to verify with TRA</p>
        </div>
        @endif
        
        <div class="fiscal-info">
            EFD fiscal receipt - Posted to TRA
        </div>
        
        <div class="footer">
            <p>Thank you for your purchase!</p>
            <p>Please come again!</p>
        </div>
    </div>
    
    <div class="print-button">
        <button onclick="window.print()">
            Print EFD Receipt
        </button>
    </div>
</body>
</html>
