<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Goods Received: {{ $grn->grn_number }}</title>
    <style type="text/css">
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            width: 100%;
        }
        table {
            border-spacing: 0;
            border-collapse: collapse;
        }
        img {
            border: 0;
            line-height: 100%;
            outline: none;
            text-decoration: none;
            display: block;
        }
        p {
            margin: 0;
            padding: 0;
        }
        body {
            background-color: #f3f4f6;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }
        .email-wrapper {
            background-color: #f3f4f6;
            padding: 40px 20px;
        }
        .email-container {
            max-width: 700px;
            margin: 0 auto;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .logo-container {
            background-color: #ffffff;
            padding: 20px;
            text-align: center;
            border-radius: 12px 12px 0 0;
        }
        .logo {
            max-width: 180px;
            height: auto;
            margin: 0 auto;
        }
        .grn-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 25px 30px;
        }
        .content {
            padding: 40px 30px;
            color: #1f2937;
            font-size: 16px;
            line-height: 1.6;
        }
        .content h1 {
            font-size: 24px;
            margin: 0 0 20px 0;
            color: #064e3b;
        }
        .info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }
        .info-item {
            padding: 10px 0;
        }
        .info-label {
            font-weight: bold;
            color: #4b5563;
            margin-bottom: 5px;
        }
        .info-value {
            color: #1f2937;
        }
        .items-table {
            width: 100%;
            margin: 20px 0;
        }
        .items-table th {
            background-color: #f3f4f6;
            padding: 12px;
            text-align: left;
            color: #374151;
            font-weight: 600;
        }
        .items-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        .total-section {
            text-align: right;
            margin-top: 20px;
        }
        .total-item {
            display: flex;
            justify-content: space-between;
            margin: 8px 0;
            max-width: 300px;
            margin-left: auto;
        }
        .total-label {
            color: #4b5563;
        }
        .total-value {
            font-weight: bold;
        }
        .grand-total {
            font-size: 18px;
            color: #064e3b;
            padding-top: 10px;
            border-top: 2px solid #e5e7eb;
            margin-top: 10px;
        }
        .footer {
            background-color: #f9fafb;
            padding: 20px 30px;
            border-radius: 0 0 12px 12px;
            text-align: center;
            color: #6b7280;
            font-size: 14px;
        }
        .footer p {
            margin-bottom: 8px;
        }
        .footer a {
            color: #10b981;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <table class="email-container" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td class="logo-container" align="center">
                    <img src="https://store.feedtancmg.org/feedtanstorelogo.png" alt="Feedtan Store Logo" class="logo">
                </td>
            </tr>
            <tr>
                <td class="grn-header">
                    <h2 style="margin:0;">Goods Received Note: {{ $grn->grn_number }}</h2>
                    <p style="margin-top: 8px; opacity: 0.9;">Received Date: {{ $grn->received_date->format('d M Y') }}</p>
                </td>
            </tr>
            <tr>
                <td class="content">
                    <p style="margin-bottom: 20px;">
                        Dear {{ $grn->supplier->name ?? 'Supplier' }},
                    </p>
                    <p style="margin-bottom: 20px;">
                        We are pleased to confirm that we have received the following goods:
                    </p>
                    <div class="info-grid">
                        <div class="info-item">
                            <div class="info-label">Supplier</div>
                            <div class="info-value">{{ $grn->supplier->name ?? 'N/A' }}</div>
                        </div>
                        @if($grn->purchaseOrder)
                        <div class="info-item">
                            <div class="info-label">Purchase Order</div>
                            <div class="info-value">{{ $grn->purchaseOrder->po_number }}</div>
                        </div>
                        @endif
                    </div>
                    @if($grn->notes)
                    <div style="margin:20px 0; padding:15px; background-color:#f9fafb; border-radius:8px;">
                        <p style="font-weight:bold; margin-bottom:8px;">Notes:</p>
                        <p>{{ $grn->notes }}</p>
                    </div>
                    @endif
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Unit Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($grn->items as $item)
                            <tr>
                                <td>{{ $item->product->name ?? 'N/A' }}</td>
                                <td>{{ number_format($item->quantity) }}</td>
                                <td>TZS {{ number_format($item->unit_price, 2) }}</td>
                                <td>TZS {{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="total-section">
                        <div class="total-item grand-total">
                            <span class="total-label">Total</span>
                            <span class="total-value">TZS {{ number_format($grn->total, 2) }}</span>
                        </div>
                    </div>
                    <p style="margin-top:30px;">
                        Best regards,<br>
                        The Feedtan Store Team
                    </p>
                </td>
            </tr>
            <tr>
                <td class="footer">
                    <p>&copy; {{ date('Y') }} Feedtan Store. All rights reserved.</p>
                    <p><a href="https://store.feedtancmg.org">Visit our store</a></p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
