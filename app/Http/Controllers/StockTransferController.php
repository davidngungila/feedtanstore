<?php

namespace App\Http\Controllers;

use App\Models\StockTransfer;
use App\Models\Product;
use App\Models\Location;
use Illuminate\Http\Request;

class StockTransferController extends Controller
{
    public function index()
    {
        $transfers = StockTransfer::with(['product', 'fromLocation', 'toLocation'])->get();
        return view('inventory.transfers', compact('transfers'));
    }

    public function create()
    {
        $products = Product::all();
        $locations = Location::all();
        return view('inventory.transfers-create', compact('products', 'locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'from_location_id' => 'required|exists:locations,id|different:to_location_id',
            'to_location_id' => 'required|exists:locations,id|different:from_location_id',
            'quantity' => 'required|numeric|min:1',
            'transfer_date' => 'required|date'
        ]);
        
        $product = Product::find($request->product_id);
        
        if ($product->quantity < $request->quantity) {
            return back()->withErrors(['quantity' => 'Not enough stock in current inventory']);
        }
        
        $transferNumber = 'TRF-' . date('YmdHis');
        
        StockTransfer::create([
            'transfer_number' => $transferNumber,
            'product_id' => $request->product_id,
            'from_location_id' => $request->from_location_id,
            'to_location_id' => $request->to_location_id,
            'quantity' => $request->quantity,
            'transfer_date' => $request->transfer_date,
            'notes' => $request->notes,
            'status' => 'completed'
        ]);
        
        // For now just adjust overall product quantity
        $product->decrement('quantity', $request->quantity);
        
        return redirect()->route('inventory.transfers')->with('success', 'Stock transfer completed successfully');
    }

    public function show($id)
    {
        $transfer = StockTransfer::with(['product', 'fromLocation', 'toLocation'])->findOrFail($id);
        return view('inventory.transfers-show', compact('transfer'));
    }

    public function edit($id)
    {
        $transfer = StockTransfer::findOrFail($id);
        $products = Product::all();
        $locations = Location::all();
        return view('inventory.transfers-edit', compact('transfer', 'products', 'locations'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'from_location_id' => 'required|exists:locations,id|different:to_location_id',
            'to_location_id' => 'required|exists:locations,id|different:from_location_id',
            'quantity' => 'required|numeric|min:1',
            'transfer_date' => 'required|date'
        ]);
        
        $transfer = StockTransfer::findOrFail($id);
        $transfer->update($request->all());
        
        return redirect()->route('inventory.transfers')->with('success', 'Stock transfer updated successfully');
    }

    public function destroy($id)
    {
        $transfer = StockTransfer::findOrFail($id);
        $product = Product::find($transfer->product_id);
        
        // Restore the quantity
        if ($product) {
            $product->increment('quantity', $transfer->quantity);
        }
        
        $transfer->delete();
        
        return redirect()->route('inventory.transfers')->with('success', 'Stock transfer deleted successfully');
    }
}
