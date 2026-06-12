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

        // Double Entry Accounting
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

        AccountingEntry::create([
            'reference_number' => $income->reference_number,
            'reference_type' => Income::class,
            'account' => 'Income',
            'type' => 'credit',
            'amount' => $income->amount,
            'description' => $request->category
        ]);

        return redirect()->route('finance.income')->with('success', 'Income recorded successfully!');
    }
}
