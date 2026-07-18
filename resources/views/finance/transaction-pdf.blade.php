<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transaction {{ $entry->reference_number }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Manrope', sans-serif;
            font-size: 14px;
            line-height: 1.6;
            color: #000000;
            padding: 20px;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 20px;
            border-bottom: 2px solid #000000;
        }
        
        .header h1 {
            font-size: 24px;
            font-weight: 700;
            color: #000000;
            margin-bottom: 8px;
        }
        
        .header p {
            color: #666666;
            font-size: 14px;
        }
        
        .details {
            margin: 20px 0;
            padding: 15px;
            background-color: #f9f9f9;
            border-radius: 8px;
        }
        
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        
        .detail-item {
            margin: 8px 0;
        }
        
        .detail-label {
            color: #666666;
            font-size: 12px;
            font-weight: 500;
            margin-bottom: 4px;
        }
        
        .detail-value {
            color: #000000;
            font-weight: 600;
            font-size: 14px;
        }
        
        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }
        
        .badge-debit {
            background-color: #dbeafe;
            color: #1e40af;
        }
        
        .badge-credit {
            background-color: #dcfce7;
            color: #166534;
        }
        
        .amount {
            font-size: 20px;
            font-weight: 700;
        }
        
        .amount-debit {
            color: #1e40af;
        }
        
        .amount-credit {
            color: #166534;
        }
        
        .section-title {
            font-size: 18px;
            font-weight: 700;
            color: #000000;
            margin: 25px 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 1px solid #e5e5e5;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        
        th {
            background-color: #f3f4f6;
            color: #000000;
            font-weight: 600;
            font-size: 12px;
            padding: 12px;
            text-align: left;
            border-bottom: 2px solid #000000;
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #e5e5e5;
            font-size: 13px;
        }
        
        tr.highlighted {
            background-color: #fef3c7;
        }
        
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e5e5;
            color: #666666;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Transaction Details</h1>
            <p>Generated on {{ now()->format('d/m/Y H:i:s') }}</p>
        </div>
        
        <div class="details">
            <div class="details-grid">
                <div class="detail-item">
                    <div class="detail-label">Reference Number</div>
                    <div class="detail-value">{{ $entry->reference_number }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Date & Time</div>
                    <div class="detail-value">{{ $entry->created_at->format('d/m/Y H:i:s') }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Account</div>
                    <div class="detail-value">{{ $entry->account }}</div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Type</div>
                    <div class="detail-value">
                        <span class="badge {{ $entry->type === 'debit' ? 'badge-debit' : 'badge-credit' }}">
                            {{ strtoupper($entry->type) }}
                        </span>
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Amount</div>
                    <div class="detail-value amount {{ $entry->type === 'debit' ? 'amount-debit' : 'amount-credit' }}">
                        TZS {{ number_format($entry->amount, 2) }}
                    </div>
                </div>
                <div class="detail-item">
                    <div class="detail-label">Reference Type</div>
                    <div class="detail-value">{{ $entry->reference_type ?? 'N/A' }}</div>
                </div>
            </div>
            <div class="detail-item" style="margin-top: 15px;">
                <div class="detail-label">Description</div>
                <div class="detail-value">{{ $entry->description }}</div>
            </div>
        </div>
        
        <div class="section-title">Related Accounting Entries</div>
        <table>
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Reference No</th>
                    <th>Account</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Description</th>
                </tr>
            </thead>
            <tbody>
                @foreach($relatedEntries as $related)
                <tr class="{{ $related->id === $entry->id ? 'highlighted' : '' }}">
                    <td>{{ $related->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $related->reference_number }}</td>
                    <td>{{ $related->account }}</td>
                    <td>
                        <span class="badge {{ $related->type === 'debit' ? 'badge-debit' : 'badge-credit' }}">
                            {{ strtoupper($related->type) }}
                        </span>
                    </td>
                    <td>TZS {{ number_format($related->amount, 2) }}</td>
                    <td>{{ $related->description }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <div class="footer">
            <p>Feedtan Store - Transaction Report</p>
            <p>This document is automatically generated and should not be modified.</p>
        </div>
    </div>
</body>
</html>
