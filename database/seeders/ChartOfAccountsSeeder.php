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

        $equipment = Account::create([
            'account_code' => '1006',
            'name' => 'Equipment',
            'type' => 'Asset',
            'description' => 'Store equipment and fixtures',
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

        $taxesPayable = Account::create([
            'account_code' => '2002',
            'name' => 'Taxes Payable',
            'type' => 'Liability',
            'description' => 'Taxes owed to government',
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

        $deliveryIncome = Account::create([
            'account_code' => '4003',
            'name' => 'Delivery Income',
            'type' => 'Revenue',
            'description' => 'Income from delivery fees',
            'is_active' => true,
        ]);

        $discountsReceived = Account::create([
            'account_code' => '4004',
            'name' => 'Discounts Received',
            'type' => 'Revenue',
            'description' => 'Discounts from suppliers',
            'is_active' => true,
        ]);

        $otherIncome = Account::create([
            'account_code' => '4002',
            'name' => 'Other Income',
            'type' => 'Revenue',
            'description' => 'Other miscellaneous income',
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

        $rentExpense = Account::create([
            'account_code' => '5003',
            'name' => 'Rent Expense',
            'type' => 'Expense',
            'description' => 'Store rent payments',
            'is_active' => true,
        ]);

        $utilitiesExpense = Account::create([
            'account_code' => '5004',
            'name' => 'Utilities Expense',
            'type' => 'Expense',
            'description' => 'Electricity, water, and internet bills',
            'is_active' => true,
        ]);

        $salariesExpense = Account::create([
            'account_code' => '5005',
            'name' => 'Salaries & Wages',
            'type' => 'Expense',
            'description' => 'Employee salaries and wages',
            'is_active' => true,
        ]);

        $deliveryExpense = Account::create([
            'account_code' => '5006',
            'name' => 'Delivery Expense',
            'type' => 'Expense',
            'description' => 'Costs related to product delivery',
            'is_active' => true,
        ]);

        $marketingExpense = Account::create([
            'account_code' => '5007',
            'name' => 'Marketing & Advertising',
            'type' => 'Expense',
            'description' => 'Marketing and advertising costs',
            'is_active' => true,
        ]);

        $suppliesExpense = Account::create([
            'account_code' => '5008',
            'name' => 'Office & Store Supplies',
            'type' => 'Expense',
            'description' => 'Office and store supplies',
            'is_active' => true,
        ]);

        $discountsGiven = Account::create([
            'account_code' => '5009',
            'name' => 'Discounts Given',
            'type' => 'Expense',
            'description' => 'Discounts provided to customers',
            'is_active' => true,
        ]);

        $otherExpenses = Account::create([
            'account_code' => '5002',
            'name' => 'Other Expenses',
            'type' => 'Expense',
            'description' => 'Other miscellaneous operating expenses',
            'is_active' => true,
        ]);
    }
}
