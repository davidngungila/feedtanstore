<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Purchase Order: {{ $purchaseOrder->po_number }}</title>
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
        .po-header {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: white;
            padding: 25px 30px;
        }
        .po-header h2 {
            margin: 0;
            font-size: 22px;
        }
        .po-info {
            margin: 25px 0;
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
            <!-- Logo Container -->
            <tr>
                <td class="logo-container" align="center">
                    <img src="https://store.feedtancmg.org/feedtanstorelogo.png" alt="Feedtan Store Logo" class="logo">
                </td>
            </tr>
            <!-- PO Header -->
            <tr>
                <td class="po-header">
                    <h2>Purchase Order: {{ $purchaseOrder->po_number }}</h2>
                    <p style="margin-top: 8px; opacity: 0.9;">Date: {{ $purchaseOrder->order_date->format('d M Y') }}</p>
                </td>
            </tr>
            <!-- Content -->
            <tr>
                <td class="content">
                    <p style="margin-bottom: 20px;">
                        Dear {{ $purchaseOrder->supplier->name }},
                    </p>
                    <p style="margin-bottom: 20px;">
                        We are pleased to send you our Purchase Order. Please find the details below:
                    </p>

                    <!-- PO Info -->
                    <div class="po-info">
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label">Supplier</div>
                                <div class="info-value">{{ $purchaseOrder->supplier->name }}</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label">Expected Date</div>
                                <div class="info-value">{{ $purchaseOrder->expected_date ? $purchaseOrder->expected_date->format('d M Y') : '-' }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Items Table -->
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
                            @foreach ($purchaseOrder->items as $item)
                            <tr>
                                <td>{{ $item->product->name }}</td>
                                <td>{{ number_format($item->quantity) }}</td>
                                <td>{{ number_format($item->unit_price, 2) }}</td>
                                <td>{{ number_format($item->total, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <!-- Total -->
                    <div class="total-section">
                        <div class="total-item">
                            <span class="total-label">Subtotal</span>
                            <span class="total-value">{{ number_format($purchaseOrder->subtotal, 2) }}</span>
                        </div>
                        @if ($purchaseOrder->tax > 0)
                        <div class="total-item">
                            <span class="total-label">Tax</span>
                            <span class="total-value">{{ number_format($purchaseOrder->tax, 2) }}</span>
                        </div>
                        @endif
                        @if ($purchaseOrder->discount > 0)
                        <div class="total-item">
                            <span class="total-label">Discount</span>
                            <span class="total-value">-{{ number_format($purchaseOrder->discount, 2) }}</span>
                        </div>
                        @endif
                        <div class="total-item grand-total">
                            <span class="total-label">Total</span>
                            <span class="total-value">{{ number_format($purchaseOrder->total, 2) }}</span>
                        </div>
                    </div>

                    @if ($purchaseOrder->notes)
                    <div style="margin-top: 25px; padding: 15px; background-color: #f9fafb; border-radius: 8px;">
                        <p style="font-weight: bold; margin-bottom: 8px;">Notes:</p>
                        <p>{{ $purchaseOrder->notes }}</p>
                    </div>
                    @endif

                    <p style="margin-top: 25px;">
                        Please confirm receipt and delivery date at your earliest convenience.
                    </p>
                    <p style="margin-top: 25px;">
                        Best regards,<br>
                        The Feedtan Store Team
                    </p>
                </td>
            </tr>
            
            <!-- Footer -->
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
