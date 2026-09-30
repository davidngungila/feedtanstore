Hello Admin,

End-of-day reports for {{ $reportDate }} (full details in the 2 attached PDFs).

INVENTORY OVERVIEW
- Total products: {{ number_format(($alerts['overview'] ?? [])['totalProducts'] ?? 0) }}
- Total inventory value: TZS {{ number_format(($alerts['overview'] ?? [])['totalStockValue'] ?? 0, 2) }}
- Products sold: {{ number_format(($alerts['overview'] ?? [])['productsSold'] ?? 0) }}
- Low-stock items: {{ number_format(($alerts['overview'] ?? [])['lowStockCount'] ?? 0) }}
- Out-of-stock items: {{ number_format(($alerts['overview'] ?? [])['outOfStockCount'] ?? 0) }}

EXPIRATION ALERTS
- Expired: {{ ($alerts['expired'] ?? collect())->count() }}
- Expiring within 7 days: {{ ($alerts['expiring7'] ?? collect())->count() }}
- Expiring within 30 days: {{ ($alerts['expiring30'] ?? collect())->count() }}
@foreach(($alerts['expired'] ?? collect())->take(10) as $p)
- EXPIRED: {{ $p->name }} | batch {{ $p->batch_number ?? '-' }} | qty {{ $p->quantity }} | {{ optional($p->expiry_date)->format('Y-m-d') }}
@endforeach
@foreach(($alerts['expiring7'] ?? collect())->take(10) as $p)
- EXPIRING SOON: {{ $p->name }} | batch {{ $p->batch_number ?? '-' }} | qty {{ $p->quantity }} | {{ optional($p->expiry_date)->format('Y-m-d') }}
@endforeach

MANAGEMENT ALERTS
- Low-stock products: {{ ($alerts['lowStock'] ?? collect())->count() }}
- Out-of-stock products: {{ ($alerts['outOfStock'] ?? collect())->count() }}
- Cancelled sales: {{ $alerts['cancelledCount'] ?? 0 }}
- Unusual discounts (>=25%): {{ ($alerts['unusualDiscounts'] ?? collect())->count() }}
- Cash discrepancies: {{ ($alerts['cashDiscrepancies'] ?? collect())->count() }}
@foreach(($alerts['lowStock'] ?? collect())->take(10) as $p)
- LOW STOCK: {{ $p->name }} | qty {{ $p->quantity }} | reorder {{ $p->reorder_level }}
@endforeach
@foreach(($alerts['cashDiscrepancies'] ?? collect()) as $d)
- CASH DIFFERENCE: session {{ $d->session_number ?? ('#' . $d->id) }} | {{ $d->user->name ?? '-' }} | TZS {{ number_format($d->difference, 2) }}
@endforeach

Attachments: daily-sales-{{ $reportDate }}.pdf, current-stock-{{ $reportDate }}.pdf

This is an automated message sent daily at 23:59 EAT. Do not reply.
