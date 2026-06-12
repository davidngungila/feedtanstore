<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::all();
        return view('store.locations', compact('locations'));
    }

    public function create()
    {
        return view('store.locations-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:warehouse,store,other',
            'address' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        Location::create($request->all());

        return redirect()->route('store.locations')->with('success', 'Location created successfully!');
    }

    public function edit(Location $location)
    {
        return view('store.locations-edit', compact('location'));
    }

    public function update(Request $request, Location $location)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|in:warehouse,store,other',
            'address' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $location->update($request->all());

        return redirect()->route('store.locations')->with('success', 'Location updated successfully!');
    }

    public function destroy(Location $location)
    {
        $location->delete();
        return redirect()->route('store.locations')->with('success', 'Location deleted successfully!');
    }
}
