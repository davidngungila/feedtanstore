<?php

namespace App\Http\Controllers;

use App\Models\StockCount;
use App\Models\StockCountItem;
use App\Models\Product;
use App\Models\Location;
use Illuminate\Http\Request;

class StockCountController extends Controller
{
    public function index()
    {
        $counts = StockCount::with('location')->get();
        return view('inventory.count', compact('counts'));
    }

    public function create()
    {
        $products = Product::all();
        $locations = Location::all();
        return view('inventory.count-create', compact('products', 'locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'count_date' => 'required|date',
            'products' => 'required|array|min:1',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.quantity_in_system' => 'required|numeric|min:0',
            'products.*.quantity_actual' => 'required|numeric|min:0'
        ]);
        
        $countNumber = 'CNT-' . date('YmdHis');
        
        $stockCount = StockCount::create([
            'count_number' => $countNumber,
            'count_date' => $request->count_date,
            'location_id' => $request->location_id,
            'notes' => $request->notes,
            'status' => 'completed'
        ]);
        
        foreach ($request->products as $productData) {
            $difference = $productData['quantity_actual'] - $productData['quantity_in_system'];
            
            StockCountItem::create([
                'stock_count_id' => $stockCount->id,
                'product_id' => $productData['product_id'],
                'quantity_in_system' => $productData['quantity_in_system'],
                'quantity_actual' => $productData['quantity_actual'],
                'difference' => $difference,
                'notes' => $productData['notes'] ?? null
            ]);
            
            // Update product quantity
            $product = Product::find($productData['product_id']);
            $product->update(['quantity' => $productData['quantity_actual']]);
        }
        
        return redirect()->route('inventory.count')->with('success', 'Stock count completed successfully');
    }
}
