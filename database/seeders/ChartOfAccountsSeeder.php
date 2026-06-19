<?php

namespace Database\Seeders;

use App\Models\Account;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ChartOfAccountsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Assets
        $cash = Account::create([
            'account_code' => '1001',
            'name' => 'Cash',
            'type' => 'Asset',
            'description' => 'Cash on hand',
            'is_active' => true,
        ]);

        $bankAccount = Account::create([
            'account_code' => '1004',
            'name' => 'Bank Account',
            'type' => 'Asset',
            'description' => 'Cash in bank accounts',
            'is_active' => true,
        ]);

        $mobileMoney = Account::create([
            'account_code' => '1005',
            'name' => 'Mobile Money',
            'type' => 'Asset',
            'description' => 'Mobile money balances',
            'is_active' => true,
        ]);

        $inventory = Account::create([
            'account_code' => '1002',
            'name' => 'Inventory',
            'type' => 'Asset',
            'description' => 'Stock of products',
            'is_active' => true,
        ]);

        $accountsReceivable = Account::create([
            'account_code' => '1003',
            'name' => 'Accounts Receivable',
            'type' => 'Asset',
            'description' => 'Money owed by customers',
            'is_active' => true,
        ]);

        // Liabilities
        $accountsPayable = Account::create([
            'account_code' => '2001',
            'name' => 'Accounts Payable',
            'type' => 'Liability',
            'description' => 'Money owed to suppliers',
            'is_active' => true,
        ]);

        // Equity
        $retainedEarnings = Account::create([
            'account_code' => '3001',
            'name' => 'Retained Earnings',
            'type' => 'Equity',
            'description' => 'Retained earnings',
            'is_active' => true,
        ]);
        $capitalAccount = Account::create([
            'account_code' => '3002',
            'name' => 'Capital',
            'type' => 'Equity',
            'description' => 'Owner\'s capital',
            'is_active' => true,
        ]);

        // Revenue
        $sales = Account::create([
            'account_code' => '4001',
            'name' => 'Sales',
            'type' => 'Revenue',
            'description' => 'Revenue from product sales',
            'is_active' => true,
        ]);

        $income = Account::create([
            'account_code' => '4002',
            'name' => 'Income',
            'type' => 'Revenue',
            'description' => 'Other income',
            'is_active' => true,
        ]);

        // Expenses
        $cogs = Account::create([
            'account_code' => '5001',
            'name' => 'Cost of Goods Sold',
            'type' => 'Expense',
            'description' => 'Cost of products sold',
            'is_active' => true,
        ]);

        $expenses = Account::create([
            'account_code' => '5002',
            'name' => 'Expenses',
            'type' => 'Expense',
            'description' => 'General operating expenses',
            'is_active' => true,
        ]);
    }
}
