<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Customer;
use App\Models\StoreSetting;
use Illuminate\Support\Facades\Auth;

class CashierController extends Controller
{
    public function index()
    {
        if (Auth::user()->role !== 'cashier') {
            return redirect()->route('dashboard');
        }

        $products = Product::all();
        $storeSetting = StoreSetting::first() ?? (object)[
            'store_name' => 'Feedtan Store'
        ];
        
        return view('cashier.dashboard', compact('products', 'storeSetting'));
    }

    public function getProductByBarcode($barcode)
    {
        $product = Product::where('barcode', $barcode)->first();
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }
        return response()->json($product);
    }

    public function searchProducts(Request $request)
    {
        $term = $request->input('term');
        $products = Product::where('name', 'like', "%{$term}%")
            ->orWhere('barcode', 'like', "%{$term}%")
            ->get();
        return response()->json($products);
    }

    public function completeSale(Request $request)
    {
        $data = $request->validate([
            'items' => 'required|array',
            'total' => 'required|numeric',
            'discount' => 'nullable|numeric',
            'paid' => 'required|numeric',
            'payment_method' => 'required|string|in:cash,card,mobile',
            'transaction_id' => 'required_if:payment_method,card,mobile|string',
            'customer_id' => 'nullable|exists:customers,id'
        ]);

        $invoiceNumber = 'INV-' . date('YmdHis');
        $subtotal = collect($data['items'])->sum(fn($item) => $item['price'] * $item['quantity']);
        $discount = $data['discount'] ?? 0;
        $total = $subtotal - $discount;
        $change = max(0, $data['paid'] - $total);

        $sale = Sale::create([
            'invoice_number' => $invoiceNumber,
            'subtotal' => $subtotal,
            'total' => $total,
            'paid' => $data['paid'],
            'change' => $change,
            'discount' => $discount,
            'payment_method' => $data['payment_method'],
            'user_id' => Auth::id(),
            'customer_id' => $data['customer_id'] ?? null,
            'type' => 'cash',
            'status' => 'completed'
        ]);

        foreach ($data['items'] as $item) {
            SaleItem::create([
                'sale_id' => $sale->id,
                'product_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
                'total' => $item['price'] * $item['quantity']
            ]);
        }

        return response()->json(['sale' => $sale, 'change' => $change]);
    }
}
