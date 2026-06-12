<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        return view('inventory.categories', compact('categories'));
    }

    public function create()
    {
        return view('inventory.categories-create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        Category::create($request->all());

        return redirect()->route('inventory.categories')->with('success', 'Category created successfully!');
    }

    public function show(Category $category)
    {
        $category->load('products');
        return view('inventory.categories-show', compact('category'));
    }

    public function edit(Category $category)
    {
        return view('inventory.categories-edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean'
        ]);

        $category->update($request->all());

        return redirect()->route('inventory.categories')->with('success', 'Category updated successfully!');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('inventory.categories')->with('success', 'Category deleted successfully!');
    }
}
