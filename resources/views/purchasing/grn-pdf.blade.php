<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $grn->grn_number }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Plus Jakarta Sans', Arial, sans-serif;
            padding: 20px;
            color: #064e3b;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #10b981;
        }
        .header h1 { color: #064e3b; font-size: 24px; margin-bottom: 5px; }
        .header p { color: #6b7280; font-size: 12px; }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
        }
        .info-item { margin-bottom: 15px; }
        .info-label { font-size: 12px; color: #6b7280; margin-bottom: 3px; }
        .info-value { font-size: 14px; font-weight: 600; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th {
            background: #ecfdf5;
            padding: 10px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            border-bottom: 1px solid #10b981;
        }
        td { padding: 10px; border-bottom: 1px solid #e5e7eb; font-size: 13px; }
        .text-right { text-align: right; }
        .total-row {
            border-top: 2px solid #10b981;
            padding-top: 15px;
            margin-top: 15px;
            text-align: right;
        }
        .total-label { font-weight: 600; font-size: 14px; }
        .total-value { font-weight: 700; font-size: 16px; color: #065f46; }
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
            text-align: center;
            font-size: 11px;
            color: #6b7280;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>FEEDTAN STORE</h1>
        <p>Goods Received Note</p>
    </div>

    <div class="info-grid">
        <div>
            <div class="info-item">
                <p class="info-label">GRN Number</p>
                <p class="info-value">{{ $grn->grn_number }}</p>
            </div>
            <div class="info-item">
                <p class="info-label">Supplier</p>
                <p class="info-value">{{ $grn->supplier->name ?? 'N/A' }}</p>
            </div>
            <div class="info-item">
                <p class="info-label">Received Date</p>
                <p class="info-value">{{ $grn->received_date ? date('M d, Y', strtotime($grn->received_date)) : '-' }}</p>
            </div>
        </div>
        <div>
            <div class="info-item">
                <p class="info-label">Purchase Order</p>
                <p class="info-value">{{ $grn->purchaseOrder?->po_number ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    @if($grn->notes)
    <div style="margin-bottom: 25px;">
        <p class="info-label">Notes</p>
        <p style="font-size: 13px;">{{ $grn->notes }}</p>
    </div>
    @endif

    <table>
        <thead>
            <tr>
                <th>Product</th>
                <th class="text-right">Quantity</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Total</th>
                <th>Expiry Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($grn->items as $item)
            <tr>
                <td>{{ $item->product->name ?? 'N/A' }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">TZS {{ number_format($item->unit_price, 2) }}</td>
                <td class="text-right">TZS {{ number_format($item->total, 2) }}</td>
                <td>{{ $item->expiry_date ? date('M d, Y', strtotime($item->expiry_date)) : '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="total-row">
        <span class="total-label">Total Amount: </span>
        <span class="total-value">TZS {{ number_format($grn->total, 2) }}</span>
    </div>

    <div class="footer">
        <p>Generated on {{ date('M d, Y H:i:s') }}</p>
        <p>FEEDTAN STORE - All rights reserved</p>
    </div>
</body>
</html>