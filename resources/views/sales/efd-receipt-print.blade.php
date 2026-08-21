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
            font-family: 'Courier New', Courier, monospace;
            font-size: 11px;
            line-height: 1.3;
            color: #000000;
            font-weight: 700;
            padding: 10px;
        }
        
        .receipt {
            max-width: 280px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 8px;
            padding-bottom: 8px;
            border-bottom: 2px dashed #000000;
        }
        
        .header h1 {
            font-size: 16px;
            font-weight: 700;
            color: #000000;
            margin-bottom: 2px;
            letter-spacing: 1px;
        }
        
        .header p {
            color: #000000;
            font-size: 10px;
            font-weight: 500;
            margin: 1px 0;
        }
        
        .legal-header {
            text-align: center;
            font-size: 10px;
            margin-bottom: 5px;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
        }
        
        .store-info {
            text-align: center;
            margin-bottom: 8px;
            padding-bottom: 5px;
            border-bottom: 1px dashed #000;
        }
        
        .store-info h1 {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 2px;
        }
        
        .store-info p {
            font-size: 10px;
            margin: 1px 0;
        }
        
        .details {
            margin: 8px 0;
            padding: 6px 0;
            border-bottom: 1px dashed #000000;
        }
        
        .details p {
            margin: 3px 0;
            font-size: 11px;
        }
        
        .details .label {
            color: #000000;
            font-weight: 600;
        }
        
        .details .value {
            font-weight: 700;
            color: #000000;
        }
        
        .fiscal-data {
            margin: 5px 0;
            padding: 5px;
            border: 1px solid #000;
            font-size: 9px;
        }
        
        .fiscal-data-row {
            display: flex;
            margin: 2px 0;
        }
        
        .fiscal-label {
            width: 120px;
            font-weight: 700;
        }
        
        .fiscal-value {
            flex: 1;
            font-weight: 700;
        }
        
        .items {
            margin: 8px 0;
            padding-bottom: 8px;
            border-bottom: 1px dashed #000000;
        }
        
        .item-row {
            margin: 6px 0;
        }
        
        .item-name {
            font-weight: 700;
            color: #000000;
            font-size: 11px;
        }
        
        .item-details {
            color: #000000;
            font-size: 10px;
            font-weight: 600;
            display: flex;
            justify-content: space-between;
        }
        
        .items-table {
            margin: 5px 0;
            border-collapse: collapse;
            width: 100%;
            font-size: 9px;
        }
        
        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 2px 4px;
            text-align: left;
        }
        
        .items-table th {
            background: #000;
            color: #fff;
            font-weight: 700;
        }
        
        .items-table .qty,
        .items-table .price,
        .items-table .type {
            text-align: center;
        }
        
        .totals {
            margin: 8px 0;
        }
        
        .totals p {
            display: flex;
            justify-content: space-between;
            margin: 3px 0;
            font-size: 11px;
        }
        
        .totals .value {
            font-weight: 700;
            color: #000000;
        }
        
        .totals .total-amount {
            font-size: 13px;
            font-weight: 700;
            color: #000000;
            padding-top: 6px;
            margin-top: 6px;
            border-top: 2px solid #000000;
        }
        
        .totals-section {
            margin: 5px 0;
            font-size: 9px;
        }
        
        .totals-row {
            display: flex;
            justify-content: space-between;
            margin: 2px 0;
        }
        
        .verification-section {
            margin: 5px 0;
            padding: 5px;
            border: 1px solid #000;
            text-align: center;
            font-size: 9px;
        }
        
        .qr-code {
            text-align: center;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed #000;
        }
        
        .qr-code img {
            max-width: 100px;
            height: auto;
        }
        
        .legal-footer {
            text-align: center;
            margin-top: 5px;
            padding-top: 5px;
            border-top: 1px solid #000;
            font-size: 10px;
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
            <img src="https://feedtanstore.com/feedtanstorelogo.png" alt="FEEDTAN STORE" style="max-width: 120px; margin: 0 auto 6px auto; filter: grayscale(100%) brightness(0);">
            <h1>{{ $settings->store_name ?? 'FEEDTAN STORE' }}</h1>
            <p>{{ $settings->store_address ?? 'Moshi, Tanzania' }}</p>
            <p>{{ $settings->store_phone ?? '' }}</p>
            <p>TIN: {{ $settings->tra_tin_number ?? '' }}</p>
        </div>
        
        <div class="details">
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
            <div style="border-top: 1px dashed #000000; margin: 6px 0;"></div>
            <p><span>Paid :</span><span class="value">{{ number_format($sale->paid, 2) }}</span></p>
            <p><span>Change :</span><span class="value">{{ number_format($sale->change, 2) }}</span></p>
        </div>
        
        <div class="legal-header">
            *** END OF LEGAL RECEIPT ***
        </div>
        
        <div class="legal-header">
            ****** START OF LEGAL RECEIPT ***
        </div>
        
        <div class="store-info">
            <h1>{{ $settings->store_name ?? 'FEEDTAN STORE GROUP' }}</h1>
            <p>{{ $settings->store_address ?? 'MOSHI CBD' }}</p>
            <p>{{ $settings->tra_tin_number ?? '205713018' }}</p>
            <p>VFD Serial: {{ $settings->tra_vfd_serial ?? '' }}</p>
        </div>
        
        <div class="fiscal-data">
            <div class="fiscal-data-row">
                <span class="fiscal-label">Normal</span>
                <span class="fiscal-value">{{ $sale->tra_znum_used ?? '-' }}</span>
            </div>
            <div class="fiscal-data-row">
                <span class="fiscal-label">VFD Serial</span>
                <span class="fiscal-value">{{ $settings->tra_vfd_serial ?? '' }}</span>
            </div>
            <div class="fiscal-data-row">
                <span class="fiscal-label">TIN</span>
                <span class="fiscal-value">{{ $settings->tra_tin_number ?? '' }}</span>
            </div>
            <div class="fiscal-data-row">
                <span class="fiscal-label">Date</span>
                <span class="fiscal-value">{{ $sale->created_at->format('d/m/Y H:i:s') }}</span>
            </div>
            <div class="fiscal-data-row">
                <span class="fiscal-label">Region</span>
                <span class="fiscal-value">KILIMANJARO</span>
            </div>
            <div class="fiscal-data-row">
                <span class="fiscal-label">VRN</span>
                <span class="fiscal-value">{{ $settings->tra_vrn ?? '-' }}</span>
            </div>
            <div class="fiscal-data-row">
                <span class="fiscal-label">Serial Number</span>
                <span class="fiscal-value">{{ $settings->tra_vfd_serial ?? '' }}</span>
            </div>
            <div class="fiscal-data-row">
                <span class="fiscal-label">UIN</span>
                <span class="fiscal-value">{{ $sale->tra_uin ?? '-' }}</span>
            </div>
            <div class="fiscal-data-row">
                <span class="fiscal-label">Tax Officer</span>
                <span class="fiscal-value">TIN</span>
            </div>
            <div class="fiscal-data-row">
                <span class="fiscal-label">Customer Name</span>
                <span class="fiscal-value">{{ $sale->customer->name ?? 'WALK-IN' }}</span>
            </div>
            <div class="fiscal-data-row">
                <span class="fiscal-label">Customer ID Type</span>
                <span class="fiscal-value">{{ $sale->customer && $sale->customer->id_type ? strtoupper($sale->customer->id_type) : '-' }}</span>
            </div>
            <div class="fiscal-data-row">
                <span class="fiscal-label">Customer ID</span>
                <span class="fiscal-value">{{ $sale->customer && $sale->customer->id_number ? $sale->customer->id_number : '-' }}</span>
            </div>
            <div class="fiscal-data-row">
                <span class="fiscal-label">Customer Mobile</span>
                <span class="fiscal-value">{{ $sale->customer && $sale->customer->phone ? $sale->customer->phone : 'NIL' }}</span>
            </div>
            <div class="fiscal-data-row">
                <span class="fiscal-label">Receipt Number</span>
                <span class="fiscal-value">{{ $sale->tra_receipt_number ?: $sale->invoice_number }}</span>
            </div>
            <div class="fiscal-data-row">
                <span class="fiscal-label">Receipt DateTime</span>
                <span class="fiscal-value">{{ $sale->created_at->format('d/m/Y H:i:s') }}</span>
            </div>
        </div>
        
        <table class="items-table">
            <thead>
                <tr>
                    <th>Item Desc</th>
                    <th class="qty">Quantity</th>
                    <th class="price">Price</th>
                    <th class="type">Type</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? 'Product' }}</td>
                    <td class="qty">{{ $item->quantity }}</td>
                    <td class="price">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="type">EX</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="totals-section">
            <div class="totals-row">
                <span>Receipt Verification Code</span>
                <span>{{ $sale->tra_verification_code ?? '-' }}</span>
            </div>
            <div class="totals-row">
                <span>TOTAL TAX</span>
                <span>{{ number_format($vatAmount, 2) }}</span>
            </div>
            <div class="totals-row">
                <span>TOTAL EXCLUSIVE OF TAX</span>
                <span>{{ number_format($sale->subtotal - $vatAmount, 2) }}</span>
            </div>
            <div class="totals-row">
                <span>DISCOUNT</span>
                <span>{{ number_format($sale->discount, 2) }}</span>
            </div>
            <div class="totals-row">
                <span>TOTAL INCLUSIVE OF TAX</span>
                <span>{{ number_format($sale->total, 2) }}</span>
            </div>
            <div class="totals-row">
                <span>TAX RATE EX - 0 %</span>
                <span>{{ number_format($vatAmount, 2) }}</span>
            </div>
        </div>
        
        @if(!empty($qrCode))
        <div class="qr-code">
            <img src="{{ $qrCode }}" alt="TRA QR Code">
        </div>
        @endif
        
        <div class="legal-footer">
            *** END OF LEGAL RECEIPT ***
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
