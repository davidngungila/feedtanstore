<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductCatalogController extends Controller
{
    public function index()
    {
        $products = Product::with(['category', 'brand', 'unit', 'images'])->get();
        $categories = Category::all();
        return view('online.catalog', compact('products', 'categories'));
    }

    public function show(Product $product)
    {
        $product->load(['category', 'brand', 'unit', 'images', 'onlineOrderItems.order']);
        $settings = \App\Models\StoreSetting::firstOrCreate();
        return view('online.catalog-show', compact('product', 'settings'));
    }

    public function toggleOnlineStatus(Request $request, Product $product)
    {
        $product->update(['is_available_online' => !$product->is_available_online]);
        return back()->with('success', 'Product online status updated!');
    }

    public function uploadImage(Request $request, Product $product)
    {
        $request->validate([
            'image' => 'required|image|max:2048'
        ]);

        $path = $request->file('image')->store('products', 'public');

        $isPrimary = $product->images()->count() === 0;

        $product->images()->create([
            'image_path' => $path,
            'is_primary' => $isPrimary,
            'order' => $product->images()->count()
        ]);

        return back()->with('success', 'Image uploaded successfully!');
    }

    public function deleteImage(Request $request, Product $product, ProductImage $image)
    {
        $image->delete();
        return back()->with('success', 'Image deleted successfully!');
    }

    public function setPrimaryImage(Request $request, Product $product, ProductImage $image)
    {
        $product->images()->update(['is_primary' => false]);
        $image->update(['is_primary' => true]);
        return back()->with('success', 'Primary image set successfully!');
    }
}
