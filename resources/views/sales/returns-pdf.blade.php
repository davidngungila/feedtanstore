<!DOCTYPE html>
 <html>
 <head>
     <meta charset="utf-8">
     <title>Return Receipt - {{ $return->return_number ?? 'N/A' }}</title>
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
          .amount-section {
              background: linear-gradient(to right, #f0fdf4, #ffffff);
              border: 1px solid #bcf0da;
              padding: 15px;
              margin-bottom: 20px;
              border-radius: 8px;
              position: relative;
          }
          .amount-label {
              font-size: 10px;
              color: #16a34a;
              font-weight: 900;
              text-transform: uppercase;
              margin-bottom: 3px;
          }
          .amount-value {
              font-size: 28px;
              font-weight: 900;
              color: #15803d;
          }
          .amount-words {
              font-size: 11px;
              font-style: italic;
              color: #6b7280;
              margin-top: 5px;
              text-transform: capitalize;
          }
          .info-grid {
              width: 100%;
              margin-bottom: 20px;
          }
          .info-card {
              border: 1px solid #e5e7eb;
              border-radius: 6px;
              padding: 12px;
              background: #fff;
          }
          .qr-code-box {
              text-align: right;
          }
          .status-badge {
              display: inline-block;
              padding: 3px 8px;
              border-radius: 9999px;
              font-size: 9px;
              font-weight: 900;
              text-transform: uppercase;
          }
          .status-verified { background: #dcfce7; color: #166534; }
          .status-pending { background: #fef3c7; color: #92400e; }
          .footer {
              margin-top: 40px;
              text-align: center;
              font-size: 10px;
              color: #6b7280;
              border-top: 1px dashed #e5e7eb;
              padding-top: 15px;
          }
          .signature-grid {
              margin-top: 50px;
              width: 100%;
          }
          .sig-line {
              border-top: 1px solid #374151;
              width: 160px;
              margin: 0 auto 5px;
          }
          .sig-text {
              font-size: 9px;
              font-weight: bold;
              color: #4b5563;
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
      @php
         $isSuccessful = true;
         $customerName = $return->sale->customer->name ?? 'Walk-in Customer';
      @endphp
      <div class="watermark">{{ $isSuccessful ? 'OFFICIAL' : 'PROVISIONAL' }}</div>

      <div class="container">
          <div class="header">
              <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('feedtanstorelogo.png'))) }}" alt="FEEDTAN STORE" style="max-width: 150px; margin: 0 auto 8px auto;">
              <div class="sub-header" style="font-size: 10px; margin-top: 4px;">Inventory & Sales Management System</div>
              <div class="receipt-title">OFFICIAL RETURN RECEIPT</div>
          </div>

          <table style="width: 100%; margin-bottom: 15px;">
              <tr>
                  <td>
                      <div class="label">Return Number:</div>
                      <div class="value" style="font-size: 16px; color: #16a34a;">{{ $return->return_number ?? 'N/A' }}</div>
                  </td>
                  <td style="text-align: right;">
                      <div class="label">Date Issued:</div>
                      <div class="value">{{ $return->created_at->format('l, d F Y') }}</div>
                      <div class="value" style="font-size: 10px; color: #6b7280; font-weight: normal;">Time: {{ $return->created_at->format('H:i:s') }}</div>
                  </td>
              </tr>
          </table>

          <div class="info-card">
              <table class="details-table">
                  <tr>
                      <td class="label">Customer:</td>
                      <td class="value" style="font-size: 14px;">{{ strtoupper($customerName) }}</td>
                      <td rowspan="4" class="qr-code-box">
                      </td>
                  </tr>
                  <tr>
                      <td class="label">Original Invoice #:</td>
                      <td class="value">{{ $return->sale->invoice_number ?? 'N/A' }}</td>
                  </tr>
                  <tr>
                      <td class="label">Return ID:</td>
                      <td class="value">{{ $return->id }}</td>
                  </tr>
                  <tr>
                      <td class="label">Reason:</td>
                      <td class="value">{{ strtoupper($return->reason ?? 'N/A') }}</td>
                  </tr>
                  <tr>
                      <td class="label">Processed By:</td>
                      <td class="value">
                          {{ strtoupper($return->user->name ?? 'N/A') }}
                          <span class="status-badge status-verified">
                              • CONFIRMED
                          </span>
                      </td>
                  </tr>
              </table>
          </div>

          <h4 style="font-size: 12px; color: #111; font-weight: bold; margin-bottom: 10px;">Returned Items:</h4>
          <table class="items-table">
              <thead>
                  <tr>
                      <th>Product</th>
                      <th style="text-align: right;">Qty</th>
                      <th style="text-align: right;">Unit Price</th>
                      <th style="text-align: right;">Total</th>
                  </tr>
              </thead>
              <tbody>
                  @foreach($return->items as $item)
                  <tr>
                      <td>{{ $item->saleItem->product->name ?? 'Product' }}</td>
                      <td style="text-align: right;">{{ $item->quantity }}</td>
                      <td style="text-align: right;">TZS {{ number_format($item->unit_price, 2) }}</td>
                      <td style="text-align: right;">TZS {{ number_format($item->total, 2) }}</td>
                  </tr>
                  @endforeach
              </tbody>
          </table>

          <div class="amount-section">
              <div class="amount-label">Total Return Amount</div>
              <div class="amount-value">TZS {{ number_format($return->total, 2) }}</div>
              @php
                  $amount = $return->total;
                  $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
                  $words = $f->format($amount);
              @endphp
              <div class="amount-words">Amount in words: {{ $words }} Tanzanian Shillings Only</div>
          </div>

          <div style="font-size: 10px; color: #4b5563; padding: 10px; background: #f9fafb; border-radius: 6px; border-left: 3px solid #16a34a;">
              <strong>Transaction Reference:</strong> {{ $return->return_number ?? 'N/A' }}<br>
              <strong>System Remarks:</strong> Products returned successfully
          </div>

          <table class="signature-grid">
              <tr>
                  <td style="text-align: center; width: 50%;">
                      <div class="sig-line"></div>
                      <div class="sig-text">STORE MANAGER / CASHIER</div>
                      <div style="font-size: 8px; color: #9ca3af;">(Digital Seal Applied)</div>
                  </td>
                  <td style="text-align: center; width: 50%;">
                      <div class="sig-line"></div>
                      <div class="sig-text">CUSTOMER'S ACKNOWLEDGMENT</div>
                  </td>
              </tr>
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
