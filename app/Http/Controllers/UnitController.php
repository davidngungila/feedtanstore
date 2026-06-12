<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use Illuminate\Http\Request;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::all();
        return view('inventory.units', compact('units'));
    }

    public function create()
    {
        return view('inventory.units-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        Unit::create($request->all());

        return redirect()->route('inventory.units')->with('success', 'Unit created successfully!');
    }

    public function show(Unit $unit)
    {
        $unit->load('products');
        return view('inventory.units-show', compact('unit'));
    }

    public function edit(Unit $unit)
    {
        return view('inventory.units-edit', compact('unit'));
    }

    public function update(Request $request, Unit $unit)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'short_name' => 'required|string|max:50',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $unit->update($request->all());

        return redirect()->route('inventory.units')->with('success', 'Unit updated successfully!');
    }

    public function destroy(Unit $unit)
    {
        $unit->delete();
        return redirect()->route('inventory.units')->with('success', 'Unit deleted successfully!');
    }
}
