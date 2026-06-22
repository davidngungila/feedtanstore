<?php

namespace App\Imports;

use App\Models\Shareholder;
use App\Models\Share;
use App\Models\Capital;
use App\Models\AccountingEntry;
use App\Models\StoreSetting;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Row;
use Illuminate\Support\Facades\Auth;

class ShareholderImport implements OnEachRow, WithHeadingRow, WithValidation
{
    public function onRow(Row $row)
    {
        $row = $row->toArray();

        $shareholder = Shareholder::create([
            'name'    => $row['name'],
            'email'   => $row['email'] ?? null,
            'phone'   => $row['phone'] ?? null,
            'address' => $row['address'] ?? null,
        ]);

        if (isset($row['number_of_shares']) && $row['number_of_shares'] > 0) {
            $storeSettings = StoreSetting::first();
            $sharePrice = $row['share_price'] ?? $storeSettings->share_price ?? 0;
            $numberOfShares = $row['number_of_shares'];
            $totalAmount = $numberOfShares * $sharePrice;
            $date = $row['date'] ?? date('Y-m-d');

            $share = $shareholder->shares()->create([
                'number_of_shares' => $numberOfShares,
                'share_price' => $sharePrice,
                'total_amount' => $totalAmount,
                'date' => $date,
                'description' => $row['description'] ?? 'Imported shares',
            ]);

            $capital = Capital::create([
                'amount' => $totalAmount,
                'description' => $row['description'] ?? "Shares issued to {$shareholder->name}",
                'transaction_type' => 'add',
                'date' => $date,
                'user_id' => Auth::id(),
            ]);

            $this->createCapitalAccountingEntries($capital);
        }
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'number_of_shares' => 'nullable|integer|min:0',
            'share_price' => 'nullable|numeric|min:0',
            'date' => 'nullable|date',
            'description' => 'nullable|string',
        ];
    }

    protected function createCapitalAccountingEntries(Capital $capital)
    {
        $cashAccount = \App\Models\Account::where('name', 'Cash')->first();
        $capitalAccount = \App\Models\Account::where('name', 'Capital')->first();

        $journalNumber = 'JE-SHARE-' . date('Ymd') . '-' . str_pad(\App\Models\JournalEntry::count() + 1, 4, '0', STR_PAD_LEFT);

        $journalEntry = \App\Models\JournalEntry::create([
            'journal_number' => $journalNumber,
            'entry_number' => $journalNumber,
            'entry_date' => now(),
            'description' => 'Shares issued',
            'reference_type' => Capital::class,
            'reference_id' => $capital->id,
            'is_manual' => false,
        ]);

        if ($capital->transaction_type === 'add') {
            AccountingEntry::create([
                'journal_entry_id' => $journalEntry->id,
                'reference_number' => 'CAP-' . $capital->id,
                'reference_type' => Capital::class,
                'account' => 'Cash',
                'account_id' => $cashAccount?->id,
                'type' => 'debit',
                'amount' => $capital->amount,
                'description' => $capital->description ?: 'Capital added',
            ]);

            AccountingEntry::create([
                'journal_entry_id' => $journalEntry->id,
                'reference_number' => 'CAP-' . $capital->id,
                'reference_type' => Capital::class,
                'account' => 'Capital',
                'account_id' => $capitalAccount?->id,
                'type' => 'credit',
                'amount' => $capital->amount,
                'description' => $capital->description ?: 'Capital added',
            ]);
        }
    }
}
