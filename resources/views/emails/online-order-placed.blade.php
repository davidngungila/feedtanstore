<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Your Feedtan Order {{ $order->short_customer_reference }}</title>
    <style type="text/css">
        html, body { margin: 0; padding: 0; width: 100%; background: #f3f4f6; }
        body { font-family: Arial, Helvetica, sans-serif; color: #1f2937; }
        table { border-spacing: 0; border-collapse: collapse; }
        img { border: 0; display: block; }
        .wrapper { background: #f3f4f6; padding: 32px 16px; }
        .container { width: 100%; max-width: 680px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; }
        .logo { text-align: center; padding: 22px 20px; background: #ffffff; }
        .hero { background: linear-gradient(135deg, #0f2a1f 0%, #1b4332 100%); color: #ffffff; padding: 28px 30px; }
        .hero h1 { margin: 0 0 8px 0; font-size: 26px; line-height: 1.2; }
        .hero p { margin: 0; font-size: 14px; color: #dceae1; }
        .content { padding: 30px; font-size: 15px; line-height: 1.7; }
        .summary { width: 100%; margin: 22px 0; }
        .summary td { padding: 12px; background: #f8fafc; border: 1px solid #e5e7eb; }
        .label { font-size: 12px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.04em; }
        .value { font-size: 18px; font-weight: bold; color: #111827; }
        .section-title { margin: 24px 0 12px 0; font-size: 16px; font-weight: bold; color: #0f2a1f; }
        .items th { background: #f3f4f6; color: #374151; font-size: 13px; padding: 12px; text-align: left; }
        .items td { padding: 12px; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
        .totals { width: 100%; margin-top: 16px; }
        .totals td { padding: 8px 0; font-size: 14px; }
        .totals .grand td { border-top: 2px solid #e5e7eb; padding-top: 12px; font-size: 17px; font-weight: bold; }
        .cta-row { padding-top: 18px; }
        .btn { display: inline-block; padding: 12px 18px; border-radius: 999px; text-decoration: none; font-weight: bold; font-size: 14px; margin: 0 8px 8px 0; }
        .btn-primary { background: #e8893a; color: #ffffff !important; }
        .btn-secondary { background: #eef2f7; color: #0f2a1f !important; }
        .detail-box { margin-top: 14px; padding: 16px; background: #f9fafb; border-radius: 12px; color: #1f2937; font-size: 14px; }
        .detail-row { display: flex; justify-content: space-between; padding: 6px 0; border-bottom: 1px solid #e5e7eb; }
        .detail-row:last-child { border-bottom: none; }
        .detail-label { color: #6b7280; font-weight: 500; }
        .detail-value { color: #111827; font-weight: 600; }
        .footer { padding: 20px 30px 28px 30px; background: #f9fafb; color: #6b7280; font-size: 13px; text-align: center; }
    </style>
</head>
<body>
    <div class="wrapper">
        <table class="container" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td class="logo">
                    <img src="{{ config('app.url') }}/feedtanstorelogo.png" alt="Feedtan Store" width="170">
                </td>
            </tr>
            <tr>
                <td class="hero">
                    <h1>Thanks for your order</h1>
                    <p>Your order {{ $order->order_number }} has been received and is now being processed.</p>
                </td>
            </tr>
            <tr>
                <td class="content">
                    <p>Hello {{ $order->customer_name }},</p>
                    <p style="margin-top:12px;">We have received your order successfully. Below are all the details of your order.</p>

                    <table class="summary" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="50%">
                                <div class="label">Order Number</div>
                                <div class="value">{{ $order->short_customer_reference }}</div>
                            </td>
                            <td width="50%">
                                <div class="label">Order Date</div>
                                <div class="value">{{ $order->created_at->format('F j, Y H:i') }}</div>
                            </td>
                        </tr>
                    </table>

                    <div class="section-title">Customer Information</div>
                    <div class="detail-box">
                        <div class="detail-row">
                            <span class="detail-label">Name</span>
                            <span class="detail-value">{{ $order->customer_name }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Phone</span>
                            <span class="detail-value">{{ $order->customer_phone }}</span>
                        </div>
                        @if($order->customer_email)
                        <div class="detail-row">
                            <span class="detail-label">Email</span>
                            <span class="detail-value">{{ $order->customer_email }}</span>
                        </div>
                        @endif
                    </div>

                    <div class="section-title">Delivery Information</div>
                    <div class="detail-box">
                        <div class="detail-row">
                            <span class="detail-label">Delivery Address</span>
                            <span class="detail-value">{{ $order->delivery_address }}</span>
                        </div>
                        @if($order->delivery_latitude && $order->delivery_longitude)
                        <div class="detail-row">
                            <span class="detail-label">Location</span>
                            <span class="detail-value">{{ $order->delivery_latitude }}, {{ $order->delivery_longitude }}</span>
                        </div>
                        @endif
                        <div class="detail-row">
                            <span class="detail-label">Delivery Fee</span>
                            <span class="detail-value">TZS {{ number_format($order->delivery_fee, 0) }}</span>
                        </div>
                    </div>

                    <div class="section-title">Payment Information</div>
                    <div class="detail-box">
                        <div class="detail-row">
                            <span class="detail-label">Payment Method</span>
                            <span class="detail-value">{{ ucfirst($order->payment_method ?? 'cash') }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Payment Status</span>
                            <span class="detail-value">{{ ucfirst($order->payment_status ?? 'pending') }}</span>
                        </div>
                    </div>

                    <div class="section-title">Order Items</div>
                    <table class="items" width="100%" cellpadding="0" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>{{ $item->product->name ?? 'Item' }}</td>
                                <td>{{ number_format($item->quantity) }}</td>
                                <td>TZS {{ number_format($item->price, 0) }}</td>
                                <td>TZS {{ number_format($item->total, 0) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>

                    <table class="totals" cellpadding="0" cellspacing="0">
                        <tr>
                            <td>Subtotal</td>
                            <td align="right">TZS {{ number_format($order->subtotal, 0) }}</td>
                        </tr>
                        <tr>
                            <td>Delivery Fee</td>
                            <td align="right">TZS {{ number_format($order->delivery_fee, 0) }}</td>
                        </tr>
                        <tr class="grand">
                            <td>Total</td>
                            <td align="right">TZS {{ number_format($order->total, 0) }}</td>
                        </tr>
                    </table>

                    <div class="section-title">Next Steps</div>
                    <p>Use the buttons below to track your order, pay for it if you selected online payment, or download the order document.</p>

                    <div class="cta-row">
                        <a href="{{ $trackingUrl }}" class="btn btn-primary">Track Order</a>
                        @if(($order->payment_method ?? 'cash') === 'online' && ($order->payment_status ?? 'pending') !== 'paid')
                        <a href="{{ $payUrl }}" class="btn btn-secondary">Pay Now</a>
                        @endif
                        <a href="{{ $pdfUrl }}" class="btn btn-secondary">Download PDF</a>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="footer">
                    <div>Feedtan Store</div>
                    <div>Kiboriloni, Moshi, Kilimanjaro, Tanzania</div>
                    <div>+255 717 358 865</div>
                    <div>info@feedtanstore.com</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
