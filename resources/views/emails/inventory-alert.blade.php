<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Feedtan Stock Alert {{ $alertDate }}</title>
    <style type="text/css">
        html, body { margin: 0; padding: 0; width: 100%; background: #f3f4f6; }
        body { font-family: Arial, Helvetica, sans-serif; color: #1f2937; }
        table { border-spacing: 0; border-collapse: collapse; }
        img { border: 0; display: block; }
        .wrapper { background: #f3f4f6; padding: 32px 16px; }
        .container { width: 100%; max-width: 680px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; }
        .logo { text-align: center; padding: 22px 20px; background: #ffffff; }
        .hero { background: linear-gradient(135deg, #7f1d1d 0%, #b91c1c 100%); color: #ffffff; padding: 28px 30px; }
        .hero h1 { margin: 0 0 8px 0; font-size: 26px; line-height: 1.2; }
        .hero p { margin: 0; font-size: 14px; color: #fecaca; }
        .content { padding: 30px; font-size: 15px; line-height: 1.7; }
        .section-title { margin: 24px 0 12px 0; font-size: 16px; font-weight: bold; color: #0f2a1f; }
        .alert-table { width: 100%; margin: 10px 0 6px 0; }
        .alert-table th { background: #f3f4f6; color: #374151; font-size: 12px; padding: 8px; text-align: left; text-transform: uppercase; }
        .alert-table td { padding: 8px; border-bottom: 1px solid #e5e7eb; font-size: 13px; }
        .muted { color: #6b7280; font-size: 13px; }
        .footer { padding: 20px 30px 28px 30px; background: #f9fafb; color: #6b7280; font-size: 13px; text-align: center; }
    </style>
</head>
<body>
@php
    $lowStock = $sections['lowStock'] ?? collect();
    $outOfStock = $sections['outOfStock'] ?? collect();
    $expired = $sections['expired'] ?? collect();
    $expiring7 = $sections['expiring7'] ?? collect();
    $unusualDiscounts = $sections['unusualDiscounts'] ?? collect();
    $cashDiscrepancies = $sections['cashDiscrepancies'] ?? collect();
@endphp
    <div class="wrapper">
        <table class="container" width="100%" cellpadding="0" cellspacing="0">
            <tr>
                <td class="logo">
                    <img src="https://feedtanstore.com/feedtanstorelogo.png" alt="FEEDTAN STORE" width="170">
                </td>
            </tr>
            <tr>
                <td class="hero">
                    <h1>🚨 Stock Alert</h1>
                    <p>New threshold breaches detected on {{ $alertDate }}. Immediate action may be required.</p>
                </td>
            </tr>
            <tr>
                <td class="content">
                    <p>Hello Admin,</p>
                    <p style="margin-top:12px;">The following issues were detected just now:</p>

                    @if($outOfStock->count() > 0)
                    <div class="section-title">Out of stock ({{ $outOfStock->count() }})</div>
                    <table class="alert-table" cellpadding="0" cellspacing="0">
                        <tr><th>Product</th><th>SKU</th></tr>
                        @foreach($outOfStock as $p)
                        <tr><td>{{ $p->name }}</td><td>{{ $p->sku ?? '-' }}</td></tr>
                        @endforeach
                    </table>
                    @endif

                    @if($lowStock->count() > 0)
                    <div class="section-title">Low stock — reorder level reached ({{ $lowStock->count() }})</div>
                    <table class="alert-table" cellpadding="0" cellspacing="0">
                        <tr><th>Product</th><th>Qty</th><th>Reorder lvl</th></tr>
                        @foreach($lowStock as $p)
                        <tr><td>{{ $p->name }}</td><td>{{ $p->quantity }}</td><td>{{ $p->reorder_level }}</td></tr>
                        @endforeach
                    </table>
                    @endif

                    @if($expired->count() > 0)
                    <div class="section-title">Expired products ({{ $expired->count() }})</div>
                    <table class="alert-table" cellpadding="0" cellspacing="0">
                        <tr><th>Product</th><th>Batch</th><th>Qty</th><th>Expiry</th></tr>
                        @foreach($expired as $p)
                        <tr>
                            <td>{{ $p->name }}</td>
                            <td>{{ $p->batch_number ?? '-' }}</td>
                            <td>{{ $p->quantity }}</td>
                            <td>{{ optional($p->expiry_date)->format('Y-m-d') }}</td>
                        </tr>
                        @endforeach
                    </table>
                    @endif

                    @if($expiring7->count() > 0)
                    <div class="section-title">Expiring within 7 days ({{ $expiring7->count() }})</div>
                    <table class="alert-table" cellpadding="0" cellspacing="0">
                        <tr><th>Product</th><th>Batch</th><th>Qty</th><th>Expiry</th></tr>
                        @foreach($expiring7 as $p)
                        <tr>
                            <td>{{ $p->name }}</td>
                            <td>{{ $p->batch_number ?? '-' }}</td>
                            <td>{{ $p->quantity }}</td>
                            <td>{{ optional($p->expiry_date)->format('Y-m-d') }}</td>
                        </tr>
                        @endforeach
                    </table>
                    @endif

                    @if($unusualDiscounts->count() > 0)
                    <div class="section-title">Unusual discounts ≥ 25% ({{ $unusualDiscounts->count() }})</div>
                    <table class="alert-table" cellpadding="0" cellspacing="0">
                        <tr><th>Invoice</th><th>Customer</th><th>Discount</th><th>Total</th></tr>
                        @foreach($unusualDiscounts as $s)
                        <tr>
                            <td>{{ $s->invoice_number }}</td>
                            <td>{{ $s->customer->name ?? 'Walk-in' }}</td>
                            <td>TZS {{ number_format($s->discount, 2) }}</td>
                            <td>TZS {{ number_format($s->total, 2) }}</td>
                        </tr>
                        @endforeach
                    </table>
                    @endif

                    @if($cashDiscrepancies->count() > 0)
                    <div class="section-title">Cash discrepancies ({{ $cashDiscrepancies->count() }})</div>
                    <table class="alert-table" cellpadding="0" cellspacing="0">
                        <tr><th>Session</th><th>Cashier</th><th>Difference</th></tr>
                        @foreach($cashDiscrepancies as $d)
                        <tr>
                            <td>{{ $d->session_number ?? ('#' . $d->id) }}</td>
                            <td>{{ $d->user->name ?? '-' }}</td>
                            <td>TZS {{ number_format($d->difference, 2) }}</td>
                        </tr>
                        @endforeach
                    </table>
                    @endif

                    <p class="muted" style="margin-top:20px;">You receive this alert immediately when a threshold is breached. A full digest is also included in the 23:59 end-of-day report.</p>
                </td>
            </tr>
            <tr>
                <td class="footer">
                    <p style="margin:0;">Automated alert from FEEDTAN STORE. Please do not reply.</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
