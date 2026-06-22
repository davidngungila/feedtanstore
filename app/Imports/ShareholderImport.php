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
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Row;
use Maatwebsite\Excel\Validators\Failure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ShareholderImport implements OnEachRow, WithHeadingRow, WithValidation, SkipsOnFailure
{
    protected $importedCount = 0;
    protected $failures = [];

    public function onRow(Row $row)
    {
        $row = $row->toArray();
        
        // Normalize the data before processing
        $normalized = $this->normalizeRow($row);

        DB::beginTransaction();

        try {
            // Create shareholder
            $shareholder = Shareholder::create([
                'name'    => $normalized['name'],
                'email'   => $normalized['email'],
                'phone'   => $normalized['phone'],
                'address' => $normalized['address'],
            ]);

            $this->importedCount++;

            // Create shares if provided
            if ($normalized['number_of_shares'] > 0) {
                $storeSettings = StoreSetting::first();
                $sharePrice = $normalized['share_price'] ?? ($storeSettings->share_price ?? 0);
                $numberOfShares = $normalized['number_of_shares'];
                $totalAmount = $numberOfShares * $sharePrice;
                $date = $normalized['date'] ?? now()->format('Y-m-d');
                $userId = Auth::check() ? Auth::id() : null;

                // Create share record
                $share = $shareholder->shares()->create([
                    'number_of_shares' => $numberOfShares,
                    'share_price' => $sharePrice,
                    'total_amount' => $totalAmount,
                    'date' => $date,
                    'description' => $normalized['description'],
                ]);

                // Create capital record
                $capital = Capital::create([
                    'amount' => $totalAmount,
                    'description' => $normalized['description'] ?? "Shares issued to {$shareholder->name}",
                    'transaction_type' => 'add',
                    'date' => $date,
                    'user_id' => $userId,
                ]);

                // Create accounting entries
                $this->createCapitalAccountingEntries($capital);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->failures[] = "Row " . ($index ?? 'unknown') . ": " . $e->getMessage();
        }
    }

    public function prepareForValidation($data, $index)
    {
        // Normalize data before validation
        return $this->normalizeRow($data);
    }

    protected function normalizeRow(array $row): array
    {
        return [
            'name'              => isset($row['name']) ? trim((string)$row['name']) : null,
            'email'             => isset($row['email']) ? trim((string)$row['email']) : null,
            'phone'             => isset($row['phone']) ? trim((string)$row['phone']) : null,
            'address'           => isset($row['address']) ? trim((string)$row['address']) : null,
            'number_of_shares'  => isset($row['number_of_shares']) ? (int)$row['number_of_shares'] : 0,
            'share_price'       => isset($row['share_price']) ? (float)$row['share_price'] : null,
            'date'              => isset($row['date']) ? (string)$row['date'] : null,
            'description'       => isset($row['description']) ? trim((string)$row['description']) : 'Imported shares',
        ];
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
            // Debit cash/bank, credit capital
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

    public function getImportedCount()
    {
        return $this->importedCount;
    }

    public function getFailures()
    {
        return $this->failures;
    }

    public function onFailure(Failure ...$failures)
    {
        foreach ($failures as $failure) {
            $this->failures[] = "Row " . $failure->row() . ": " . implode(", ", $failure->errors());
        }
    }
}
