<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cash Reconciliation Report - {{ $date }}</title>
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
         .breakdown-card {
             background: #f3f4f6;
             padding: 15px;
             border-radius: 6px;
             margin-bottom: 20px;
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
             <div class="receipt-title">CASH RECONCILIATION REPORT</div>
         </div>

         <table style="width: 100%; margin-bottom: 15px;">
             <tr>
                 <td>
                     <div style="font-size: 10px; color: #4b5563; font-weight: 800; text-transform: uppercase;">Date:</div>
                     <div style="font-size: 16px; color: #16a34a; font-weight: 900;">{{ \Carbon\Carbon::parse($date)->format('l, d F Y') }}</div>
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
                 <div class="stat-label">Cash Sales</div>
                 <div class="stat-value">TZS {{ number_format($cashSales, 2) }}</div>
             </div>
             <div class="stat-card">
                 <div class="stat-label">Expenses</div>
                 <div class="stat-value">TZS {{ number_format($expenses, 2) }}</div>
             </div>
             <div class="stat-card">
                 <div class="stat-label">Expected Cash</div>
                 <div class="stat-value">TZS {{ number_format($expectedCash, 2) }}</div>
             </div>
             <div class="stat-card">
                 <div class="stat-label">Difference</div>
                 <div class="stat-value" style="{{ $difference < 0 ? 'color: #dc2626;' : '' }}">
                     TZS {{ number_format($difference, 2) }}
                 </div>
             </div>
         </div>

         <div class="breakdown-card">
             <h4 style="font-size: 12px; color: #111; font-weight: bold; margin-bottom: 10px;">Reconciliation Breakdown:</h4>
             <div style="line-height: 1.8;">
                 <div style="display: flex; justify-content: space-between;">
                     <span style="font-size: 11px;">Total Opening Cash</span>
                     <span style="font-size: 11px; font-weight: 700;">TZS {{ number_format($totalOpeningCash, 2) }}</span>
                 </div>
                 <div style="display: flex; justify-content: space-between;">
                     <span style="font-size: 11px;">+ Cash Sales</span>
                     <span style="font-size: 11px; font-weight: 700;">TZS {{ number_format($cashSales, 2) }}</span>
                 </div>
                 <div style="display: flex; justify-content: space-between;">
                     <span style="font-size: 11px;">- Expenses</span>
                     <span style="font-size: 11px; font-weight: 700;">TZS {{ number_format($expenses, 2) }}</span>
                 </div>
                 <div style="display: flex; justify-content: space-between; border-top: 1px solid #e5e7eb; padding-top: 5px; margin-top: 5px;">
                     <span style="font-size: 11px; font-weight: 800;">Expected Cash</span>
                     <span style="font-size: 11px; font-weight: 800;">TZS {{ number_format($expectedCash, 2) }}</span>
                 </div>
                 <div style="display: flex; justify-content: space-between;">
                     <span style="font-size: 11px;">Actual Closing Cash</span>
                     <span style="font-size: 11px; font-weight: 700;">TZS {{ number_format($totalClosingCash, 2) }}</span>
                 </div>
                 <div style="display: flex; justify-content: space-between; border-top: 2px solid #16a34a; padding-top: 5px; margin-top: 5px;">
                     <span style="font-size: 12px; font-weight: 900;">Difference</span>
                     <span style="font-size: 14px; font-weight: 900; {{ $difference < 0 ? 'color: #dc2626;' : '' }}">
                         TZS {{ number_format($difference, 2) }}
                     </span>
                 </div>
             </div>
         </div>

         <h4 style="font-size: 12px; color: #111; font-weight: bold; margin-bottom: 10px;">Shift Details:</h4>
         <table class="items-table">
             <thead>
                 <tr>
                     <th>Shift ID</th>
                     <th>Cashier</th>
                     <th>Opened At</th>
                     <th>Closed At</th>
                     <th style="text-align: right;">Opening Cash</th>
                     <th style="text-align: right;">Closing Cash</th>
                     <th>Status</th>
                 </tr>
             </thead>
             <tbody>
                 @foreach($shifts as $shift)
                 <tr>
                     <td>#{{ $shift->id }}</td>
                     <td>{{ $shift->cashier ? $shift->cashier->name : 'N/A' }}</td>
                     <td>{{ $shift->created_at->format('H:i') }}</td>
                     <td>{{ $shift->closed_at ? $shift->closed_at->format('H:i') : '-' }}</td>
                     <td style="text-align: right;">TZS {{ number_format($shift->opening_cash, 2) }}</td>
                     <td style="text-align: right;">{{ $shift->closing_cash ? 'TZS ' . number_format($shift->closing_cash, 2) : '-' }}</td>
                     <td>{{ ucfirst($shift->status) }}</td>
                 </tr>
                 @endforeach
                 @if($shifts->isEmpty())
                 <tr>
                     <td colspan="7" style="text-align: center;">No shifts found for this date</td>
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
