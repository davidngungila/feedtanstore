<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::all();
        return view('inventory.brands', compact('brands'));
    }

    public function create()
    {
        return view('inventory.brands-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        Brand::create($request->all());

        return redirect()->route('inventory.brands')->with('success', 'Brand created successfully!');
    }

    public function show(Brand $brand)
    {
        $brand->load('products');
        return view('inventory.brands-show', compact('brand'));
    }

    public function edit(Brand $brand)
    {
        return view('inventory.brands-edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $brand->update($request->all());

        return redirect()->route('inventory.brands')->with('success', 'Brand updated successfully!');
    }

    public function destroy(Brand $brand)
    {
        $brand->delete();
        return redirect()->route('inventory.brands')->with('success', 'Brand deleted successfully!');
    }
}
