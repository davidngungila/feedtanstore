<?php

namespace App\Http\Controllers;

use App\Models\CashRegister;
use App\Models\Shift;
use Illuminate\Http\Request;

class CashManagementController extends Controller
{
    public function index()
    {
        $cashRegisters = CashRegister::all();
        $activeShifts = Shift::whereNull('closed_at')->with('user')->get();
        return view('finance.cash', compact('cashRegisters', 'activeShifts'));
    }

    public function create()
    {
        return view('finance.cash-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'opening_balance' => 'nullable|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        CashRegister::create([
            'name' => $request->name,
            'opening_balance' => $request->opening_balance ?? 0,
            'current_balance' => $request->opening_balance ?? 0,
            'is_active' => $request->is_active ?? true
        ]);

        return redirect()->route('finance.cash')->with('success', 'Cash Register created successfully!');
    }
}
