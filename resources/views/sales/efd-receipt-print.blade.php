<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EFD Receipt · {{ $sale->invoice_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap');
        
        /* ----- base & reset ----- */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Manrope', sans-serif;
            background: #f5f5f5;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 24px 16px;
        }

        .receipt-wrapper {
            max-width: 380px;
            width: 100%;
            background: white;
            padding: 18px 16px 12px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }

        /* ----- typography & spacing (all bold, clean) ----- */
        .receipt {
            font-size: 10.5px;
            line-height: 1.4;
            color: #000;
            font-weight: 700;
        }

        .receipt .thin {
            font-weight: 600;
        }

        .receipt .center {
            text-align: center;
        }

        .receipt .border-dash {
            border-bottom: 1.5px dashed #222;
        }

        .receipt .border-solid {
            border-bottom: 1.5px solid #000;
        }

        .receipt .mt-1 { margin-top: 6px; }
        .receipt .mb-1 { margin-bottom: 6px; }
        .receipt .py-1 { padding-top: 6px; padding-bottom: 6px; }
        .receipt .pb-1 { padding-bottom: 6px; }

        /* ----- header / store ----- */
        .store-header {
            text-align: center;
            margin-bottom: 8px;
        }

        .store-header .logo {
            max-width: 110px;
            margin: 0 auto 4px;
            filter: grayscale(1) brightness(0);
            display: block;
        }

        .store-header h1 {
            font-size: 16px;
            letter-spacing: 0.5px;
        }

        .store-header p {
            font-size: 10px;
            font-weight: 500;
            margin: 2px 0;
        }

        /* ----- detail grid (two-column) ----- */
        .detail-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2px 8px;
            padding: 6px 0;
            border-bottom: 1.5px dashed #222;
            margin-bottom: 6px;
        }

        .detail-grid .label {
            font-weight: 600;
            opacity: 0.8;
        }

        .detail-grid .value {
            font-weight: 700;
            text-align: right;
        }

        .detail-grid .full {
            grid-column: 1 / -1;
        }

        /* ----- items (clean list) ----- */
        .items-list {
            margin: 6px 0 8px;
            border-bottom: 1.5px dashed #222;
            padding-bottom: 6px;
        }

        .item-row {
            display: flex;
            justify-content: space-between;
            padding: 3px 0;
            border-bottom: 1px dotted #ccc;
        }

        .item-row:last-child {
            border-bottom: none;
        }

        .item-name {
            font-weight: 700;
            flex: 2;
        }

        .item-meta {
            font-weight: 600;
            text-align: right;
            flex: 1;
        }

        /* ----- totals (clean) ----- */
        .totals {
            margin: 6px 0 8px;
        }

        .totals .line {
            display: flex;
            justify-content: space-between;
            padding: 2px 0;
        }

        .totals .total-amount {
            font-size: 14px;
            border-top: 2px solid #000;
            padding-top: 6px;
            margin-top: 4px;
        }

        /* ----- EFD mini table (compact) ----- */
        .efd-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
            margin: 6px 0;
        }

        .efd-table th,
        .efd-table td {
            border: 1px solid #000;
            padding: 3px 4px;
            text-align: left;
        }

        .efd-table th {
            background: #000;
            color: #fff;
            font-weight: 700;
        }

        .efd-table .center {
            text-align: center;
        }

        /* ----- tax summary (grid) ----- */
        .tax-summary {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2px 12px;
            font-size: 9.5px;
            padding: 6px 0;
            border-top: 1px solid #000;
            margin-top: 4px;
        }

        .tax-summary .label {
            font-weight: 600;
        }

        .tax-summary .value {
            font-weight: 700;
            text-align: right;
        }

        /* ----- QR & footer ----- */
        .qr-area {
            text-align: center;
            padding: 8px 0 4px;
        }

        .qr-area img {
            max-width: 90px;
            height: auto;
        }

        .legal-footer {
            text-align: center;
            font-size: 9px;
            padding: 6px 0 2px;
            border-top: 1px solid #000;
            margin-top: 6px;
            letter-spacing: 0.3px;
        }

        .thankyou {
            text-align: center;
            font-size: 10px;
            padding-top: 4px;
            border-top: 1.5px dashed #222;
            margin-top: 6px;
        }

        /* ----- print button ----- */
        .print-btn {
            margin-top: 20px;
            text-align: center;
        }

        .print-btn button {
            background: #1e293b;
            color: white;
            border: none;
            padding: 12px 36px;
            font-size: 14px;
            font-weight: 600;
            border-radius: 40px;
            cursor: pointer;
            font-family: 'Manrope', system-ui, sans-serif;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transition: all 0.2s;
        }

        .print-btn button:hover {
            background: #0f172a;
            transform: scale(1.01);
        }

        /* ----- print overrides ----- */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .receipt-wrapper {
                box-shadow: none;
                border-radius: 0;
                padding: 8px 12px;
                max-width: 100%;
            }
            .print-btn {
                display: none;
            }
        }
    </style>
