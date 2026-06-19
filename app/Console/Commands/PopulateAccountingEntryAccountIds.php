<?php

namespace App\Console\Commands;

use App\Models\AccountingEntry;
use App\Models\Account;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('app:populate-accounting-entry-account-ids')]
#[Description('Populate account_id for existing accounting entries based on account name')]
class PopulateAccountingEntryAccountIds extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to populate account_id for accounting entries...');

        $entries = AccountingEntry::whereNull('account_id')->get();
        $count = 0;

        foreach ($entries as $entry) {
            $account = Account::where('name', $entry->account)->first();
            if ($account) {
                $entry->account_id = $account->id;
                $entry->save();
                $count++;
            }
        }

        $this->info("Successfully populated account_id for {$count} entries!");
    }
}
