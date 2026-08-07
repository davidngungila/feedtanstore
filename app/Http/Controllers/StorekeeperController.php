<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Stock;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StorekeeperController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get store statistics
        $totalProducts = Product::count();
        $lowStockProducts = Product::where('quantity', '<=', 10)->count();
        $outOfStockProducts = Product::where('quantity', '<=', 0)->count();
        $totalStockValue = Product::sum(\DB::raw('quantity * cost_price'));
        
        // Recent purchase orders
        $recentPurchaseOrders = PurchaseOrder::with('supplier')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();
        
        // Low stock products
        $lowStockItems = Product::where('quantity', '<=', 10)
            ->orderBy('quantity', 'asc')
            ->take(10)
            ->get();
        
        return view('storekeeper.dashboard', compact(
            'totalProducts',
            'lowStockProducts',
            'outOfStockProducts',
            'totalStockValue',
            'recentPurchaseOrders',
            'lowStockItems'
        ));
    }

    public function products()
    {
        $products = Product::with('category')->orderBy('name')->paginate(20);
        $categories = Category::all();
        return view('storekeeper.products', compact('products', 'categories'));
    }

    public function stock()
    {
        $products = Product::where('quantity', '<=', 10)
            ->orderBy('quantity', 'asc')
            ->paginate(20);
        return view('storekeeper.stock', compact('products'));
    }

    public function purchaseOrders()
    {
        $purchaseOrders = PurchaseOrder::with('supplier')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        $suppliers = Supplier::all();
        return view('storekeeper.purchase-orders', compact('purchaseOrders', 'suppliers'));
    }

    public function suppliers()
    {
        $suppliers = Supplier::orderBy('name')->paginate(20);
        return view('storekeeper.suppliers', compact('suppliers'));
    }

    public function stockTransfers()
    {
        $transfers = \App\Models\StockTransfer::with(['product', 'fromLocation', 'toLocation', 'requester', 'approver'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('storekeeper.stock-transfers', compact('transfers'));
    }

    public function purchaseOrderRequests()
    {
        $requests = \App\Models\PurchaseOrderRequest::with('product', 'requester', 'supplier')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('storekeeper.purchase-order-requests', compact('requests'));
    }

    public function stockAdjustments()
    {
        $adjustments = \App\Models\StockAdjustment::with('product')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('storekeeper.stock-adjustments', compact('adjustments'));
    }
}
