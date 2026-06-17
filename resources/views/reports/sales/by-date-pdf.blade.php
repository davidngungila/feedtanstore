<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Sales by Date - {{ $startDate }} to {{ $endDate }}</title>
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
         .details-table {
             width: 100%;
             border-collapse: collapse;
             margin-bottom: 20px;
         }
         .details-table td {
             padding: 6px 0;
             vertical-align: top;
         }
         .label {
             font-weight: 800;
             color: #4b5563;
             width: 130px;
             text-transform: uppercase;
             font-size: 10px;
         }
         .value {
             font-weight: 700;
             color: #111;
             font-size: 12px;
         }
         .info-card {
             border: 1px solid #e5e7eb;
             border-radius: 6px;
             padding: 12px;
             background: #fff;
         }
         .footer {
             margin-top: 40px;
             text-align: center;
             font-size: 10px;
             color: #6b7280;
             border-top: 1px dashed #e5e7eb;
             padding-top: 15px;
         }
         .items-table {
             width: 100%;
             border-collapse: collapse;
             margin-top: 20px;
             margin-bottom: 20px;
         }
         .items-table th {
             background: #16a34a;
             color: white;
             padding: 8px;
             text-align: left;
             font-size: 10px;
         }
         .items-table td {
             padding: 6px 8px;
             border-bottom: 1px solid #e5e7eb;
             font-size: 11px;
         }
         .stats-grid {
             display: grid;
             grid-template-columns: repeat(3, 1fr);
             gap: 10px;
             margin-bottom: 20px;
         }
         .stat-card {
             background: #f3f4f6;
             padding: 12px;
             border-radius: 6px;
         }
         .stat-label {
             font-size: 10px;
             color: #4b5563;
             font-weight: bold;
             text-transform: uppercase;
         }
         .stat-value {
             font-size: 16px;
             font-weight: 900;
             color: #16a34a;
             margin-top: 4px;
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
             <div class="receipt-title">SALES BY DATE</div>
         </div>

         <table style="width: 100%; margin-bottom: 15px;">
             <tr>
                 <td>
                     <div class="label">Start Date:</div>
                     <div class="value" style="font-size: 16px; color: #16a34a;">{{ \Carbon\Carbon::parse($startDate)->format('l, d F Y') }}</div>
                 </td>
                 <td style="text-align: right;">
                     <div class="label">End Date:</div>
                     <div class="value" style="font-size: 16px; color: #16a34a;">{{ \Carbon\Carbon::parse($endDate)->format('l, d F Y') }}</div>
                 </td>
             </tr>
             <tr>
                 <td colspan="2" style="text-align: right; padding-top: 5px;">
                     <div class="label">Generated On:</div>
                     <div class="value">{{ now()->format('l, d F Y') }}</div>
                     <div class="value" style="font-size: 10px; color: #6b7280; font-weight: normal;">Time: {{ now()->format('H:i:s') }}</div>
                 </td>
             </tr>
         </table>

         <div class="stats-grid">
             <div class="stat-card">
                 <div class="stat-label">Total Sales</div>
                 <div class="stat-value">TZS {{ number_format($sales->sum('total'), 2) }}</div>
             </div>
             <div class="stat-card">
                 <div class="stat-label">Total Transactions</div>
                 <div class="stat-value">{{ $sales->sum('count') }}</div>
             </div>
             <div class="stat-card">
                 <div class="stat-label">Days Active</div>
                 <div class="stat-value">{{ $sales->count() }}</div>
             </div>
         </div>

         <h4 style="font-size: 12px; color: #111; font-weight: bold; margin-bottom: 10px;">Daily Sales:</h4>
         <table class="items-table">
             <thead>
                 <tr>
                     <th>Date</th>
                     <th style="text-align: center;">Transactions</th>
                     <th style="text-align: right;">Total Sales</th>
                 </tr>
             </thead>
             <tbody>
                 @foreach($sales as $sale)
                 <tr>
                     <td>{{ $sale->date }}</td>
                     <td style="text-align: center;">{{ $sale->count }}</td>
                     <td style="text-align: right;">TZS {{ number_format($sale->total, 2) }}</td>
                 </tr>
                 @endforeach
                 @if($sales->isEmpty())
                 <tr>
                     <td colspan="3" style="text-align: center;">No sales data found for the selected period</td>
                 </tr>
                 @endif
             </tbody>
         </table>

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
