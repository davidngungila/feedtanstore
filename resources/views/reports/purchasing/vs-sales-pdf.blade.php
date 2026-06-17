<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase vs Sales Report</title>
    <style>
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 100%;
            padding: 10px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #16a34a;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .logo {
            font-size: 22px;
            font-weight: 900;
            color: #16a34a;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .sub-header {
            font-size: 11px;
            color: #16a34a;
            font-weight: bold;
            margin-top: 2px;
            text-transform: uppercase;
        }
        .receipt-title {
            font-size: 18px;
            margin-top: 8px;
            color: #111;
            font-weight: 900;
            background: #f3f4f6;
            padding: 5px;
            display: inline-block;
            border-radius: 4px;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 100px;
            color: rgba(22, 163, 74, 0.05);
            z-index: -1;
            font-weight: bold;
            white-space: nowrap;
        }
        .info-card {
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 12px;
            background: #fff;
            margin-bottom: 20px;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 10px;
            color: #6b7280;
            border-top: 1px dashed #e5e7eb;
            padding-top: 15px;
        }
        .stat-card {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 10px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="watermark">OFFICIAL</div>
    <div class="container">
        <div class="header">
            @if(file_exists(public_path('feedtanstorelogo.png')))
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('feedtanstorelogo.png'))) }}" alt="FEEDTAN STORE" style="max-width: 150px; margin: 0 auto 8px auto;">
            @else
            <div class="logo">FEEDTAN STORE</div>
            @endif
            <div class="sub-header" style="font-size: 10px; margin-top: 4px;">Inventory & Sales Management System</div>
            <div class="receipt-title">PURCHASE vs SALES REPORT</div>
        </div>

        <div class="info-card">
            <div style="font-size: 12px; color: #4b5563;">
                <strong>Report Date:</strong> {{ now()->format('l, d F Y H:i:s') }}<br>
                <strong>From:</strong> {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }}<br>
                <strong>To:</strong> {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}
            </div>
        </div>

        <div class="stat-card" style="background: #dbeafe; border-color: #bfdbfe;">
            <div style="font-size: 14px; font-weight: bold; color: #1e40af;">
                Total Purchases: TZS {{ number_format($purchases, 2) }}
            </div>
        </div>
        <div class="stat-card" style="background: #fef3c7; border-color: #fcd34d;">
            <div style="font-size: 14px; font-weight: bold; color: #92400e;">
                Total Sales: TZS {{ number_format($sales, 2) }}
            </div>
        </div>
        <div class="stat-card" style="background: #f0fdf4; border-color: #bbf7d0;">
            <div style="font-size: 14px; font-weight: bold; color: #166534;">
                Profit (Sales - Purchases): TZS {{ number_format($sales - $purchases, 2) }}
            </div>
        </div>

        <div class="footer">
            <strong>FEEDTAN STORE INVENTORY SYSTEM</strong><br>
            Powered by FeedTan Team<br>
            <div style="margin-top: 10px; font-size: 8px; color: #9ca3af;">
                This document is electronically generated and verified by FEEDTAN STORE INVENTORY SYSTEM.
            </div>
        </div>
    </div>
</body>
</html>
