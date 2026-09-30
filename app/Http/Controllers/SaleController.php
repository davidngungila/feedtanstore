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
    public function index(Request $request) {
        $search = trim((string) $request->input('search'));

        $sales = Sale::with(['customer', 'user', 'items'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', '%' . $search . '%')
                        ->orWhere('type', 'like', '%' . $search . '%')
                        ->orWhere('status', 'like', '%' . $search . '%')
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('name', 'like', '%' . $search . '%')
                                ->orWhere('phone', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20)
            ->appends($request->only('search'));

        return view('sales.history', compact('sales', 'search'));
    }

    /**
     * Export all sales of a full day as CSV (Excel-compatible).
     * Respects the optional search filter from the history page.
     */
    public function export(Request $request) {
        $request->validate([
            'date' => 'nullable|date',
            'search' => 'nullable|string|max:100',
        ]);

        $date = $request->input('date', now()->toDateString());
        $search = trim((string) $request->input('search'));

        $sales = Sale::with(['customer', 'user', 'items.product'])
            ->whereDate('created_at', $date)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('invoice_number', 'like', '%' . $search . '%')
                        ->orWhere('type', 'like', '%' . $search . '%')
                        ->orWhere('status', 'like', '%' . $search . '%')
                        ->orWhereHas('customer', function ($customerQuery) use ($search) {
                            $customerQuery->where('name', 'like', '%' . $search . '%')
                                ->orWhere('phone', 'like', '%' . $search . '%')
                                ->orWhere('email', 'like', '%' . $search . '%');
                        });
                });
            })
            ->orderBy('created_at', 'asc')
            ->get();

        $filename = 'day-sales-' . $date . '.csv';

        return response()->streamDownload(function () use ($sales) {
            $out = fopen('php://output', 'w');
            fputcsv($out, ['Invoice #', 'Date/Time', 'Customer', 'Cashier', 'Items', 'Payment Method', 'Type', 'Status', 'Subtotal', 'Discount', 'Total', 'Paid', 'Change']);
            foreach ($sales as $sale) {
                $items = $sale->items->map(function ($i) {
                    return $i->quantity . 'x ' . ($i->product->name ?? ('Product #' . $i->product_id));
                })->implode('; ');
                fputcsv($out, [
                    $sale->invoice_number,
                    optional($sale->created_at)->format('Y-m-d H:i'),
                    $sale->customer->name ?? 'Walk-in',
                    $sale->user->name ?? '-',
                    $items,
                    ucwords(str_replace('_', ' ', $sale->payment_method ?? '')),
                    ucfirst($sale->type ?? ''),
                    ucfirst($sale->status ?? ''),
                    number_format((float) $sale->subtotal, 2, '.', ''),
                    number_format((float) $sale->discount, 2, '.', ''),
                    number_format((float) $sale->total, 2, '.', ''),
                    number_format((float) $sale->paid, 2, '.', ''),
                    number_format((float) $sale->change, 2, '.', ''),
                ]);
            }
            // Summary row
            fputcsv($out, []);
            fputcsv($out, ['TOTAL SALES', $sales->count(), '', '', '', '', '', '', '', '', number_format((float) $sales->sum('total'), 2, '.', ''), number_format((float) $sales->sum('paid'), 2, '.', ''), '']);
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv']);
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
            'type' => 'nullable|in:cash,credit',
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
        $change = $paid - $total;

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
            'type' => $request->type ?? 'cash',
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

        // Open cash drawer if payment method is cash and setting is enabled
        if ($request->payment_method == 'cash') {
            $settings = \App\Models\StoreSetting::first();
            if ($settings && $settings->cash_drawer_auto_open_after_cash_sale) {
                $this->openCashDrawer();
            }
        }

        if ($request->type == 'credit' && $request->customer_id) {
            $customer = Customer::find($request->customer_id);
            $customer->increment('balance', $total);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'sale' => $sale,
                'sale_key' => $sale->encrypted_key,
                'total' => $total,
                'paid' => $request->paid,
                'change' => $change
            ]);
        }

        return redirect()->route('sales.show', $sale)->with('success', 'Sale completed successfully!');
    }

    public function show($key) {
        $sale = Sale::findByAnyKeyOrFail($key, true);
        $sale->load(['customer', 'user', 'items.product', 'discountApplied']);
        return view('sales.show', compact('sale'));
    }

    public function edit(Sale $sale) {
        // Soft-deleted (cancelled) sales cannot be edited — restore first
        if ($sale->trashed() || $sale->status === 'cancelled') {
            return redirect()->route('sales.history')->with('error', 'Cancelled sales cannot be edited. Restore it first.');
        }

        // Block editing once posted to TRA (fiscal receipt already issued)
        if ($sale->tra_status === 'posted') {
            return redirect()->route('sales.show', $sale)->with('error', 'This sale was already posted to TRA and cannot be edited.');
        }

        $sale->load(['items.product', 'customer']);
        // Pre-build plain arrays in PHP: Blade's @json cannot parse inline closures
        $cartItems = $sale->items->map(function($i) {
            return [
                'product_id' => $i->product_id,
                'name' => $i->product->name ?? ('Product #' . $i->product_id),
                'quantity' => (int) $i->quantity,
                'unit_price' => (float) $i->unit_price,
            ];
        })->values()->all();
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
        $discounts = Discount::where('is_active', true)
            ->where(function($q) {
                $q->whereNull('start_date')->orWhere('start_date', '<=', now());
            })
            ->where(function($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', now());
            })
            ->get();
        return view('sales.edit', compact('sale', 'products', 'productsData', 'customers', 'discounts', 'cartItems'));
    }

    public function update(Request $request, Sale $sale) {
        if ($sale->trashed() || $sale->status === 'cancelled') {
            return back()->with('error', 'Cancelled sales cannot be edited. Restore it first.');
        }

        if ($sale->tra_status === 'posted') {
            return back()->with('error', 'This sale was already posted to TRA and cannot be edited.');
        }

        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'discount_id' => 'nullable|exists:discounts,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:cash,card,mobile,clickpesa,lipa_namba',
            'type' => 'nullable|in:cash,credit',
            'paid' => 'required|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        // Old quantities per product (to allow "returning" stock before checking)
        $sale->load('items');
        $oldQtyByProduct = [];
        foreach ($sale->items as $oldItem) {
            $oldQtyByProduct[$oldItem->product_id] = ($oldQtyByProduct[$oldItem->product_id] ?? 0) + $oldItem->quantity;
        }

        // Check stock availability accounting for stock that will be returned
        $newQtyByProduct = [];
        foreach ($request->items as $item) {
            $newQtyByProduct[$item['product_id']] = ($newQtyByProduct[$item['product_id']] ?? 0) + $item['quantity'];
        }
        foreach ($newQtyByProduct as $productId => $newQty) {
            $product = Product::find($productId);
            if (!$product) {
                return back()->withErrors(['items' => 'Product not found'])->withInput();
            }
            $available = $product->quantity + ($oldQtyByProduct[$productId] ?? 0);
            if ($newQty > $available) {
                return back()->withErrors(['items' => "Insufficient stock for {$product->name}. Available (incl. current sale): {$available}"])->withInput();
            }
        }

        // Recalculate totals
        $subtotal = 0;
        foreach ($request->items as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }
        $tax = 0;

        $discount = 0;
        $discountId = $request->discount_id;
        if ($discountId) {
            $selectedDiscount = Discount::find($discountId);
            if ($selectedDiscount && $selectedDiscount->is_active) {
                if ((!$selectedDiscount->min_amount || $subtotal >= $selectedDiscount->min_amount) &&
                    (!$selectedDiscount->max_amount || $subtotal <= $selectedDiscount->max_amount)) {
                    $discount = $selectedDiscount->type == 'percentage'
                        ? $subtotal * ($selectedDiscount->value / 100)
                        : $selectedDiscount->value;
                } else {
                    $discountId = null;
                }
            } else {
                $discountId = null;
            }
        } else {
            // Keep existing auto-discount behaviour: retain current discount if still valid,
            // otherwise try best applicable active discount
            $activeDiscounts = Discount::where('is_active', true)
                ->where(function($q) {
                    $q->whereNull('start_date')->orWhere('start_date', '<=', now());
                })
                ->where(function($q) {
                    $q->whereNull('end_date')->orWhere('end_date', '>=', now());
                })
                ->get();
            $maxDiscount = 0;
            $best = null;
            foreach ($activeDiscounts as $d) {
                if ((!$d->min_amount || $subtotal >= $d->min_amount) &&
                    (!$d->max_amount || $subtotal <= $d->max_amount)) {
                    $calc = $d->type == 'percentage' ? $subtotal * ($d->value / 100) : $d->value;
                    if ($calc > $maxDiscount) {
                        $maxDiscount = $calc;
                        $best = $d;
                    }
                }
            }
            if ($best) {
                $discount = $maxDiscount;
                $discountId = $best->id;
            }
        }

        $total = $subtotal + $tax - $discount;
        $paid = $request->paid;
        $change = $paid - $total;
        $newType = $request->type ?? $sale->type ?? 'cash';

        $oldTotal = (float) $sale->total;
        $oldPaymentMethod = $sale->payment_method;
        $oldType = $sale->type;
        $oldCustomerId = $sale->customer_id;
        $oldShiftId = $sale->shift_id;

        \DB::transaction(function () use ($request, $sale, $oldQtyByProduct, $newQtyByProduct, $subtotal, $tax, $discount, $discountId, $total, $paid, $change, $newType, $oldTotal, $oldPaymentMethod, $oldType, $oldCustomerId, $oldShiftId) {
            // 1. Adjust inventory (diff per product)
            $allProductIds = array_unique(array_merge(array_keys($oldQtyByProduct), array_keys($newQtyByProduct)));
            foreach ($allProductIds as $productId) {
                $diff = ($newQtyByProduct[$productId] ?? 0) - ($oldQtyByProduct[$productId] ?? 0);
                if ($diff === 0) continue;
                $product = Product::find($productId);
                if (!$product) continue;
                if ($diff > 0) {
                    $product->decrement('quantity', $diff);
                } else {
                    $product->increment('quantity', abs($diff));
                }
            }

            // 2. Reverse old customer balance (credit sales increase balance owed)
            if ($oldType == 'credit' && $oldCustomerId) {
                $oldCustomer = Customer::find($oldCustomerId);
                if ($oldCustomer) {
                    $oldCustomer->decrement('balance', $oldTotal);
                }
            }

            // 3. Reverse old shift totals
            if ($oldShiftId) {
                $shift = Shift::find($oldShiftId);
                if ($shift) {
                    $column = $oldPaymentMethod == 'card' ? 'card_sales' : ($oldPaymentMethod == 'mobile' ? 'mobile_sales' : 'cash_sales');
                    $shift->decrement($column, $oldTotal);
                }
            }

            // 4. Replace items
            $sale->items()->delete();
            foreach ($request->items as $itemData) {
                $itemTotal = $itemData['quantity'] * $itemData['unit_price'];
                $sale->items()->create([
                    'product_id' => $itemData['product_id'],
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemData['unit_price'],
                    'discount' => $itemData['discount'] ?? 0,
                    'total' => $itemTotal
                ]);
            }

            // 5. Update sale header
            $sale->update([
                'customer_id' => $request->customer_id,
                'discount_id' => $discountId,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'discount' => $discount,
                'total' => $total,
                'paid' => $paid,
                'change' => $change,
                'payment_method' => $request->payment_method,
                'type' => $newType,
                'notes' => $request->notes,
            ]);

            // 6. Apply new customer balance
            if ($newType == 'credit' && $request->customer_id) {
                $newCustomer = Customer::find($request->customer_id);
                if ($newCustomer) {
                    $newCustomer->increment('balance', $total);
                }
            }

            // 7. Apply new shift totals (to original shift to keep cash-up consistent)
            $targetShiftId = $oldShiftId ?? Shift::where('user_id', \Auth::id())->whereNull('closed_at')->value('id');
            if ($targetShiftId) {
                $shift = Shift::find($targetShiftId);
                if ($shift) {
                    $column = $request->payment_method == 'card' ? 'card_sales' : ($request->payment_method == 'mobile' ? 'mobile_sales' : 'cash_sales');
                    $shift->increment($column, $total);
                    // Keep sale linked to a shift
                    if (!$sale->shift_id) {
                        $sale->update(['shift_id' => $targetShiftId]);
                    }
                }
            }
        });

        return redirect()->route('sales.show', $sale)->with('success', 'Sale updated successfully!');
    }

    public function destroy(Request $request, Sale $sale) {
        $request->validate([
            'cancellation_reason' => 'required|string'
        ]);
        
        $sale->update([
            'status' => 'cancelled', 
            'cancellation_reason' => $request->cancellation_reason
        ]);

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

    public function restore($key) {
        $sale = Sale::findByAnyKeyOrFail($key, true);

        foreach ($sale->items as $item) {
            $product = Product::find($item->product_id);
            if ($product->quantity >= $item->quantity) {
                $product->decrement('quantity', $item->quantity);
            } else {
                return back()->with('error', 'Not enough stock to restore this sale!');
            }
        }

        if ($sale->type == 'credit' && $sale->customer_id) {
            $customer = Customer::find($sale->customer_id);
            $customer->increment('balance', $sale->total);
        }

        $sale->update(['status' => 'completed']);
        $sale->restore();

        return redirect()->route('sales.cancelled')->with('success', 'Sale restored successfully!');
    }

    protected function createAccountingEntries(Sale $sale) {
        // Skip if we don't have accounting models yet
        try {
            $cashAccount = \App\Models\Account::where('name', 'Cash')->first();
            $salesAccount = \App\Models\Account::where('name', 'Sales')->first();
            $inventoryAccount = \App\Models\Account::where('name', 'Inventory')->first();
            $cogsAccount = \App\Models\Account::where('name', 'Cost of Goods Sold')->first();

            $journalNumber = 'JE-SALE-' . date('Ymd') . '-' . str_pad(\App\Models\JournalEntry::count() + 1, 4, '0', STR_PAD_LEFT);

            $journalEntry = \App\Models\JournalEntry::create([
                'journal_number' => $journalNumber,
                'entry_number' => $journalNumber,
                'entry_date' => now(),
                'description' => 'Sale: ' . $sale->invoice_number,
                'reference_type' => Sale::class,
                'reference_id' => $sale->id,
                'is_manual' => false,
            ]);

            AccountingEntry::create([
                'journal_entry_id' => $journalEntry->id,
                'reference_number' => $sale->invoice_number,
                'reference_type' => Sale::class,
                'account' => 'Cash',
                'account_id' => $cashAccount?->id,
                'type' => 'debit',
                'amount' => $sale->paid,
                'description' => 'Sale payment received'
            ]);

            AccountingEntry::create([
                'journal_entry_id' => $journalEntry->id,
                'reference_number' => $sale->invoice_number,
                'reference_type' => Sale::class,
                'account' => 'Sales',
                'account_id' => $salesAccount?->id,
                'type' => 'credit',
                'amount' => $sale->total,
                'description' => 'Sale completed'
            ]);

            AccountingEntry::create([
                'journal_entry_id' => $journalEntry->id,
                'reference_number' => $sale->invoice_number,
                'reference_type' => Sale::class,
                'account' => 'Inventory',
                'account_id' => $inventoryAccount?->id,
                'type' => 'credit',
                'amount' => $sale->subtotal,
                'description' => 'Inventory sold'
            ]);

            AccountingEntry::create([
                'journal_entry_id' => $journalEntry->id,
                'reference_number' => $sale->invoice_number,
                'reference_type' => Sale::class,
                'account' => 'Cost of Goods Sold',
                'account_id' => $cogsAccount?->id,
                'type' => 'debit',
                'amount' => $sale->subtotal,
                'description' => 'COGS for sale'
            ]);
        } catch (\Exception $e) {
            // Ignore accounting entry creation fails, don't break the sale
            // Just log it and continue
            \Log::error('Failed to create accounting entries: ' . $e->getMessage());
        }
    }

    protected function openCashDrawer()
    {
        try {
            $settings = \App\Models\StoreSetting::first();
            if (!$settings || !$settings->vfd_enabled) {
                return;
            }

            // Send cash drawer open command to VFD
            \App\Services\VFDService::openCashDrawer();
            
            \Log::info('Cash drawer opened successfully');
        } catch (\Exception $e) {
            \Log::error('Failed to open cash drawer: ' . $e->getMessage());
            // Don't fail the sale if cash drawer doesn't open
        }
    }
}
