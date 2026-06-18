<!DOCTYPE html>
 <html>
 <head>
     <meta charset="utf-8">
     <title>Supplier Payment - {{ $payment->payment_number ?? 'N/A' }}</title>
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
              width: 150px;
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
          .status-canceled { background: #fee2e2; color: #991b1b; }
          .footer {
              margin-top: 40px;
              text-align: center;
              font-size: 10px;
              color: #6b7280;
              border-top: 1px dashed #e5e7eb;
              padding-top: 15px;
          }
      </style>
  </head>
  <body>
      @php
         $statusBadgeClass = match($payment->status) {
             'completed' => 'status-verified',
             'pending' => 'status-pending',
             default => 'status-canceled'
         };
         $statusText = strtoupper($payment->status);
      @endphp
      <div class="watermark">OFFICIAL</div>

      <div class="container">
          <div class="header">
              <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('feedtanstorelogo.png'))) }}" alt="FEEDTAN STORE" style="max-width: 150px; margin: 0 auto 8px auto;">
              <div class="sub-header" style="font-size: 10px; margin-top: 4px;">Inventory & Sales Management System</div>
              <div class="receipt-title">SUPPLIER PAYMENT</div>
          </div>

          <table style="width: 100%; margin-bottom: 15px;">
              <tr>
                  <td>
                      <div class="label">Payment Number:</div>
                      <div class="value" style="font-size: 16px; color: #16a34a;">{{ $payment->payment_number ?? 'N/A' }}</div>
                  </td>
                  <td style="text-align: right;">
                      <div class="label">Date Issued:</div>
                      <div class="value">{{ $payment->created_at->format('l, d F Y') }}</div>
                      <div class="value" style="font-size: 10px; color: #6b7280; font-weight: normal;">Time: {{ $payment->created_at->format('H:i:s') }}</div>
                  </td>
              </tr>
          </table>

          <div class="info-card">
              <table class="details-table">
                  <tr>
                      <td class="label">Supplier:</td>
                      <td class="value" style="font-size: 14px;">{{ strtoupper($payment->supplier->name ?? 'N/A') }}</td>
                  </tr>
                  @if($payment->purchaseOrder)
                  <tr>
                      <td class="label">Purchase Order:</td>
                      <td class="value">{{ $payment->purchaseOrder->po_number }}</td>
                  </tr>
                  @endif
                  <tr>
                      <td class="label">Payment Method:</td>
                      <td class="value">{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                  </tr>
                  @if($payment->transaction_id)
                  <tr>
                      <td class="label">Transaction ID:</td>
                      <td class="value">{{ $payment->transaction_id }}</td>
                  </tr>
                  @endif
                  <tr>
                      <td class="label">Payment Date:</td>
                      <td class="value">{{ $payment->payment_date ? date('l, d F Y', strtotime($payment->payment_date)) : '-' }}</td>
                  </tr>
                  <tr>
                      <td class="label">Status:</td>
                      <td class="value">
                          <span class="status-badge {{ $statusBadgeClass }}">
                              {{ $statusText }}
                          </span>
                      </td>
                  </tr>
                  @if($payment->notes)
                  <tr>
                      <td class="label">Notes:</td>
                      <td class="value">{{ $payment->notes }}</td>
                  </tr>
                  @endif
              </table>
          </div>

          <div class="info-card">
              <table class="details-table" style="width: 100%;">
                  <tr>
                      <td class="label" style="font-size: 14px; padding-top: 8px;">Total Amount:</td>
                      <td class="value" style="font-size: 16px; text-align: right; padding-top: 8px; color: #15803d;">TZS {{ number_format($payment->amount, 2) }}</td>
                  </tr>
              </table>
          </div>

          @php
              $amount = $payment->amount;
              $f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
              $words = $f->format($amount);
          @endphp

          <div class="amount-section">
              <div class="amount-label">Amount in Words</div>
              <div class="amount-words">{{ $words }} Tanzanian Shillings Only</div>
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
