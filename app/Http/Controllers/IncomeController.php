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
        // Debit the payment source
        if ($request->payment_method === 'cash') {
            AccountingEntry::create([
                'reference_number' => $income->reference_number,
                'reference_type' => Income::class,
                'account' => 'Cash',
                'type' => 'debit',
                'amount' => $income->amount,
                'description' => 'Income received'
            ]);
        } elseif ($request->bank_account_id) {
            AccountingEntry::create([
                'reference_number' => $income->reference_number,
                'reference_type' => Income::class,
                'account' => 'Bank Account',
                'type' => 'debit',
                'amount' => $income->amount,
                'description' => 'Income received'
            ]);
            
            $bankAccount = BankAccount::find($request->bank_account_id);
            $bankAccount->update(['balance' => $bankAccount->balance + $income->amount]);
        } elseif ($request->mobile_money_account_id) {
            AccountingEntry::create([
                'reference_number' => $income->reference_number,
                'reference_type' => Income::class,
                'account' => 'Mobile Money',
                'type' => 'debit',
                'amount' => $income->amount,
                'description' => 'Income received'
            ]);
            
            $mobileMoneyAccount = MobileMoneyAccount::find($request->mobile_money_account_id);
            $mobileMoneyAccount->update(['balance' => $mobileMoneyAccount->balance + $income->amount]);
        }

        // Credit Income
        AccountingEntry::create([
            'reference_number' => $income->reference_number,
            'reference_type' => Income::class,
            'account' => 'Income',
            'type' => 'credit',
            'amount' => $income->amount,
            'description' => $request->category
        ]);
    }
    
    private function reverseAccountingEntries(Income $income)
    {
        $oldEntries = AccountingEntry::where('reference_number', $income->reference_number)->get();
        
        foreach ($oldEntries as $entry) {
            // Reverse entry
            AccountingEntry::create([
                'reference_number' => $income->reference_number . '-REV',
                'reference_type' => Income::class,
                'account' => $entry->account,
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
