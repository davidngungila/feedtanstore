<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AssetController extends Controller
{
    public function index()
    {
        $assets = Asset::with('user')->latest()->get();
        return view('finance.assets', compact('assets'));
    }

    public function create()
    {
        return view('finance.asset-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'cost' => 'required|numeric|min:0',
            'current_value' => 'nullable|numeric|min:0',
            'status' => 'required|string|max:50',
            'description' => 'nullable|string'
        ]);

        Asset::create([
            'name' => $request->name,
            'type' => $request->type,
            'purchase_date' => $request->purchase_date,
            'cost' => $request->cost,
            'current_value' => $request->current_value ?? $request->cost,
            'status' => $request->status,
            'description' => $request->description,
            'user_id' => Auth::id()
        ]);

        return redirect()->route('finance.assets')->with('success', 'Asset added successfully!');
    }

    public function show(Asset $asset)
    {
        $asset->load('user');
        return view('finance.asset-show', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        return view('finance.asset-edit', compact('asset'));
    }

    public function update(Request $request, Asset $asset)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'purchase_date' => 'required|date',
            'cost' => 'required|numeric|min:0',
            'current_value' => 'nullable|numeric|min:0',
            'status' => 'required|string|max:50',
            'description' => 'nullable|string'
        ]);

        $asset->update($request->all());

        return redirect()->route('finance.assets')->with('success', 'Asset updated successfully!');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        return redirect()->route('finance.assets')->with('success', 'Asset deleted successfully!');
    }
}