</head>
<body>
<div class="receipt-wrapper">
    <div class="receipt">

        <!-- ===== HEADER ===== -->
        <div class="store-header border-dash pb-1 mb-1">
            <img class="logo" src="https://feedtanstore.com/feedtanstorelogo.png" alt="FEEDTAN STORE">
            <p>{{ $settings->store_address ?? 'Moshi, Tanzania' }}</p>
            <p>{{ $settings->store_phone ?? '' }} · TIN: {{ $settings->tra_tin_number ?? '' }}</p>
        </div>

        <!-- ===== DETAILS (grid) ===== -->
        <div class="detail-grid">
            <span class="label">Invoice</span><span class="value">{{ $sale->invoice_number }}</span>
            <span class="label">Date</span><span class="value">{{ $sale->created_at->format('d/m/Y H:i') }}</span>
            <span class="label">Customer</span><span class="value">{{ $sale->customer->name ?? 'Walk-in' }}</span>
            @if($sale->customer && $sale->customer->tin_number)
                <span class="label">Cust TIN</span><span class="value">{{ $sale->customer->tin_number }}</span>
            @endif
            <span class="label">Cashier</span><span class="value">{{ $sale->user->name ?? '-' }}</span>
            <span class="label">Payment</span><span class="value">{{ strtoupper($sale->payment_method ?? 'CASH') }}</span>
        </div>

        <!-- ===== ITEMS ===== -->
        <div class="items-list">
            @foreach($sale->items as $item)
                <div class="item-row">
                    <span class="item-name">{{ $item->product->name ?? 'Product' }}</span>
                    <span class="item-meta">{{ $item->quantity }} × {{ number_format($item->unit_price, 2) }} = {{ number_format($item->total, 2) }}</span>
                </div>
            @endforeach
        </div>

        <!-- ===== TOTALS ===== -->
        <div class="totals">
            <div class="line"><span>Subtotal</span><span>{{ number_format($sale->subtotal, 2) }}</span></div>
            @if($sale->discount > 0)
                <div class="line"><span>Discount</span><span>-{{ number_format($sale->discount, 2) }}</span></div>
            @endif
            <div class="line total-amount"><span>TOTAL</span><span>{{ number_format($sale->total, 2) }}</span></div>
            <div class="line" style="border-top:1px dashed #222;padding-top:4px;margin-top:2px;"><span>Paid</span><span>{{ number_format($sale->paid, 2) }}</span></div>
            <div class="line"><span>Change</span><span>{{ number_format($sale->change, 2) }}</span></div>
        </div>

        <!-- ===== EFD TABLE (items + tax) ===== -->
        <table class="efd-table">
            <thead>
                <tr><th>Item Desc</th><th class="center">Qty</th><th class="center">Price</th><th class="center">Type</th></tr>
            </thead>
            <tbody>
                @foreach($sale->items as $item)
                <tr>
                    <td>{{ $item->product->name ?? 'Product' }}</td>
                    <td class="center">{{ $item->quantity }}</td>
                    <td class="center">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="center">EX</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- ===== TAX SUMMARY (compact) ===== -->
        <div class="tax-summary">
            <span class="label">Receipt Verification</span><span class="value">{{ $sale->tra_verification_code ?? '-' }}</span>
            <span class="label">TOTAL TAX</span><span class="value">0.00</span>
            <span class="label">Exclusive of Tax</span><span class="value">{{ number_format($sale->subtotal, 2) }}</span>
            <span class="label">DISCOUNT</span><span class="value">{{ number_format($sale->discount, 2) }}</span>
            <span class="label">Inclusive of Tax</span><span class="value">{{ number_format($sale->total, 2) }}</span>
            <span class="label">Tax Rate EX 0%</span><span class="value">0.00</span>
        </div>

        <!-- ===== QR CODE ===== -->
        @if(!empty($qrCode))
        <div class="qr-area">
            <img src="{{ $qrCode }}" alt="TRA QR Code">
        </div>
        @endif

        <!-- ===== LEGAL END & THANK YOU ===== -->
        <div class="legal-footer">*** END OF LEGAL RECEIPT ***</div>
        <div class="thankyou">
            Thank you for your purchase!<br>Please come again!
        </div>

    </div> <!-- /receipt -->
</div> <!-- /receipt-wrapper -->

<!-- ===== PRINT BUTTON ===== -->
<div class="print-btn">
    <button onclick="window.print()">🖨️ Print EFD Receipt</button>
</div>

</body>
</html>