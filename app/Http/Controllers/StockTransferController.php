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
        $products = Product::where('is_active', true)->where('quantity', '>', 0)->orderBy('name')->get();
        $locations = Location::orderBy('name')->get();
        return view('storekeeper.stock-transfers-create', compact('products', 'locations'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_location_id' => 'required|exists:locations,id|different:to_location_id',
            'to_location_id' => 'required|exists:locations,id|different:from_location_id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'notes' => 'nullable|string',
        ]);
        
        // Validate stock availability for all items
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            if ($product->quantity < $item['quantity']) {
                return back()->withErrors(['items' => "Not enough stock for {$product->name}"]);
            }
        }
        
        $stockTransfer = StockTransfer::create([
            'transfer_number' => StockTransfer::generateTransferNumber(),
            'from_location_id' => $request->from_location_id,
            'to_location_id' => $request->to_location_id,
            'status' => 'pending',
            'notes' => $request->notes,
            'requested_by' => Auth::id(),
        ]);
        
        // Add transfer items
        foreach ($request->items as $item) {
            $stockTransfer->items()->create([
                'product_id' => $item['product_id'],
                'quantity' => $item['quantity'],
            ]);
        }
        
        return redirect()->route('storekeeper.stock-transfers')
            ->with('success', 'Stock transfer request submitted successfully');
    }

    public function show(StockTransfer $stockTransfer)
    {
        $stockTransfer->load(['items.product', 'product', 'fromLocation', 'toLocation', 'requester', 'approver']);
        return view('inventory.transfers-show', compact('stockTransfer'));
    }

    public function approve(Request $request, StockTransfer $stockTransfer)
    {
        if ($stockTransfer->status !== 'pending') {
            return back()->with('error', 'This transfer has already been processed');
        }

        // Validate stock availability for all items
        foreach ($stockTransfer->items as $item) {
            $product = Product::find($item->product_id);
            if ($product->quantity < $item->quantity) {
                return back()->with('error', "Not enough stock for {$product->name}");
            }
        }

        // Deduct stock for all items
        foreach ($stockTransfer->items as $item) {
            $product = Product::find($item->product_id);
            $product->decrement('quantity', $item->quantity);
        }

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
