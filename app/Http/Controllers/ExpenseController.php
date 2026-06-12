<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\BankAccount;
use App\Models\MobileMoneyAccount;
use App\Models\AccountingEntry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index()
    {
        $expenses = Expense::with(['user', 'bankAccount', 'mobileMoneyAccount'])->latest()->get();
        return view('finance.expenses', compact('expenses'));
    }

    public function create()
    {
        $bankAccounts = BankAccount::where('is_active', true)->get();
        $mobileMoneyAccounts = MobileMoneyAccount::where('is_active', true)->get();
        return view('finance.expenses-create', compact('bankAccounts', 'mobileMoneyAccounts'));
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

        $expense = Expense::create([
            'reference_number' => 'EXP-' . strtoupper(uniqid()),
            'date' => $request->date,
            'category' => $request->category,
            'description' => $request->description,
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'bank_account_id' => $request->bank_account_id,
            'mobile_money_account_id' => $request->mobile_money_account_id,
            'user_id' => Auth::id()
        ]);

        // Double Entry Accounting
        AccountingEntry::create([
            'reference_number' => $expense->reference_number,
            'reference_type' => Expense::class,
            'account' => 'Expenses',
            'type' => 'debit',
            'amount' => $expense->amount,
            'description' => $request->category
        ]);

        if ($request->payment_method === 'cash') {
            AccountingEntry::create([
                'reference_number' => $expense->reference_number,
                'reference_type' => Expense::class,
                'account' => 'Cash',
                'type' => 'credit',
                'amount' => $expense->amount,
                'description' => 'Payment for expense'
            ]);
        } elseif ($request->bank_account_id) {
            AccountingEntry::create([
                'reference_number' => $expense->reference_number,
                'reference_type' => Expense::class,
                'account' => 'Bank Account',
                'type' => 'credit',
                'amount' => $expense->amount,
                'description' => 'Payment for expense'
            ]);
            
            $bankAccount = BankAccount::find($request->bank_account_id);
            $bankAccount->update(['balance' => $bankAccount->balance - $expense->amount]);
        } elseif ($request->mobile_money_account_id) {
            AccountingEntry::create([
                'reference_number' => $expense->reference_number,
                'reference_type' => Expense::class,
                'account' => 'Mobile Money',
                'type' => 'credit',
                'amount' => $expense->amount,
                'description' => 'Payment for expense'
            ]);
            
            $mobileMoneyAccount = MobileMoneyAccount::find($request->mobile_money_account_id);
            $mobileMoneyAccount->update(['balance' => $mobileMoneyAccount->balance - $expense->amount]);
        }

        return redirect()->route('finance.expenses')->with('success', 'Expense recorded successfully!');
    }
}
