<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CashierController extends Controller
{
    public function index()
    {
        $products = Product::where('active', true)
            ->where('quantity', '>', 0)
            ->get();
        $storeSetting = StoreSetting::first() ?? (object)[
            'store_name' => 'FEEDTAN STORE',
            'logo' => null
        ];
        $customers = Customer::orderBy('name')->get();
        
        return view('cashier.dashboard', compact('products', 'storeSetting', 'customers'));
    }

    public function getProductByBarcode($barcode)
    {
        $product = Product::where('barcode', $barcode)
            ->where('active', true)
            ->where('quantity', '>', 0)
            ->first();
        if (!$product) {
            return response()->json(['error' => 'Product not found or out of stock'], 404);
        }
        return response()->json($product);
    }

    public function searchProducts(Request $request)
    {
        $term = $request->input('term');
        $products = Product::where(function($q) use ($term) {
                $q->where('name', 'like', "%{$term}%")
                  ->orWhere('barcode', 'like', "%{$term}%")
                  ->orWhere('sku', 'like', "%{$term}%");
            })
            ->where('active', true)
            ->where('quantity', '>', 0)
            ->orderBy('name')
            ->get();
        return response()->json($products);
    }

    public function completeSale(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'items.*.id' => 'required|exists:products,id',
            'items.*.name' => 'required|string',
            'items.*.price' => 'required|numeric',
            'items.*.quantity' => 'required|integer|min:1',
            'total' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'paid' => 'required|numeric',
            'payment_method' => 'required|string|in:cash,card,mobile',
            'transaction_id' => 'nullable|string',
            'customer_id' => 'nullable|exists:customers,id'
        ]);

        // Validate stock
        foreach ($data['items'] as $item) {
            $product = Product::find($item['id']);
            if (!$product || $product->quantity < $item['quantity']) {
                return response()->json([
                    'error' => "Insufficient stock for {$item['name']}"
                ], 400);
            }
        }

        $saleNumber = 'SAL-' . date('YmdHis');
        $subtotal = collect($data['items'])->sum(fn($item) => $item['price'] * $item['quantity']);
        $discount = $data['discount'] ?? 0;
        $total = $subtotal - $discount;
        $change = $data['paid'] - $total;

        $sale = Sale::create([
            'sale_number' => $saleNumber,
            'total' => $total,
            'paid' => $data['paid'],
            'payment_method' => $data['payment_method'],
            'transaction_id' => $data['transaction_id'] ?? null,
            'user_id' => Auth::id(),
            'customer_id' => $data['customer_id'] ?? null,
            'type' => $data['customer_id'] ? 'credit' : 'cash',
            'status' => 'completed'
        ]);

        foreach ($data['items'] as $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['id'],
                'product_name' => $item['name'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['price'] * $item['quantity']
            ]);
            
            // Update product stock
            $product = Product::find($item['id']);
            $product->quantity -= $item['quantity'];
            $product->save();
        }

        return response()->json([
            'sale' => $sale,
            'change' => $change,
            'sale_number' => $saleNumber
        ]);
    }

    public function printReceipt($saleId)
    {
        $sale = Sale::with('saleItems')->findOrFail($saleId);
        $storeSetting = StoreSetting::first() ?? (object)[
            'store_name' => 'FEEDTAN STORE',
            'logo' => null
        ];
        return view('sales.receipt-pdf', compact('sale', 'storeSetting'));
    }
}
