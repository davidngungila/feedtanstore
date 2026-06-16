<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Shift;
use App\Models\AccountingEntry;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Auth;

class CashierController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'cashier') {
            return redirect()->route('dashboard');
        }

        $products = Product::with(['category', 'brand', 'unit'])->where('is_active', true)->get();
        $storeSetting = StoreSetting::firstOrCreate();
        
        // Get current shift
        $currentShift = Shift::where('user_id', Auth::id())->whereNull('closed_at')->first();
        
        // Get today's sales
        $todaySales = Sale::whereDate('created_at', today())->where('status', 'completed')->get();
        $todayTotal = $todaySales->sum('total');
        $todayCash = $todaySales->where('payment_method', 'cash')->sum('total');
        $todayMobile = $todaySales->where('payment_method', 'mobile')->sum('total');
        $todayCard = $todaySales->where('payment_method', 'card')->sum('total');
        $todayTransactionsCount = $todaySales->count();
        $todayItemsSold = $todaySales->flatMap->items->sum('quantity');
        
        // Get shift sales if shift exists
        $shiftSales = collect();
        $shiftTotal = 0;
        $shiftCash = 0;
        $shiftMobile = 0;
        $shiftCard = 0;
        $shiftTransactionsCount = 0;
        $shiftItemsSold = 0;
        
        if ($currentShift) {
            $shiftSales = Sale::where('shift_id', $currentShift->id)->where('status', 'completed')->get();
            $shiftTotal = $shiftSales->sum('total');
            $shiftCash = $shiftSales->where('payment_method', 'cash')->sum('total');
            $shiftMobile = $shiftSales->where('payment_method', 'mobile')->sum('total');
            $shiftCard = $shiftSales->where('payment_method', 'card')->sum('total');
            $shiftTransactionsCount = $shiftSales->count();
            $shiftItemsSold = $shiftSales->flatMap->items->sum('quantity');
        }
        
        return view('cashier.dashboard', compact(
            'products', 
            'storeSetting', 
            'todaySales', 
            'todayTotal', 
            'todayCash', 
            'todayMobile', 
            'todayCard', 
            'todayTransactionsCount', 
            'todayItemsSold',
            'currentShift',
            'shiftSales', 
            'shiftTotal', 
            'shiftCash', 
            'shiftMobile', 
            'shiftCard', 
            'shiftTransactionsCount', 
            'shiftItemsSold'
        ));
    }

    public function getProductByBarcode($barcode)
    {
        $product = Product::where('barcode', $barcode)->where('is_active', true)->first();
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        return response()->json($product);
    }

    public function searchProducts(Request $request)
    {
        $term = $request->input('term');
        $products = Product::with(['category', 'brand', 'unit'])
            ->where('is_active', true)
            ->where(function($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('barcode', 'like', "%{$term}%");
            })
            ->get();
        return response()->json($products);
    }

    public function completeSale(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric',
            'paid' => 'required|numeric|min:0',
            'payment_method' => 'required|string|in:cash,card,mobile',
            'transaction_id' => 'required_if:payment_method,card,mobile|string',
            'customer_id' => 'nullable|exists:customers,id'
        ]);

        // Check stock availability
        foreach ($data['items'] as $item) {
            $product = Product::find($item['id']);
            if (!$product) {
                return response()->json(['error' => 'Product not found'], 400);
            }
            if ($product->quantity < $item['quantity']) {
                return response()->json(['error' => "Insufficient stock for {$product->name}. Available: {$product->quantity}"], 400);
            }
        }

        $invoiceNumber = 'INV-' . date('YmdHis');
        $subtotal = collect($data['items'])->sum(fn($item) => $item['price'] * $item['quantity']);
        $tax = 0; // 0% tax
        $discount = $data['discount'] ?? 0;
        $total = $subtotal + $tax - $discount;
        $paid = $data['paid'];
        $change = max(0, $paid - $total);

        $currentShift = Shift::where('user_id', Auth::id())->whereNull('closed_at')->first();

        $sale = Sale::create([
            'invoice_number' => $invoiceNumber,
            'customer_id' => $data['customer_id'] ?? null,
            'user_id' => Auth::id(),
            'shift_id' => $currentShift->id ?? null,
            'discount_id' => null,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $total,
            'paid' => $paid,
            'change' => $change,
            'payment_method' => $data['payment_method'],
            'type' => 'cash',
            'status' => 'completed',
            'notes' => $data['transaction_id'] ?? ''
        ]);

        foreach ($data['items'] as $itemData) {
            $itemTotal = $itemData['quantity'] * $itemData['price'];
            $sale->items()->create([
                'product_id' => $itemData['id'],
                'quantity' => $itemData['quantity'],
                'unit_price' => $itemData['price'],
                'discount' => 0,
                'total' => $itemTotal
            ]);

            $product = Product::find($itemData['id']);
            $product->decrement('quantity', $itemData['quantity']);
        }

        if ($currentShift) {
            if ($data['payment_method'] == 'cash') {
                $currentShift->increment('cash_sales', $total);
            } elseif ($data['payment_method'] == 'card') {
                $currentShift->increment('card_sales', $total);
            } elseif ($data['payment_method'] == 'mobile') {
                $currentShift->increment('mobile_sales', $total);
            }
        }

        $this->createAccountingEntries($sale);

        return response()->json(['sale' => $sale, 'change' => $change, 'sale_id' => $sale->id]);
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
