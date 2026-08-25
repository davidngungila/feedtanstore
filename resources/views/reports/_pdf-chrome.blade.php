<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: 'Helvetica', 'Arial', sans-serif; color: #333; line-height: 1.4; margin: 0; padding: 0; }
        .container { width: 100%; padding: 10px; }
        .header { text-align: center; border-bottom: 2px solid #16a34a; padding-bottom: 12px; margin-bottom: 15px; }
        .logo { font-size: 22px; font-weight: 900; color: #16a34a; text-transform: uppercase; letter-spacing: 1px; }
        .sub-header { font-size: 11px; color: #16a34a; font-weight: bold; margin-top: 2px; text-transform: uppercase; }
        .receipt-title { font-size: 18px; margin-top: 8px; color: #111; font-weight: 900; background: #f3f4f6; padding: 5px; display: inline-block; border-radius: 4px; }
        .items-table { width: 100%; border-collapse: collapse; margin-top: 14px; margin-bottom: 14px; }
        .items-table th { background: #16a34a; color: white; padding: 8px; text-align: left; font-size: 10px; }
        .items-table td { padding: 6px 8px; border-bottom: 1px solid #e5e7eb; font-size: 11px; }
        .stats-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-bottom: 16px; }
        .stat-card { background: #f3f4f6; padding: 12px; border-radius: 6px; }
        .stat-label { font-size: 10px; color: #4b5563; font-weight: bold; text-transform: uppercase; }
        .stat-value { font-size: 16px; font-weight: 900; color: #16a34a; margin-top: 4px; }
        .badge { padding: 4px 8px; border-radius: 9999px; font-size: 9px; font-weight: bold; text-transform: uppercase; }
        .badge-green { background: #dcfce7; color: #166534; }
        .badge-yellow { background: #fef3c7; color: #92400e; }
        .badge-red { background: #fee2e2; color: #991b1b; }
        h4.sec { font-size: 12px; color: #111; font-weight: bold; margin: 14px 0 8px; }
        .empty { text-align: center; color: #6b7280; font-size: 12px; padding: 20px 0; }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        @if(file_exists(public_path('feedtanstorelogo.png')))
        <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('feedtanstorelogo.png'))) }}" alt="FEEDTAN STORE" style="max-width: 150px; margin: 0 auto 8px auto;">
        @else
        <div class="logo">FEEDTAN STORE</div>
        @endif
        <div class="sub-header" style="font-size: 10px; margin-top: 4px;">Inventory &amp; Sales Management System</div>
        <div class="receipt-title">{{ $title }}</div>
    </div>

    <table style="width: 100%; margin-bottom: 12px;">
        <tr>
            <td style="text-align: right;">
                <div style="font-size: 10px; color: #4b5563; font-weight: 800; text-transform: uppercase;">Generated On:</div>
                <div style="font-size: 12px; font-weight: 700; color: #111;">{{ now()->format('l, d F Y H:i') }}</div>
                @if(!empty($period))
                <div style="font-size: 11px; color: #374151; font-weight: 600;">Period: {{ $period }}</div>
                @endif
            </td>
        </tr>
    </table>
