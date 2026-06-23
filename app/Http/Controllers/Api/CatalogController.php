<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CarouselSlide;
use App\Models\Product;
use App\Models\ProductImage;

class CatalogController extends Controller
{
    public function products()
    {
        $products = Product::where('is_active', true)
            ->where('is_available_online', true)
            ->with(['images'])
            ->get();
        return response()->json($products);
    }

    public function product($id)
    {
        $product = Product::with(['images'])->findOrFail($id);
        return response()->json($product);
    }

    public function carousel()
    {
        $slides = CarouselSlide::orderBy('order')->get();
        return response()->json($slides);
    }
}