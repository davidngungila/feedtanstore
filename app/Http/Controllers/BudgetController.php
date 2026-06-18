<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    public function index()
    {
        $budgets = Budget::with('expenses')->orderBy('created_at', 'desc')->get();
        return view('finance.budgets', compact('budgets'));
    }

    public function create()
    {
        return view('finance.budget-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        Budget::create([
            'name' => $request->name,
            'description' => $request->description,
            'amount' => $request->amount,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('finance.budgets')->with('success', 'Budget created successfully!');
    }

    public function show(Budget $budget)
    {
        $budget->load('expenses');
        return view('finance.budget-show', compact('budget'));
    }

    public function edit(Budget $budget)
    {
        return view('finance.budget-edit', compact('budget'));
    }

    public function update(Request $request, Budget $budget)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        $budget->update($request->all());

        return redirect()->route('finance.budgets')->with('success', 'Budget updated successfully!');
    }

    public function destroy(Budget $budget)
    {
        $budget->delete();
        return redirect()->route('finance.budgets')->with('success', 'Budget deleted successfully!');
    }
}
