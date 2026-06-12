<?php

namespace App\Http\Controllers;

use App\Models\CustomerGroup;
use Illuminate\Http\Request;

class CustomerGroupController extends Controller
{
    public function index()
    {
        $groups = CustomerGroup::withCount('customers')->get();
        return view('customers.groups', compact('groups'));
    }

    public function create()
    {
        return view('customers.groups-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_percentage' => 'required|numeric|min:0|max:100',
        ]);

        CustomerGroup::create($request->all());

        return redirect()->route('customers.groups')->with('success', 'Customer group created successfully!');
    }

    public function edit(CustomerGroup $group)
    {
        return view('customers.groups-edit', compact('group'));
    }

    public function update(Request $request, CustomerGroup $group)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'discount_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $group->update($request->all());

        return redirect()->route('customers.groups')->with('success', 'Customer group updated successfully!');
    }

    public function destroy(CustomerGroup $group)
    {
        $group->delete();
        return redirect()->route('customers.groups')->with('success', 'Customer group deleted successfully!');
    }
}
