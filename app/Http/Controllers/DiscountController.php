<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;

class DiscountController extends Controller {
    public function index() {
        $discounts = Discount::orderBy('created_at', 'desc')->get();
        return view('sales.discounts', compact('discounts'));
    }

    public function create() {
        return view('sales.discounts-create');
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'requires_approval' => 'nullable|boolean'
        ]);

        $discount = Discount::create([
            'name' => $request->name,
            'type' => $request->type,
            'value' => $request->value,
            'min_amount' => $request->min_amount,
            'max_amount' => $request->max_amount,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'requires_approval' => $request->requires_approval ? true : false,
            'is_active' => $request->is_active ? true : false
        ]);

        return redirect()->route('sales.discounts')->with('success', 'Discount created successfully!');
    }

    public function edit(Discount $discount) {
        return view('sales.discounts-edit', compact('discount'));
    }

    public function update(Request $request, Discount $discount) {
        $request->validate([
            'name' => 'required',
            'type' => 'required|in:fixed,percentage',
            'value' => 'required|numeric|min:0',
            'min_amount' => 'nullable|numeric|min:0',
            'max_amount' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'requires_approval' => 'nullable|boolean'
        ]);

        $discount->update([
            'name' => $request->name,
            'type' => $request->type,
            'value' => $request->value,
            'min_amount' => $request->min_amount,
            'max_amount' => $request->max_amount,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'requires_approval' => $request->requires_approval ? true : false,
            'is_active' => $request->is_active ? true : false
        ]);

        return redirect()->route('sales.discounts')->with('success', 'Discount updated successfully!');
    }

    public function toggleActive(Discount $discount) {
        $discount->update(['is_active' => !$discount->is_active]);
        return redirect()->route('sales.discounts')->with('success', 'Discount status updated!');
    }

    public function destroy(Discount $discount) {
        $discount->delete();
        return redirect()->route('sales.discounts')->with('success', 'Discount deleted successfully!');
    }
}
