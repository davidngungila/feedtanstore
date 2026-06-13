<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Location;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::with('location')->get();
        return view('store.branches', compact('branches'));
    }

    public function create()
    {
        $locations = Location::where('is_active', true)->get();
        return view('store.branches-create', compact('locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'manager_name' => 'nullable|string|max:255',
            'location_id' => 'nullable|exists:locations,id',
            'is_active' => 'boolean'
        ]);

        Branch::create($request->all());

        return redirect()->route('store.branches')->with('success', 'Branch created successfully!');
    }

    public function edit(Branch $branch)
    {
        $locations = Location::where('is_active', true)->get();
        return view('store.branches-edit', compact('branch', 'locations'));
    }

    public function update(Request $request, Branch $branch)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'manager_name' => 'nullable|string|max:255',
            'location_id' => 'nullable|exists:locations,id',
            'is_active' => 'boolean'
        ]);

        $branch->update($request->all());

        return redirect()->route('store.branches')->with('success', 'Branch updated successfully!');
    }

    public function destroy(Branch $branch)
    {
        $branch->delete();
        return redirect()->route('store.branches')->with('success', 'Branch deleted successfully!');
    }
}
