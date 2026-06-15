<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Customer;
use App\Models\CustomerPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CreditSaleController extends Controller {
    public function index() {
        $sales = Sale::where('type', 'credit')->with(['customer', 'user'])->orderBy('created_at', 'desc')->get();
        return view('sales.credit', compact('sales'));
    }

    public function addPayment($id) {
        $sale = Sale::with(['customer'])->findOrFail($id);
        return view('sales.credit-payment', compact('sale'));
    }

    public function storePayment(Request $request, $id) {
        $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string|in:cash,card,mobile',
            'transaction_id' => 'nullable|string',
            'notes' => 'nullable|string'
        ]);

        $sale = Sale::findOrFail($id);
        $due = $sale->total - $sale->paid;

        if ($request->amount > $due) {
            return back()->withErrors(['amount' => 'Payment amount cannot exceed due amount: TZS ' . number_format($due, 2)]);
        }

        // Create customer payment record
        $paymentNumber = 'CP-' . date('YmdHis');
        CustomerPayment::create([
            'payment_number' => $paymentNumber,
            'customer_id' => $sale->customer_id,
            'user_id' => Auth::id(),
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id,
            'notes' => $request->notes
        ]);

        // Update sale paid amount
        $sale->increment('paid', $request->amount);

        // Update customer balance
        $customer = Customer::findOrFail($sale->customer_id);
        $customer->decrement('balance', $request->amount);

        return redirect()->route('sales.credit')->with('success', 'Payment recorded successfully!');
    }

    public function show($id) {
        $sale = Sale::with(['customer', 'user', 'items.product'])->findOrFail($id);
        $payments = CustomerPayment::where('customer_id', $sale->customer_id)->orderBy('created_at', 'desc')->get();
        return view('sales.credit-show', compact('sale', 'payments'));
    }

    public function edit($id) {
        $sale = Sale::with(['items'])->findOrFail($id);
        $products = \App\Models\Product::where('is_active', true)->get();
        $customers = Customer::all();
        $discounts = \App\Models\Discount::where('is_active', true)->get();
        return view('sales.credit-edit', compact('sale', 'products', 'customers', 'discounts'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'discount_id' => 'nullable|exists:discounts,id',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $sale = Sale::findOrFail($id);
        $oldPaid = $sale->paid;
        $oldTotal = $sale->total;
        $oldCustomerId = $sale->customer_id;

        // Restore old stock first
        foreach ($sale->items as $item) {
            $product = \App\Models\Product::find($item->product_id);
            if ($product) {
                $product->increment('quantity', $item->quantity);
            }
        }

        // Delete old items
        $sale->items()->delete();

        // Calculate new totals
        $subtotal = 0;
        foreach ($request->items as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }
        $tax = $subtotal * 0;
        $discount = 0;
        $discountId = null;
        if ($request->discount_id) {
            $selectedDiscount = \App\Models\Discount::find($request->discount_id);
            if ($selectedDiscount) {
                if ($selectedDiscount->type == 'percentage') {
                    $discount = $subtotal * ($selectedDiscount->value / 100);
                } else {
                    $discount = $selectedDiscount->value;
                }
                $discountId = $selectedDiscount->id;
            }
        }
        $total = $subtotal + $tax - $discount;
        $paid = $request->paid ?? $oldPaid;
        $change = max(0, $paid - $total);

        // Update sale
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
            'notes' => $request->notes
        ]);

        // Create new items
        foreach ($request->items as $itemData) {
            $itemTotal = $itemData['quantity'] * $itemData['unit_price'];
            $sale->items()->create([
                'product_id' => $itemData['product_id'],
                'quantity' => $itemData['quantity'],
                'unit_price' => $itemData['unit_price'],
                'discount' => $itemData['discount'] ?? 0,
                'total' => $itemTotal
            ]);

            $product = \App\Models\Product::find($itemData['product_id']);
            if ($product) {
                $product->decrement('quantity', $itemData['quantity']);
            }
        }

        // Adjust customer balance if customer changed or total changed
        if ($oldCustomerId) {
            $oldCustomer = Customer::find($oldCustomerId);
            if ($oldCustomer) {
                $oldCustomer->increment('balance', $oldTotal - $oldPaid);
            }
        }
        if ($request->customer_id) {
            $newCustomer = Customer::find($request->customer_id);
            if ($newCustomer) {
                $newCustomer->decrement('balance', $total - $paid);
            }
        }

        return redirect()->route('sales.credit')->with('success', 'Credit sale updated successfully!');
    }

    public function destroy($id) {
        $sale = Sale::findOrFail($id);
        $sale->update(['status' => 'cancelled']);
        
        foreach ($sale->items as $item) {
            $product = \App\Models\Product::find($item->product_id);
            if ($product) {
                $product->increment('quantity', $item->quantity);
            }
        }

        if ($sale->customer_id) {
            $customer = Customer::find($sale->customer_id);
            if ($customer) {
                $customer->increment('balance', $sale->total - $sale->paid);
            }
        }

        $sale->delete();

        return redirect()->route('sales.credit')->with('success', 'Credit sale cancelled successfully!');
    }
}
