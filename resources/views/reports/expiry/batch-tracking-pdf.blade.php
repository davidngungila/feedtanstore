<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Batch Tracking Report</title>
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
             <div class="receipt-title">BATCH TRACKING REPORT</div>
         </div>

         <div class="info-card">
             <div style="font-size: 12px; color: #4b5563;">
                 <strong>Report Date:</strong> {{ now()->format('l, d F Y H:i:s') }}
             </div>
         </div>

         <h4 style="font-size: 12px; color: #111; font-weight: bold; margin-bottom: 10px;">Batch Products:</h4>
         <table class="items-table">
             <thead>
                 <tr>
                     <th>Product</th>
                     <th>Category</th>
                     <th>Brand</th>
                     <th>Batch Number</th>
                     <th style="text-align: right;">Quantity</th>
                     <th>Expiry Date</th>
                 </tr>
             </thead>
             <tbody>
                 @foreach($products as $product)
                 <tr>
                     <td>{{ $product->name }}</td>
                     <td>{{ $product->category ? $product->category->name : 'N/A' }}</td>
                     <td>{{ $product->brand ? $product->brand->name : 'N/A' }}</td>
                     <td>{{ $product->batch_number }}</td>
                     <td style="text-align: right;">{{ number_format($product->quantity) }}</td>
                     <td>{{ $product->expiry_date ? $product->expiry_date->format('d F Y') : 'N/A' }}</td>
                 </tr>
                 @endforeach
                 @if($products->isEmpty())
                 <tr>
                     <td colspan="6" style="text-align: center;">No products with batch numbers</td>
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
