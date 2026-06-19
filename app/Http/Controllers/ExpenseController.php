<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\BankAccount;
use App\Models\MobileMoneyAccount;
use App\Models\AccountingEntry;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with(['user', 'bankAccount', 'mobileMoneyAccount', 'budget'])->latest()->get();
        return view('finance.expenses', compact('expenses'));
    }

    public function create()
    {
        $bankAccounts = BankAccount::where('is_active', true)->get();
        $mobileMoneyAccounts = MobileMoneyAccount::where('is_active', true)->get();
        $budgets = Budget::all();
        return view('finance.expenses-create', compact('bankAccounts', 'mobileMoneyAccounts', 'budgets'));
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
            'mobile_money_account_id' => 'nullable|exists:mobile_money_accounts,id',
            'budget_id' => 'nullable|exists:budgets,id'
        ]);

        $expense = Expense::create([
            'reference_number' => 'EXP-' . strtoupper(uniqid()),
            'date' => $request->date,
            'category' => $request->category,
            'description' => $request->description,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'bank_account_id' => $request->bank_account_id,
            'mobile_money_account_id' => $request->mobile_money_account_id,
            'budget_id' => $request->budget_id,
            'user_id' => Auth::id()
        ]);

        $this->createAccountingEntries($expense, $request);

        return redirect()->route('finance.expenses')->with('success', 'Expense recorded successfully!');
    }
    
    public function show(Expense $expense)
    {
        $expense->load(['user', 'bankAccount', 'mobileMoneyAccount', 'budget']);
        $entries = AccountingEntry::where('reference_number', $expense->reference_number)->get();
        return view('finance.expenses-show', compact('expense', 'entries'));
    }
    
    public function edit(Expense $expense)
    {
        $bankAccounts = BankAccount::where('is_active', true)->get();
        $mobileMoneyAccounts = MobileMoneyAccount::where('is_active', true)->get();
        $budgets = Budget::all();
        return view('finance.expenses-edit', compact('expense', 'bankAccounts', 'mobileMoneyAccounts', 'budgets'));
    }
    
    public function update(Request $request, Expense $expense)
    {
        $request->validate([
            'date' => 'required|date',
            'category' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'bank_account_id' => 'nullable|exists:bank_accounts,id',
            'mobile_money_account_id' => 'nullable|exists:mobile_money_accounts,id',
            'budget_id' => 'nullable|exists:budgets,id'
        ]);
        
        // Reverse old accounting entries
        $this->reverseAccountingEntries($expense);
        
        // Update expense
        $expense->update([
            'date' => $request->date,
            'category' => $request->category,
            'description' => $request->description,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'bank_account_id' => $request->bank_account_id,
            'mobile_money_account_id' => $request->mobile_money_account_id,
            'budget_id' => $request->budget_id,
        ]);
        
        // Create new entries
        $this->createAccountingEntries($expense, $request);
        
        return redirect()->route('finance.expenses')->with('success', 'Expense updated successfully!');
    }
    
    public function destroy(Expense $expense)
    {
        $this->reverseAccountingEntries($expense);
        $expense->delete();
        return redirect()->route('finance.expenses')->with('success', 'Expense deleted successfully!');
    }
    
    private function createAccountingEntries(Expense $expense, Request $request)
    {
        $expensesAccount = \App\Models\Account::where('name', 'Other Expenses')->first();
        $cashAccount = \App\Models\Account::where('name', 'Cash')->first();
        $bankAccount = \App\Models\Account::where('name', 'Bank Account')->first();
        $mobileMoneyAccount = \App\Models\Account::where('name', 'Mobile Money')->first();

        $journalNumber = 'JE-EXP-' . date('Ymd') . '-' . str_pad(\App\Models\JournalEntry::count() + 1, 4, '0', STR_PAD_LEFT);

        $journalEntry = \App\Models\JournalEntry::create([
            'journal_number' => $journalNumber,
            'entry_date' => now(),
            'description' => 'Expense: ' . $expense->reference_number,
            'reference_type' => Expense::class,
            'reference_id' => $expense->id,
            'is_manual' => false,
        ]);

        // Debit Expense
        AccountingEntry::create([
            'journal_entry_id' => $journalEntry->id,
            'reference_number' => $expense->reference_number,
            'reference_type' => Expense::class,
            'account' => 'Expenses',
            'account_id' => $expensesAccount?->id,
            'type' => 'debit',
            'amount' => $expense->amount,
            'description' => $request->category
        ]);

        // Credit the payment source
        if ($request->payment_method === 'cash') {
            AccountingEntry::create([
                'journal_entry_id' => $journalEntry->id,
                'reference_number' => $expense->reference_number,
                'reference_type' => Expense::class,
                'account' => 'Cash',
                'account_id' => $cashAccount?->id,
                'type' => 'credit',
                'amount' => $expense->amount,
                'description' => 'Payment for expense'
            ]);
        } elseif ($request->bank_account_id) {
            AccountingEntry::create([
                'journal_entry_id' => $journalEntry->id,
                'reference_number' => $expense->reference_number,
                'reference_type' => Expense::class,
                'account' => 'Bank Account',
                'account_id' => $bankAccount?->id,
                'type' => 'credit',
                'amount' => $expense->amount,
                'description' => 'Payment for expense'
            ]);
            
            $bankAccountModel = BankAccount::find($request->bank_account_id);
            $bankAccountModel->update(['balance' => $bankAccountModel->balance - $expense->amount]);
        } elseif ($request->mobile_money_account_id) {
            AccountingEntry::create([
                'journal_entry_id' => $journalEntry->id,
                'reference_number' => $expense->reference_number,
                'reference_type' => Expense::class,
                'account' => 'Mobile Money',
                'account_id' => $mobileMoneyAccount?->id,
                'type' => 'credit',
                'amount' => $expense->amount,
                'description' => 'Payment for expense'
            ]);
            
            $mobileMoneyAccountModel = MobileMoneyAccount::find($request->mobile_money_account_id);
            $mobileMoneyAccountModel->update(['balance' => $mobileMoneyAccountModel->balance - $expense->amount]);
        }
    }
    
    private function reverseAccountingEntries(Expense $expense)
    {
        $oldEntries = AccountingEntry::where('reference_number', $expense->reference_number)->get();

        $journalNumber = 'JE-EXP-REV-' . date('Ymd') . '-' . str_pad(\App\Models\JournalEntry::count() + 1, 4, '0', STR_PAD_LEFT);
        $journalEntry = \App\Models\JournalEntry::create([
            'journal_number' => $journalNumber,
            'entry_date' => now(),
            'description' => 'Reversal of Expense: ' . $expense->reference_number,
            'reference_type' => Expense::class,
            'reference_id' => $expense->id,
            'is_manual' => false,
        ]);
        
        foreach ($oldEntries as $entry) {
            $account = \App\Models\Account::where('name', $entry->account)->first();

            // Reverse the entry
            AccountingEntry::create([
                'journal_entry_id' => $journalEntry->id,
                'reference_number' => $expense->reference_number . '-REV',
                'reference_type' => Expense::class,
                'account' => $entry->account,
                'account_id' => $account?->id,
                'type' => $entry->type === 'debit' ? 'credit' : 'debit',
                'amount' => $entry->amount,
                'description' => 'Reversal of ' . $entry->description
            ]);
            
            // Adjust account balances
            if ($entry->account === 'Bank Account' && $expense->bank_account_id) {
                $bankAccount = BankAccount::find($expense->bank_account_id);
                if ($entry->type === 'credit') {
                    $bankAccount->increment('balance', $expense->amount);
                } else {
                    $bankAccount->decrement('balance', $expense->amount);
                }
            } elseif ($entry->account === 'Mobile Money' && $expense->mobile_money_account_id) {
                $mobileAccount = MobileMoneyAccount::find($expense->mobile_money_account_id);
                if ($entry->type === 'credit') {
                    $mobileAccount->increment('balance', $expense->amount);
                } else {
                    $mobileAccount->decrement('balance', $expense->amount);
                }
            }
        }
        
        $oldEntries->each->delete();
    }
}
