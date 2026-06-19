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
        $customers = \App\Models\Customer::all();
        
        return view('cashier.dashboard', compact('products', 'storeSetting', 'customers'));
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

    public function getDashboardData()
    {
        $userId = Auth::id();
        $today = now()->startOfDay();
        
        // Get current shift
        $currentShift = Shift::where('user_id', $userId)->whereNull('closed_at')->first();
        
        // Today's sales - eager load items to avoid N+1 queries
        $todaySales = Sale::with('items')->where('user_id', $userId)
            ->where('created_at', '>=', $today)
            ->where('status', 'completed')
            ->latest()
            ->get();
        
        // Shift's sales (if shift exists) - eager load items
        $shiftSales = $currentShift 
            ? Sale::with('items')->where('shift_id', $currentShift->id)->where('status', 'completed')->get()
            : collect();
        
        // Calculate totals
        $todayTotal = $todaySales->sum('total');
        $shiftTotal = $shiftSales->sum('total');
        $todayItems = $todaySales->sum(fn($sale) => $sale->items->sum('quantity'));
        $shiftItems = $shiftSales->sum(fn($sale) => $sale->items->sum('quantity'));
        
        // Payment breakdown
        $todayBreakdown = [
            'cash' => $todaySales->where('payment_method', 'cash')->sum('total'),
            'card' => $todaySales->where('payment_method', 'card')->sum('total'),
            'mobile' => $todaySales->where('payment_method', 'mobile')->sum('total'),
        ];
        
        $shiftBreakdown = [
            'cash' => $shiftSales->where('payment_method', 'cash')->sum('total'),
            'card' => $shiftSales->where('payment_method', 'card')->sum('total'),
            'mobile' => $shiftSales->where('payment_method', 'mobile')->sum('total'),
        ];
        
        return response()->json([
            'todayTotal' => $todayTotal,
            'shiftTotal' => $shiftTotal,
            'todayItems' => $todayItems,
            'shiftItems' => $shiftItems,
            'todayBreakdown' => $todayBreakdown,
            'shiftBreakdown' => $shiftBreakdown,
            'transactions' => $todaySales->take(10)->map(fn($sale) => [
                'id' => $sale->id,
                'invoice_number' => $sale->invoice_number,
                'total' => $sale->total,
                'payment_method' => $sale->payment_method,
                'created_at' => $sale->created_at->format('H:i:s'),
                'items_count' => $sale->items->count()
            ]),
            'currentShift' => $currentShift
        ]);
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
            'notes' => ''
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
        $cashAccount = \App\Models\Account::where('name', 'Cash')->first();
        $salesAccount = \App\Models\Account::where('name', 'Sales')->first();
        $inventoryAccount = \App\Models\Account::where('name', 'Inventory')->first();
        $cogsAccount = \App\Models\Account::where('name', 'Cost of Goods Sold')->first();

        AccountingEntry::create([
            'reference_number' => $sale->invoice_number,
            'reference_type' => Sale::class,
            'account' => 'Cash',
            'account_id' => $cashAccount?->id,
            'type' => 'debit',
            'amount' => $sale->paid,
            'description' => 'Sale payment received'
        ]);

        AccountingEntry::create([
            'reference_number' => $sale->invoice_number,
            'reference_type' => Sale::class,
            'account' => 'Sales',
            'account_id' => $salesAccount?->id,
            'type' => 'credit',
            'amount' => $sale->total,
            'description' => 'Sale completed'
        ]);

        AccountingEntry::create([
            'reference_number' => $sale->invoice_number,
            'reference_type' => Sale::class,
            'account' => 'Inventory',
            'account_id' => $inventoryAccount?->id,
            'type' => 'credit',
            'amount' => $sale->subtotal,
            'description' => 'Inventory sold'
        ]);

        AccountingEntry::create([
            'reference_number' => $sale->invoice_number,
            'reference_type' => Sale::class,
            'account' => 'Cost of Goods Sold',
            'account_id' => $cogsAccount?->id,
            'type' => 'debit',
            'amount' => $sale->subtotal,
            'description' => 'COGS for sale'
        ]);
    }
}
