<?php

namespace App\Http\Controllers;

use App\Models\Income;
use App\Models\BankAccount;
use App\Models\MobileMoneyAccount;
use App\Models\AccountingEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IncomeController extends Controller
{
    public function index()
    {
        $incomes = Income::with(['user', 'bankAccount', 'mobileMoneyAccount'])->latest()->get();
        return view('finance.income', compact('incomes'));
    }

    public function create()
    {
        $bankAccounts = BankAccount::where('is_active', true)->get();
        $mobileMoneyAccounts = MobileMoneyAccount::where('is_active', true)->get();
        return view('finance.income-create', compact('bankAccounts', 'mobileMoneyAccounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'mobile_money_account_id' => 'nullable|exists:mobile_money_accounts,id'
        ]);

        $income = Income::create([
            'reference_number' => 'INC-' . strtoupper(uniqid()),
            'date' => $request->date,
            'category' => $request->category,
            'description' => $request->description,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'bank_account_id' => $request->bank_account_id,
            'mobile_money_account_id' => $request->mobile_money_account_id,
            'user_id' => Auth::id()
        ]);

        $this->createAccountingEntries($income, $request);

        return redirect()->route('finance.income')->with('success', 'Income recorded successfully!');
    }
    
    public function show(Income $income)
    {
        $income->load(['user', 'bankAccount', 'mobileMoneyAccount']);
        $entries = AccountingEntry::where('reference_number', $income->reference_number)->get();
        return view('finance.income-show', compact('income', 'entries'));
    }
    
    public function edit(Income $income)
    {
        $bankAccounts = BankAccount::where('is_active', true)->get();
        $mobileMoneyAccounts = MobileMoneyAccount::where('is_active', true)->get();
        return view('finance.income-edit', compact('income', 'bankAccounts', 'mobileMoneyAccounts'));
    }
    
    public function update(Request $request, Income $income)
    {
        $request->validate([
            'date' => 'required|date',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'mobile_money_account_id' => 'nullable|exists:mobile_money_accounts,id'
        ]);
        
        $this->reverseAccountingEntries($income);
        
        $income->update([
            'date' => $request->date,
            'category' => $request->category,
            'description' => $request->description,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'bank_account_id' => $request->bank_account_id,
            'mobile_money_account_id' => $request->mobile_money_account_id,
        ]);
        
        $this->createAccountingEntries($income, $request);
        
        return redirect()->route('finance.income')->with('success', 'Income updated successfully!');
    }
    
    public function destroy(Income $income)
    {
        $this->reverseAccountingEntries($income);
        $income->delete();
        return redirect()->route('finance.income')->with('success', 'Income deleted successfully!');
    }
    
    private function createAccountingEntries(Income $income, Request $request)
    {
        $incomeAccount = \App\Models\Account::where('name', 'Other Income')->first();
        $cashAccount = \App\Models\Account::where('name', 'Cash')->first();
        $bankAccount = \App\Models\Account::where('name', 'Bank Account')->first();
        $mobileMoneyAccount = \App\Models\Account::where('name', 'Mobile Money')->first();

        $journalNumber = 'JE-INCOME-' . date('Ymd') . '-' . str_pad(\App\Models\JournalEntry::count() + 1, 4, '0', STR_PAD_LEFT);

        $journalEntry = \App\Models\JournalEntry::create([
            'journal_number' => $journalNumber,
            'entry_date' => now(),
            'description' => 'Income: ' . $income->reference_number,
            'reference_type' => Income::class,
            'reference_id' => $income->id,
            'is_manual' => false,
        ]);

        // Debit the payment source
        if ($request->payment_method === 'cash') {
            AccountingEntry::create([
                'journal_entry_id' => $journalEntry->id,
                'reference_number' => $income->reference_number,
                'reference_type' => Income::class,
                'account' => 'Cash',
                'account_id' => $cashAccount?->id,
                'type' => 'debit',
                'amount' => $income->amount,
                'description' => 'Income received'
            ]);
        } elseif ($request->bank_account_id) {
            AccountingEntry::create([
                'journal_entry_id' => $journalEntry->id,
                'reference_number' => $income->reference_number,
                'reference_type' => Income::class,
                'account' => 'Bank Account',
                'account_id' => $bankAccount?->id,
                'type' => 'debit',
                'amount' => $income->amount,
                'description' => 'Income received'
            ]);
            
            $bankAccountModel = BankAccount::find($request->bank_account_id);
            $bankAccountModel->update(['balance' => $bankAccountModel->balance + $income->amount]);
        } elseif ($request->mobile_money_account_id) {
            AccountingEntry::create([
                'journal_entry_id' => $journalEntry->id,
                'reference_number' => $income->reference_number,
                'reference_type' => Income::class,
                'account' => 'Mobile Money',
                'account_id' => $mobileMoneyAccount?->id,
                'type' => 'debit',
                'amount' => $income->amount,
                'description' => 'Income received'
            ]);
            
            $mobileMoneyAccountModel = MobileMoneyAccount::find($request->mobile_money_account_id);
            $mobileMoneyAccountModel->update(['balance' => $mobileMoneyAccountModel->balance + $income->amount]);
        }

        // Credit Income
        AccountingEntry::create([
            'journal_entry_id' => $journalEntry->id,
            'reference_number' => $income->reference_number,
            'reference_type' => Income::class,
            'account' => 'Income',
            'account_id' => $incomeAccount?->id,
            'type' => 'credit',
            'amount' => $income->amount,
            'description' => $request->category
        ]);
    }
    
    private function reverseAccountingEntries(Income $income)
    {
        $oldEntries = AccountingEntry::where('reference_number', $income->reference_number)->get();

        $journalNumber = 'JE-INCOME-REV-' . date('Ymd') . '-' . str_pad(\App\Models\JournalEntry::count() + 1, 4, '0', STR_PAD_LEFT);
        $journalEntry = \App\Models\JournalEntry::create([
            'journal_number' => $journalNumber,
            'entry_date' => now(),
            'description' => 'Reversal of Income: ' . $income->reference_number,
            'reference_type' => Income::class,
            'reference_id' => $income->id,
            'is_manual' => false,
        ]);
        
        foreach ($oldEntries as $entry) {
            $account = \App\Models\Account::where('name', $entry->account)->first();

            // Reverse entry
            AccountingEntry::create([
                'journal_entry_id' => $journalEntry->id,
                'reference_number' => $income->reference_number . '-REV',
                'reference_type' => Income::class,
                'account' => $entry->account,
                'account_id' => $account?->id,
                'type' => $entry->type === 'debit' ? 'credit' : 'debit',
                'amount' => $entry->amount,
                'description' => 'Reversal of ' . $entry->description
            ]);
            
            if ($entry->account === 'Bank Account' && $income->bank_account_id) {
                $bankAccount = BankAccount::find($income->bank_account_id);
                if ($entry->type === 'debit') {
                    $bankAccount->decrement('balance', $income->amount);
                } else {
                    $bankAccount->increment('balance', $income->amount);
                }
            } elseif ($entry->account === 'Mobile Money' && $income->mobile_money_account_id) {
                $mobileAccount = MobileMoneyAccount::find($income->mobile_money_account_id);
                if ($entry->type === 'debit') {
                    $mobileAccount->decrement('balance', $income->amount);
                } else {
                    $mobileAccount->increment('balance', $income->amount);
                }
            }
        }
        
        $oldEntries->each->delete();
    }
}
