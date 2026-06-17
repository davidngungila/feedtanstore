<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Payment Method Report - {{ $startDate }} to {{ $endDate }}</title>
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
             grid-template-columns: repeat(4, 1fr);
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
             <div class="receipt-title">PAYMENT METHOD REPORT</div>
         </div>

         <table style="width: 100%; margin-bottom: 15px;">
             <tr>
                 <td>
                     <div style="font-size: 10px; color: #4b5563; font-weight: 800; text-transform: uppercase;">Date Range:</div>
                     <div style="font-size: 16px; color: #16a34a; font-weight: 900;">{{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} - {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}</div>
                 </td>
                 <td style="text-align: right;">
                     <div style="font-size: 10px; color: #4b5563; font-weight: 800; text-transform: uppercase;">Generated On:</div>
                     <div style="font-size: 12px; font-weight: 700;">{{ now()->format('l, d F Y') }}</div>
                     <div style="font-size: 10px; color: #6b7280;">Time: {{ now()->format('H:i:s') }}</div>
                 </td>
             </tr>
         </table>

         <div class="stats-grid">
             <div class="stat-card">
                 <div class="stat-label">Total Sales</div>
                 <div class="stat-value">TZS {{ number_format($totalSales, 2) }}</div>
             </div>
             <div class="stat-card">
                 <div class="stat-label">Transactions</div>
                 <div class="stat-value">{{ number_format($totalTransactions) }}</div>
             </div>
             <div class="stat-card">
                 <div class="stat-label">Avg Transaction</div>
                 <div class="stat-value">
                     TZS {{ $totalTransactions > 0 ? number_format($totalSales / $totalTransactions, 2) : '0.00' }}
                 </div>
             </div>
             <div class="stat-card">
                 <div class="stat-label">Payment Methods</div>
                 <div class="stat-value">{{ $methods->count() }}</div>
             </div>
         </div>

         <h4 style="font-size: 12px; color: #111; font-weight: bold; margin-bottom: 10px;">Payment Method Breakdown:</h4>
         <table class="items-table">
             <thead>
                 <tr>
                     <th>Payment Method</th>
                     <th style="text-align: right;">Transactions</th>
                     <th style="text-align: right;">Total Sales</th>
                     <th style="text-align: right;">Percentage</th>
                     <th style="text-align: right;">Avg Transaction</th>
                 </tr>
             </thead>
             <tbody>
                 @foreach($methods as $method)
                 <tr>
                     <td>{{ ucfirst($method->payment_method) }}</td>
                     <td style="text-align: right;">{{ number_format($method->count) }}</td>
                     <td style="text-align: right;">TZS {{ number_format($method->total, 2) }}</td>
                     <td style="text-align: right;">
                         {{ $totalSales > 0 ? number_format(($method->total / $totalSales) * 100, 2) : '0.00' }}%
                     </td>
                     <td style="text-align: right;">
                         TZS {{ number_format($method->total / $method->count, 2) }}
                     </td>
                 </tr>
                 @endforeach
                 @if($methods->isEmpty())
                 <tr>
                     <td colspan="5" style="text-align: center;">No data found for this date range</td>
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
