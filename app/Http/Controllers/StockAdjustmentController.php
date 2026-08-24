<?php

namespace App\Http\Controllers;

use App\Models\StockAdjustment;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
            'adjustment_date' => 'required|date',
            'reason' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|distinct|exists:products,id',
            'items.*.type' => 'required|in:addition,subtraction',
            'items.*.quantity_change' => 'required|numeric|min:1',
        ], [
            'items.*.product_id.distinct' => 'Each product can only be selected once.',
            'items.*.product_id.required' => 'Please select a product for every row.',
        ]);

        DB::beginTransaction();
        try {
            $baseReferenceNumber = 'ADJ-' . date('YmdHis');
            $multiple = count($request->items) > 1;
            $created = 0;

            foreach ($request->items as $index => $itemData) {
                $product = Product::lockForUpdate()->findOrFail($itemData['product_id']);

                $quantityBefore = $product->quantity;
                $quantityChange = $itemData['type'] === 'addition'
                    ? $itemData['quantity_change']
                    : -$itemData['quantity_change'];
                $quantityAfter = $quantityBefore + $quantityChange;

                if ($quantityAfter < 0) {
                    DB::rollBack();
                    return back()
                        ->withErrors(['items.' . $index . '.quantity_change' => "Cannot subtract more than current stock for {$product->name} (available: {$quantityBefore})"])
                        ->withInput();
                }

                StockAdjustment::create([
                    'reference_number' => $multiple ? $baseReferenceNumber . '-' . ($created + 1) : $baseReferenceNumber,
                    'product_id' => $product->id,
                    'quantity_before' => $quantityBefore,
                    'quantity_change' => $quantityChange,
                    'quantity_after' => $quantityAfter,
                    'type' => $itemData['type'],
                    'reason' => $request->reason,
                    'adjustment_date' => $request->adjustment_date,
                    'notes' => $request->notes
                ]);

                $product->update(['quantity' => $quantityAfter]);
                $created++;
            }

            DB::commit();

            return redirect()->route('inventory.adjustments')
                ->with('success', "Stock adjustment created successfully for {$created} product(s)");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create stock adjustment: ' . $e->getMessage())->withInput();
        }
    }

    public function show(StockAdjustment $adjustment)
    {
        $adjustment->load('product.category', 'product.brand', 'product.unit');
        return view('inventory.adjustments-show', compact('adjustment'));
    }
}
