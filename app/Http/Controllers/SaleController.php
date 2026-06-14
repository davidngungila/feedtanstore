<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Shift;
use App\Models\AccountingEntry;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SaleController extends Controller {
    public function index() {
        $sales = Sale::with(['customer', 'user', 'items'])->orderBy('created_at', 'desc')->get();
        return view('sales.history', compact('sales'));
    }

    public function create()
    {
        $products = Product::where('is_active', true)->get();
        $productsData = $products->map(function($p) { 
            return [
                'id' => $p->id, 
                'name' => $p->name, 
                'selling_price' => $p->selling_price, 
                'quantity' => $p->quantity, 
                'barcode' => $p->barcode, 
                'sku' => $p->sku
            ]; 
        });
        $customers = Customer::all();
        $currentShift = Shift::where('user_id', Auth::id())->whereNull('closed_at')->first();
        $discounts = Discount::where('is_active', true)
            ->where(function($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->get();
        return view('sales.new', compact('products', 'productsData', 'customers', 'currentShift', 'discounts'));
    }

    public function store(Request $request) {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'discount_id' => 'nullable|exists:discounts,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'type' => 'required|in:cash,credit',
            'paid' => 'required|numeric|min:0',
            'paid_note' => 'nullable|string'
        ]);

        // Check stock availability
        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            if (!$product) {
                return back()->withErrors(['items' => 'Product not found'])->withInput();
            }
            if ($product->quantity < $item['quantity']) {
                return back()->withErrors(['items' => "Insufficient stock for {$product->name}. Available: {$product->quantity}"])->withInput();
            }
        }

        $invoiceNumber = 'INV-' . date('YmdHis');
        $subtotal = 0;
        foreach ($request->items as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }
        $tax = $subtotal * 0; // 0% tax for now
        
        // Calculate discount
        $discount = 0;
        $discountId = null;
        
        // If a discount is selected via request, use it (but verify it's valid)
        // Otherwise, automatically find and apply the best applicable active discount
        $selectedDiscount = null;
        if ($request->discount_id) {
            $selectedDiscount = Discount::find($request->discount_id);
        } else {
            // Find all active discounts that are within date range
            $activeDiscounts = Discount::where('is_active', true)
                ->where(function($q) {
                    $q->whereNull('start_date')->orWhere('start_date', '<=', now());
                })
                ->where(function($q) {
                    $q->whereNull('end_date')->orWhere('end_date', '>=', now());
                })
                ->get();
            
            // Find the discount that gives the maximum discount amount
            $maxDiscount = 0;
            foreach ($activeDiscounts as $d) {
                if ((!$d->min_amount || $subtotal >= $d->min_amount) &&
                    (!$d->max_amount || $subtotal <= $d->max_amount)) {
                    $calcDiscount = $d->type == 'percentage' ? $subtotal * ($d->value / 100) : $d->value;
                    if ($calcDiscount > $maxDiscount) {
                        $maxDiscount = $calcDiscount;
                        $selectedDiscount = $d;
                    }
                }
            }
        }
        
        if ($selectedDiscount) {
            // Check min/max amount
            if ((!$selectedDiscount->min_amount || $subtotal >= $selectedDiscount->min_amount) &&
                (!$selectedDiscount->max_amount || $subtotal <= $selectedDiscount->max_amount)) {
                if ($selectedDiscount->type == 'percentage') {
                    $discount = $subtotal * ($selectedDiscount->value / 100);
                } else {
                    $discount = $selectedDiscount->value;
                }
                $discountId = $selectedDiscount->id;
            }
        }
        
        $total = $subtotal + $tax - $discount;
        $paid = $request->paid;
        $change = max(0, $paid - $total);

        $currentShift = Shift::where('user_id', Auth::id())->whereNull('closed_at')->first();

        // Combine notes
        $notes = $request->notes ?? '';
        if ($request->paid_note) {
            if ($notes) {
                $notes .= "\n";
            }
            $notes .= "Paid Amount Note: {$request->paid_note}";
        }

        $sale = Sale::create([
            'invoice_number' => $invoiceNumber,
            'customer_id' => $request->customer_id,
            'user_id' => Auth::id(),
            'shift_id' => $currentShift->id ?? null,
            'discount_id' => $discountId,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $total,
            'paid' => $paid,
            'change' => $change,
            'payment_method' => $request->payment_method,
            'type' => $request->type,
            'status' => 'completed',
            'notes' => $notes
        ]);

        foreach ($request->items as $itemData) {
            $itemTotal = $itemData['quantity'] * $itemData['unit_price'];
            $sale->items()->create([
                'product_id' => $itemData['product_id'],
                'quantity' => $itemData['quantity'],
                'unit_price' => $itemData['unit_price'],
                'discount' => $itemData['discount'] ?? 0,
                'total' => $itemTotal
            ]);

            $product = Product::find($itemData['product_id']);
            $product->decrement('quantity', $itemData['quantity']);
        }

        if ($currentShift) {
            if ($request->payment_method == 'cash') {
                $currentShift->increment('cash_sales', $total);
            } elseif ($request->payment_method == 'card') {
                $currentShift->increment('card_sales', $total);
            } elseif ($request->payment_method == 'mobile') {
                $currentShift->increment('mobile_sales', $total);
            }
        }

        $this->createAccountingEntries($sale);

        if ($request->type == 'credit' && $request->customer_id) {
            $customer = Customer::find($request->customer_id);
            $customer->increment('balance', $total);
        }

        return redirect()->route('sales.show', $sale)->with('success', 'Sale completed successfully!');
    }

    public function show(Sale $sale) {
        $sale->load(['customer', 'user', 'items.product', 'discountApplied']);
        return view('sales.show', compact('sale'));
    }

    public function destroy(Sale $sale) {
        $sale->update(['status' => 'cancelled']);

        foreach ($sale->items as $item) {
            $product = Product::find($item->product_id);
            $product->increment('quantity', $item->quantity);
        }

        if ($sale->type == 'credit' && $sale->customer_id) {
            $customer = Customer::find($sale->customer_id);
            $customer->decrement('balance', $sale->total);
        }

        $sale->delete();

        return redirect()->route('sales.history')->with('success', 'Sale cancelled successfully!');
    }

    protected function createAccountingEntries(Sale $sale) {
        AccountingEntry::create([
            'reference_number' => $sale->invoice_number,
            'reference_type' => Sale::class,
            'account' => 'Cash',
            'type' => 'debit',
            'amount' => $sale->paid,
            'description' => 'Sale payment received'
        ]);

        AccountingEntry::create([
            'reference_number' => $sale->invoice_number,
            'reference_type' => Sale::class,
            'account' => 'Sales',
            'type' => 'credit',
            'amount' => $sale->total,
            'description' => 'Sale completed'
        ]);

        AccountingEntry::create([
            'reference_number' => $sale->invoice_number,
            'reference_type' => Sale::class,
            'account' => 'Inventory',
            'type' => 'credit',
            'amount' => $sale->subtotal,
            'description' => 'Inventory sold'
        ]);

        AccountingEntry::create([
            'reference_number' => $sale->invoice_number,
            'reference_type' => Sale::class,
            'account' => 'Cost of Goods Sold',
            'type' => 'debit',
            'amount' => $sale->subtotal,
            'description' => 'COGS for sale'
        ]);
    }
}
