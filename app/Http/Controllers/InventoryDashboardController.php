<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\StockAdjustment;
use App\Models\StockTransfer;
use App\Models\DamagedGood;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryDashboardController extends Controller
{
    public function index()
    {
        // Total products summary
        $totalProducts = Product::count();
        $totalValue = Product::sum(DB::raw('quantity * cost_price'));
        $totalRetailValue = Product::sum(DB::raw('quantity * selling_price'));

        // Stock status breakdown
        $inStock = Product::where('quantity', '>', DB::raw('reorder_level'))->count();
        $lowStock = Product::whereColumn('quantity', '<=', 'reorder_level')->where('quantity', '>', 0)->count();
        $outOfStock = Product::where('quantity', 0)->count();
        $expiringSoon = Product::whereNotNull('expiry_date')->whereBetween('expiry_date', [now(), now()->addDays(30)])->count();
        $expired = Product::whereNotNull('expiry_date')->where('expiry_date', '<', now())->count();

        // Stock movements (last 30 days)
        $adjustments = StockAdjustment::where('created_at', '>=', now()->subDays(30))->count();
        $transfers = StockTransfer::where('created_at', '>=', now()->subDays(30))->count();
        $damaged = DamagedGood::where('created_at', '>=', now()->subDays(30))->count();

        // Products by category
        $productsByCategory = Category::withCount('products')->orderByDesc('products_count')->limit(10)->get();

        // Products by brand
        $productsByBrand = Brand::withCount('products')->orderByDesc('products_count')->limit(10)->get();

        // Recent stock movements
        $recentAdjustments = StockAdjustment::latest()->limit(10)->get();
        $recentTransfers = StockTransfer::latest()->limit(10)->get();
        $recentDamaged = DamagedGood::latest()->limit(10)->get();

        // Top low stock products
        $lowStockProducts = Product::whereColumn('quantity', '<=', 'reorder_level')
            ->orderBy('quantity', 'asc')
            ->limit(10)
            ->get();

        // Inventory value trend (last 30 days)
        $inventoryValueData = [];
        $labels = [];
        for ($i = 29; $i >= 0; $i--) {
            $labels[] = now()->subDays($i)->format('d M');
            $inventoryValueData[] = Product::sum(DB::raw('quantity * cost_price'));
        }

        return view('dashboard.inventory', compact(
            'totalProducts',
            'totalValue',
            'totalRetailValue',
            'inStock',
            'lowStock',
            'outOfStock',
            'expiringSoon',
            'expired',
            'adjustments',
            'transfers',
            'damaged',
            'productsByCategory',
            'productsByBrand',
            'recentAdjustments',
            'recentTransfers',
            'recentDamaged',
            'lowStockProducts',
            'inventoryValueData',
            'labels'
        ));
    }
}
