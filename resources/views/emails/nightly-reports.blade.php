<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Feedtan End-of-Day Reports {{ $reportDate }}</title>
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
        .alert-table { width: 100%; margin: 10px 0 6px 0; }
        .alert-table th { background: #f3f4f6; color: #374151; font-size: 12px; padding: 8px; text-align: left; text-transform: uppercase; }
        .alert-table td { padding: 8px; border-bottom: 1px solid #e5e7eb; font-size: 13px; }
        .pill { display: inline-block; padding: 2px 10px; border-radius: 999px; font-size: 12px; font-weight: bold; }
        .pill-red { background: #fee2e2; color: #b91c1c; }
        .pill-orange { background: #ffedd5; color: #c2410c; }
        .pill-yellow { background: #fef9c3; color: #a16207; }
        .pill-green { background: #dcfce7; color: #15803d; }
        .pill-gray { background: #f3f4f6; color: #4b5563; }
        .detail-box { margin-top: 14px; padding: 16px; background: #f9fafb; border-radius: 12px; color: #1f2937; font-size: 14px; }
        .kv { width: 100%; }
        .kv td { padding: 6px 0; border-bottom: 1px solid #e5e7eb; font-size: 14px; }
        .muted { color: #6b7280; font-size: 13px; }
        .footer { padding: 20px 30px 28px 30px; background: #f9fafb; color: #6b7280; font-size: 13px; text-align: center; }
    </style>
</head>
<body>
@php
    $alerts = $alerts ?? [];
    $overview = $alerts['overview'] ?? [];
    $lowStock = $alerts['lowStock'] ?? collect();
    $outOfStock = $alerts['outOfStock'] ?? collect();
    $expired = $alerts['expired'] ?? collect();
    $expiring7 = $alerts['expiring7'] ?? collect();
    $expiring30 = $alerts['expiring30'] ?? collect();
    $unusualDiscounts = $alerts['unusualDiscounts'] ?? collect();
    $cancelledCount = $alerts['cancelledCount'] ?? 0;
    $cashDiscrepancies = $alerts['cashDiscrepancies'] ?? collect();
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
                    <h1>End-of-Day Reports</h1>
                    <p>Sales, inventory and management alerts for {{ $reportDate }}. Full details are in the attached PDF documents.</p>
                </td>
            </tr>
            <tr>
                <td class="content">
                    <p>Hello Admin,</p>
                    <p style="margin-top:12px;">Here is the performance summary for <strong>{{ $reportDate }}</strong>.</p>

                    <table class="summary" cellpadding="0" cellspacing="0">
                        <tr>
                            <td width="50%">
                                <div class="label">Total Sales</div>
                                <div class="value">TZS {{ number_format($summary['totalSales'] ?? 0, 2) }}</div>
                            </td>
                            <td width="50%">
                                <div class="label">Transactions</div>
                                <div class="value">{{ number_format($summary['transactionCount'] ?? 0) }}</div>
                            </td>
                        </tr>
                        <tr>
                            <td width="50%">
                                <div class="label">Items Sold</div>
                                <div class="value">{{ number_format($summary['itemsSold'] ?? 0) }}</div>
                            </td>
                            <td width="50%">
                                <div class="label">Stock Value (Cost)</div>
                                <div class="value">TZS {{ number_format($summary['totalStockValue'] ?? 0, 2) }}</div>
                            </td>
                        </tr>
                    </table>

                    <div class="section-title">📦 Inventory Overview</div>
                    <table class="kv" cellpadding="0" cellspacing="0">
                        <tr><td>Total products</td><td align="right"><strong>{{ number_format($overview['totalProducts'] ?? 0) }}</strong></td></tr>
                        <tr><td>Total inventory value</td><td align="right"><strong>TZS {{ number_format($overview['totalStockValue'] ?? 0, 2) }}</strong></td></tr>
                        <tr><td>Products sold ({{ $reportDate }})</td><td align="right"><strong>{{ number_format($overview['productsSold'] ?? 0) }}</strong></td></tr>
                        <tr><td>Low-stock items</td><td align="right"><strong>{{ number_format($overview['lowStockCount'] ?? 0) }}</strong></td></tr>
                        <tr><td>Out-of-stock items</td><td align="right"><strong>{{ number_format($overview['outOfStockCount'] ?? 0) }}</strong></td></tr>
                    </table>

                    <div class="section-title">⚠️ Expiration Alerts</div>
                    <p>
                        <span class="pill pill-red">Expired: {{ $expired->count() }}</span>
                        <span class="pill pill-orange">≤ 7 days: {{ $expiring7->count() }}</span>
                        <span class="pill pill-yellow">≤ 30 days: {{ $expiring30->count() }}</span>
                    </p>
                    @if($expired->count() > 0)
                    <p class="muted"><strong>Already expired</strong></p>
                    <table class="alert-table" cellpadding="0" cellspacing="0">
                        <tr><th>Product</th><th>Batch</th><th>Qty</th><th>Expiry</th></tr>
                        @foreach($expired->take(10) as $p)
                        <tr>
                            <td>{{ $p->name }}</td>
                            <td>{{ $p->batch_number ?? '-' }}</td>
                            <td>{{ $p->quantity }}</td>
                            <td>{{ optional($p->expiry_date)->format('Y-m-d') }}</td>
                        </tr>
                        @endforeach
                    </table>
                    @if($expired->count() > 10)<p class="muted">…and {{ $expired->count() - 10 }} more (see stock PDF).</p>@endif
                    @endif
                    @if($expiring7->count() > 0)
                    <p class="muted"><strong>Expiring within 7 days</strong></p>
                    <table class="alert-table" cellpadding="0" cellspacing="0">
                        <tr><th>Product</th><th>Batch</th><th>Qty</th><th>Expiry</th></tr>
                        @foreach($expiring7->take(10) as $p)
                        <tr>
                            <td>{{ $p->name }}</td>
                            <td>{{ $p->batch_number ?? '-' }}</td>
                            <td>{{ $p->quantity }}</td>
                            <td>{{ optional($p->expiry_date)->format('Y-m-d') }}</td>
                        </tr>
                        @endforeach
                    </table>
                    @if($expiring7->count() > 10)<p class="muted">…and {{ $expiring7->count() - 10 }} more (see stock PDF).</p>@endif
                    @endif
                    @if($expiring30->count() > 0)
                    <p class="muted"><strong>Expiring within 30 days:</strong> {{ $expiring30->count() }} product(s) — full list in the stock PDF.</p>
                    @endif
                    @if($expired->count() === 0 && $expiring7->count() === 0 && $expiring30->count() === 0)
                    <p><span class="pill pill-green">No expiry issues</span></p>
                    @endif

                    <div class="section-title">🚨 Management Alerts</div>
                    <p>
                        <span class="pill pill-orange">Low stock: {{ $lowStock->count() }}</span>
                        <span class="pill pill-red">Out of stock: {{ $outOfStock->count() }}</span>
                        <span class="pill pill-gray">Cancelled sales: {{ $cancelledCount }}</span>
                    </p>
                    @if($lowStock->count() > 0)
                    <p class="muted"><strong>Low-stock products</strong> (at or below reorder level)</p>
                    <table class="alert-table" cellpadding="0" cellspacing="0">
                        <tr><th>Product</th><th>Qty</th><th>Reorder lvl</th></tr>
                        @foreach($lowStock->take(10) as $p)
                        <tr><td>{{ $p->name }}</td><td>{{ $p->quantity }}</td><td>{{ $p->reorder_level }}</td></tr>
                        @endforeach
                    </table>
                    @if($lowStock->count() > 10)<p class="muted">…and {{ $lowStock->count() - 10 }} more (see stock PDF).</p>@endif
                    @endif
                    @if($outOfStock->count() > 0)
                    <p class="muted"><strong>Out-of-stock products</strong></p>
                    <table class="alert-table" cellpadding="0" cellspacing="0">
                        <tr><th>Product</th><th>SKU</th></tr>
                        @foreach($outOfStock->take(10) as $p)
                        <tr><td>{{ $p->name }}</td><td>{{ $p->sku ?? '-' }}</td></tr>
                        @endforeach
                    </table>
                    @if($outOfStock->count() > 10)<p class="muted">…and {{ $outOfStock->count() - 10 }} more (see stock PDF).</p>@endif
                    @endif
                    @if($unusualDiscounts->count() > 0)
                    <p class="muted"><strong>Unusual discounts (≥ 25% off)</strong></p>
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
                    <p class="muted"><strong>Cash discrepancies</strong></p>
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
                    @else
                    <p><span class="pill pill-green">No cash discrepancies</span></p>
                    @endif

                    <div class="section-title">Attached Documents</div>
                    <div class="detail-box">
                        <table class="kv" cellpadding="0" cellspacing="0">
                            <tr><td>Daily Sales Report</td><td align="right"><strong>daily-sales-{{ $reportDate }}.pdf</strong></td></tr>
                            <tr><td>Current Stock Report</td><td align="right"><strong>current-stock-{{ $reportDate }}.pdf</strong></td></tr>
                        </table>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="footer">
                    <p style="margin:0;">This is an automated message sent daily at 23:59 EAT. Please do not reply.</p>
                    <p style="margin:8px 0 0 0;">&copy; FEEDTAN STORE</p>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
