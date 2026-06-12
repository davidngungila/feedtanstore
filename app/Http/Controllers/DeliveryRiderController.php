<?php

namespace App\Http\Controllers;

use App\Models\DeliveryRider;
use Illuminate\Http\Request;

class DeliveryRiderController extends Controller
{
    public function index()
    {
        $riders = DeliveryRider::all();
        return view('online.riders', compact('riders'));
    }

    public function create()
    {
        return view('online.riders-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'vehicle_type' => 'nullable|string|max:255',
            'vehicle_plate' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        DeliveryRider::create($request->all());

        return redirect()->route('online.riders')->with('success', 'Delivery Rider created successfully!');
    }

    public function edit(DeliveryRider $rider)
    {
        return view('online.riders-edit', compact('rider'));
    }

    public function update(Request $request, DeliveryRider $rider)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'vehicle_type' => 'nullable|string|max:255',
            'vehicle_plate' => 'nullable|string|max:255',
            'is_active' => 'boolean'
        ]);

        $rider->update($request->all());

        return redirect()->route('online.riders')->with('success', 'Delivery Rider updated successfully!');
    }

    public function destroy(DeliveryRider $rider)
    {
        $rider->delete();
        return redirect()->route('online.riders')->with('success', 'Delivery Rider deleted successfully!');
    }
}
