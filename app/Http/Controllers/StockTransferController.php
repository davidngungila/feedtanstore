<?php

namespace App\Http\Controllers;

use App\Models\StockTransfer;
use App\Models\Product;
use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StockTransferController extends Controller
{
    public function index()
    {
        $transfers = StockTransfer::with(['product', 'fromLocation', 'toLocation', 'requester', 'approver'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('inventory.transfers', compact('transfers'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        return view('inventory.transfers-create', compact('products', 'locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'from_location_id' => 'required|exists:locations,id|different:to_location_id',
            'to_location_id' => 'required|exists:locations,id|different:from_location_id',
            'quantity' => 'required|numeric|min:1',
            'notes' => 'nullable|string',
        ]);
        
        $product = Product::find($request->product_id);
        
        if ($product->quantity < $request->quantity) {
            return back()->withErrors(['quantity' => 'Not enough stock in current inventory']);
        }
        
        StockTransfer::create([
            'transfer_number' => StockTransfer::generateTransferNumber(),
            'product_id' => $request->product_id,
            'from_location_id' => $request->from_location_id,
            'to_location_id' => $request->to_location_id,
            'quantity' => $request->quantity,
            'status' => 'pending',
            'notes' => $request->notes,
            'requested_by' => Auth::id(),
        ]);
        
        return redirect()->route('stock-transfers.index')
            ->with('success', 'Stock transfer request submitted successfully');
    }

    public function show(StockTransfer $stockTransfer)
    {
        $stockTransfer->load(['product', 'fromLocation', 'toLocation', 'requester', 'approver']);
        return view('inventory.transfers-show', compact('stockTransfer'));
    }

    public function approve(Request $request, StockTransfer $stockTransfer)
    {
        if ($stockTransfer->status !== 'pending') {
            return back()->with('error', 'This transfer has already been processed');
        }

        $product = Product::find($stockTransfer->product_id);
        
        if ($product->quantity < $stockTransfer->quantity) {
            return back()->with('error', 'Not enough stock available for transfer');
        }

        // Deduct from source location and add to destination
        $product->quantity -= $stockTransfer->quantity;
        $product->save();

        $stockTransfer->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Stock transfer approved and completed');
    }

    public function reject(Request $request, StockTransfer $stockTransfer)
    {
        if ($stockTransfer->status !== 'pending') {
            return back()->with('error', 'This transfer has already been processed');
        }

        $stockTransfer->update([
            'status' => 'rejected',
            'approved_by' => Auth::id(),
            'approved_at' => now(),
        ]);

        return back()->with('success', 'Stock transfer rejected');
    }

    public function complete(StockTransfer $stockTransfer)
    {
        if ($stockTransfer->status !== 'approved') {
            return back()->with('error', 'This transfer must be approved first');
        }

        $stockTransfer->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with('success', 'Stock transfer completed');
    }
}
