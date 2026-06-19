<?php

namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

class ChartOfAccountsController extends Controller
{
    public function index()
    {
        $accounts = Account::with('parent')->orderBy('account_code')->get();
        return view('finance.chart-of-accounts', compact('accounts'));
    }

    public function create()
    {
        $parentAccounts = Account::whereNull('parent_id')->get();
        return view('finance.chart-of-accounts-create', compact('parentAccounts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'account_code' => 'required|string|max:255|unique:chart_of_accounts,account_code',
            'name' => 'required|string|max:255',
            'type' => 'required|in:Asset,Liability,Equity,Revenue,Expense',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'parent_id' => 'nullable|exists:chart_of_accounts,id'
        ]);

        Account::create($request->all());

        return redirect()->route('finance.chart-of-accounts')->with('success', 'Account created successfully!');
    }

    public function show(Account $account)
    {
        $account->load(['parent', 'children', 'accountingEntries']);
        return view('finance.chart-of-accounts-show', compact('account'));
    }

    public function edit(Account $account)
    {
        $parentAccounts = Account::whereNull('parent_id')->where('id', '!=', $account->id)->get();
        return view('finance.chart-of-accounts-edit', compact('account', 'parentAccounts'));
    }

    public function update(Request $request, Account $account)
    {
        $request->validate([
            'account_code' => 'required|string|max:255|unique:chart_of_accounts,account_code,' . $account->id,
            'name' => 'required|string|max:255',
            'type' => 'required|in:Asset,Liability,Equity,Revenue,Expense',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'parent_id' => 'nullable|exists:chart_of_accounts,id'
        ]);

        $account->update($request->all());

        return redirect()->route('finance.chart-of-accounts')->with('success', 'Account updated successfully!');
    }

    public function destroy(Account $account)
    {
        $account->delete();
        return redirect()->route('finance.chart-of-accounts')->with('success', 'Account deleted successfully!');
    }
}
