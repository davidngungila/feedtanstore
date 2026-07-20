<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use App\Models\Product;
use Illuminate\Http\Request;

class StockAdjustmentController extends Controller
{
    public function index()
    {
        $adjustments = StockAdjustment::with('product')->get();
        return view('inventory.adjustments', compact('adjustments'));
    }

    public function create()
    {
        $products = Product::all();
        return view('inventory.adjustments-create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'type' => 'required|in:addition,subtraction',
            'quantity_change' => 'required|numeric|min:1',
            'reason' => 'required',
            'adjustment_date' => 'required|date'
        ]);

        $product = Product::find($request->product_id);
        $quantityBefore = $product->quantity;
        
        $quantityChange = $request->type === 'addition' ? $request->quantity_change : -$request->quantity_change;
        $quantityAfter = $quantityBefore + $quantityChange;
        
        if ($quantityAfter < 0) {
            return back()->withErrors(['quantity_change' => 'Cannot subtract more than current stock']);
        }
        
        $referenceNumber = 'ADJ-' . date('YmdHis');
        
        StockAdjustment::create([
            'reference_number' => $referenceNumber,
            'product_id' => $request->product_id,
            'quantity_before' => $quantityBefore,
            'quantity_change' => $quantityChange,
            'quantity_after' => $quantityAfter,
            'type' => $request->type,
            'reason' => $request->reason,
            'adjustment_date' => $request->adjustment_date,
            'notes' => $request->notes
        ]);
        
        $product->update(['quantity' => $quantityAfter]);
        
        return redirect()->route('inventory.adjustments')->with('success', 'Stock adjustment created successfully');
    }

    public function show(StockAdjustment $adjustment)
    {
        $adjustment->load('product.category', 'product.brand', 'product.unit');
        return view('inventory.adjustments-show', compact('adjustment'));
    }
}
