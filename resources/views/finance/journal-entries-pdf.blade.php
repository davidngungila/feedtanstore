<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Journal Entry - {{ $journalEntry->journal_number ?? 'N/A' }}</title>
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
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 9999px;
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
        }
        .status-balanced { background: #dcfce7; color: #166534; }
        .status-unbalanced { background: #fee2e2; color: #991b1b; }
        .status-manual { background: #dbeafe; color: #1d4ed8; }
        .status-system { background: #f3f4f6; color: #4b5563; }
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
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <div class="watermark">OFFICIAL</div>
    <div class="container">
        <div class="header">
            <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('feedtanstorelogo.png'))) }}" alt="FEEDTAN STORE" style="max-width: 150px; margin: 0 auto 8px auto;">
            <div class="sub-header" style="font-size: 10px; margin-top: 4px;">Inventory & Sales Management System</div>
            <div class="receipt-title">JOURNAL ENTRY</div>
        </div>

        <table style="width: 100%; margin-bottom: 15px;">
            <tr>
                <td>
                    <div class="label">Journal Number:</div>
                    <div class="value" style="font-size: 16px; color: #16a34a;">{{ $journalEntry->journal_number ?? 'N/A' }}</div>
                </td>
                <td style="text-align: right;">
                    <div class="label">Entry Date:</div>
                    <div class="value">{{ \Carbon\Carbon::parse($journalEntry->entry_date)->format('l, d F Y') }}</div>
                    <div class="label">Created At:</div>
                    <div class="value" style="font-size: 10px; color: #6b7280; font-weight: normal;">{{ $journalEntry->created_at->format('l, d F Y H:i:s') }}</div>
                </td>
            </tr>
        </table>

        <div class="info-card">
            <table class="details-table">
                <tr>
                    <td class="label">Description:</td>
                    <td class="value" style="font-size: 14px;">{{ $journalEntry->description }}</td>
                </tr>
                <tr>
                    <td class="label">Status:</td>
                    <td class="value">
                        <span class="status-badge {{ $journalEntry->is_balanced ? 'status-balanced' : 'status-unbalanced' }}">
                            {{ $journalEntry->is_balanced ? 'BALANCED' : 'UNBALANCED' }}
                        </span>
                        <span class="status-badge {{ $journalEntry->is_manual ? 'status-manual' : 'status-system' }}" style="margin-left: 8px;">
                            {{ $journalEntry->is_manual ? 'MANUAL' : 'SYSTEM' }}
                        </span>
                    </td>
                </tr>
            </table>
        </div>

        <h4 style="font-size: 12px; color: #111; font-weight: bold; margin-bottom: 10px;">Journal Entry Lines:</h4>
        <table class="items-table">
            <thead>
                <tr>
                    <th>Account Code</th>
                    <th>Account Name</th>
                    <th>Description</th>
                    <th style="text-align: right;">Debit</th>
                    <th style="text-align: right;">Credit</th>
                </tr>
            </thead>
            <tbody>
                @foreach($journalEntry->entries as $entry)
                <tr>
                    <td>{{ optional($entry->accountModel)->account_code ?? 'N/A' }}</td>
                    <td>{{ optional($entry->accountModel)->name ?? $entry->account }}</td>
                    <td>{{ $entry->description }}</td>
                    <td class="text-right">
                        @if($entry->type === 'debit')
                        TZS {{ number_format($entry->amount, 2) }}
                        @endif
                    </td>
                    <td class="text-right">
                        @if($entry->type === 'credit')
                        TZS {{ number_format($entry->amount, 2) }}
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="font-bold bg-gray-50">
                    <td colspan="3" style="padding: 8px; border-top: 2px solid #16a34a; text-align: right; font-weight: bold;">Total:</td>
                    <td style="padding: 8px; border-top: 2px solid #16a34a; text-align: right; font-weight: bold; color: #15803d;">TZS {{ number_format($journalEntry->total_debits, 2) }}</td>
                    <td style="padding: 8px; border-top: 2px solid #16a34a; text-align: right; font-weight: bold; color: #15803d;">TZS {{ number_format($journalEntry->total_credits, 2) }}</td>
                </tr>
            </tfoot>
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
