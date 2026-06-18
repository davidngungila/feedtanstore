<?php

namespace App\Http\Controllers;

use App\Models\MobileMoneyAccount;
use Illuminate\Http\Request;

class MobileMoneyAccountController extends Controller
{
    public function index()
    {
        $mobileMoneyAccounts = MobileMoneyAccount::all();
        return view('finance.mobile-money', compact('mobileMoneyAccounts'));
    }

    public function create()
    {
        return view('finance.mobile-money-create');
    }
    
    public function show(MobileMoneyAccount $mobileMoneyAccount)
    {
        return view('finance.mobile-money-show', compact('mobileMoneyAccount'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'provider' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'balance' => 'nullable|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        MobileMoneyAccount::create($request->all());

        return redirect()->route('finance.mobile-money')->with('success', 'Mobile Money Account created successfully!');
    }

    public function edit(MobileMoneyAccount $mobileMoneyAccount)
    {
        return view('finance.mobile-money-edit', compact('mobileMoneyAccount'));
    }

    public function update(Request $request, MobileMoneyAccount $mobileMoneyAccount)
    {
        $request->validate([
            'provider' => 'required|string|max:255',
            'phone_number' => 'required|string|max:255',
            'account_name' => 'nullable|string|max:255',
            'balance' => 'nullable|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $mobileMoneyAccount->update($request->all());

        return redirect()->route('finance.mobile-money')->with('success', 'Mobile Money Account updated successfully!');
    }

    public function destroy(MobileMoneyAccount $mobileMoneyAccount)
    {
        $mobileMoneyAccount->delete();
        return redirect()->route('finance.mobile-money')->with('success', 'Mobile Money Account deleted successfully!');
    }
}
