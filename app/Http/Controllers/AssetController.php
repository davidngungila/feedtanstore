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
        return view('finance.assets-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'purchase_date' => 'required|date',
            'depreciation_start_date' => 'nullable|date',
            'purchase_cost' => 'required|numeric|min:0',
            'salvage_value' => 'nullable|numeric|min:0',
            'useful_life_years' => 'required|integer|min:1',
            'depreciation_method' => 'required|string|in:straight_line,declining_balance,double_declining_balance',
            'location' => 'nullable|string|max:255',
            'status' => 'required|string',
            'serial_number' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'warranty_expiry' => 'nullable|date',
            'assigned_to' => 'nullable|string|max:255',
            'maintenance_notes' => 'nullable|string',
        ]);

        Asset::create([
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'purchase_date' => $request->purchase_date,
            'depreciation_start_date' => $request->depreciation_start_date,
            'purchase_cost' => $request->purchase_cost,
            'salvage_value' => $request->salvage_value ?? 0,
            'accumulated_depreciation' => 0,
            'useful_life_years' => $request->useful_life_years,
            'depreciation_method' => $request->depreciation_method,
            'location' => $request->location,
            'status' => $request->status,
            'serial_number' => $request->serial_number,
            'manufacturer' => $request->manufacturer,
            'model' => $request->model,
            'warranty_expiry' => $request->warranty_expiry,
            'assigned_to' => $request->assigned_to,
            'maintenance_notes' => $request->maintenance_notes,
            'user_id' => Auth::id(),
        ]);

        return redirect()->route('finance.assets')->with('success', 'Asset created successfully!');
    }

    public function show(Asset $asset)
    {
        $asset->load('user');
        return view('finance.assets-show', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        return view('finance.assets-edit', compact('asset'));
    }

    public function update(Request $request, Asset $asset)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'description' => 'nullable|string',
            'purchase_date' => 'required|date',
            'depreciation_start_date' => 'nullable|date',
            'purchase_cost' => 'required|numeric|min:0',
            'salvage_value' => 'nullable|numeric|min:0',
            'useful_life_years' => 'required|integer|min:1',
            'depreciation_method' => 'required|string|in:straight_line,declining_balance,double_declining_balance',
            'location' => 'nullable|string|max:255',
            'status' => 'required|string',
            'serial_number' => 'nullable|string|max:255',
            'manufacturer' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'warranty_expiry' => 'nullable|date',
            'assigned_to' => 'nullable|string|max:255',
            'maintenance_notes' => 'nullable|string',
        ]);

        $asset->update([
            'name' => $request->name,
            'type' => $request->type,
            'description' => $request->description,
            'purchase_date' => $request->purchase_date,
            'depreciation_start_date' => $request->depreciation_start_date,
            'purchase_cost' => $request->purchase_cost,
            'salvage_value' => $request->salvage_value ?? 0,
            'useful_life_years' => $request->useful_life_years,
            'depreciation_method' => $request->depreciation_method,
            'location' => $request->location,
            'status' => $request->status,
            'serial_number' => $request->serial_number,
            'manufacturer' => $request->manufacturer,
            'model' => $request->model,
            'warranty_expiry' => $request->warranty_expiry,
            'assigned_to' => $request->assigned_to,
            'maintenance_notes' => $request->maintenance_notes,
        ]);

        return redirect()->route('finance.assets')->with('success', 'Asset updated successfully!');
    }

    public function destroy(Asset $asset)
    {
        $asset->delete();
        return redirect()->route('finance.assets')->with('success', 'Asset deleted successfully!');
    }

    public function runDepreciation()
    {
        $assets = Asset::where('status', 'active')->get();
        $updatedCount = 0;

        foreach ($assets as $asset) {
            $startDate = $asset->depreciation_start_date ?? $asset->purchase_date;
            $yearsUsed = now()->diffInYears($startDate);

            if ($yearsUsed > 0 && !$asset->is_fully_depreciated) {
                // Calculate the current accumulated depreciation based on the model
                $currentAccumulatedDepreciation = $asset->purchase_cost - $asset->current_value;
                
                $asset->update([
                    'accumulated_depreciation' => $currentAccumulatedDepreciation,
                    'last_depreciation_date' => now(),
                ]);
                $updatedCount++;
            }
        }

        return redirect()->route('finance.assets')->with('success', "Depreciation calculated and updated for {$updatedCount} active assets.");
    }
}
