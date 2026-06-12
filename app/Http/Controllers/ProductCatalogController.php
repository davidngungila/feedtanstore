<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductCatalogController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'brand', 'unit'])->where('is_available_online', true)->get();
        $categories = Category::all();
        return view('online.catalog', compact('products', 'categories'));
    }

    public function toggleOnlineStatus(Request $request, Product $product)
    {
        $product->update(['is_available_online' => !$product->is_available_online]);
        return back()->with('success', 'Product online status updated!');
    }
}
