<?php

namespace App\Http\Controllers;

use App\Models\BankAccount;
use Illuminate\Http\Request;

class BankAccountController extends Controller
{
    public function index()
    {
        $bankAccounts = BankAccount::all();
        return view('finance.bank', compact('bankAccounts'));
    }

    public function create()
    {
        return view('finance.bank-create');
    }
    
    public function show(BankAccount $bankAccount)
    {
        return view('finance.bank-show', compact('bankAccount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'balance' => 'nullable|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        BankAccount::create($request->all());

        return redirect()->route('finance.bank')->with('success', 'Bank Account created successfully!');
    }

    public function edit(BankAccount $bankAccount)
    {
        return view('finance.bank-edit', compact('bankAccount'));
    }

    public function update(Request $request, BankAccount $bankAccount)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'account_number' => 'required|string|max:255',
            'bank_name' => 'required|string|max:255',
            'branch' => 'nullable|string|max:255',
            'balance' => 'nullable|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $bankAccount->update($request->all());

        return redirect()->route('finance.bank')->with('success', 'Bank Account updated successfully!');
    }

    public function destroy(BankAccount $bankAccount)
    {
        $bankAccount->delete();
        return redirect()->route('finance.bank')->with('success', 'Bank Account deleted successfully!');
    }
}
