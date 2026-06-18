<?php

namespace App\Http\Controllers;

use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller {
    public function index()
    {
        $budgets = Budget::orderBy('created_at', 'desc')->get();
        return view('finance.budgets', compact('budgets'));
    }
    
    public function create()
    {
        return view('finance.budgets-create');
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_amount' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:255',
        ]);
        
        Budget::create($validated);
        
        return redirect()->route('finance.budgets')->with('success', 'Budget created successfully');
    }
    
    public function show(Budget $budget)
    {
        $budget->load('expenses');
        return view('finance.budgets-show', compact('budget'));
    }
    
    public function edit(Budget $budget)
    {
        return view('finance.budgets-edit', compact('budget'));
    }
    
    public function update(Request $request, Budget $budget) {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'total_amount' => 'required|numeric|min:0',
            'category' => 'nullable|string|max:255',
        ]);
        
        $budget->update($validated);
        
        return redirect()->route('finance.budgets')->with('success', 'Budget updated successfully');
    }
    
    public function destroy(Budget $budget) {
        $budget->delete();
        return redirect()->route('finance.budgets')->with('success', 'Budget deleted successfully');
    }
}
