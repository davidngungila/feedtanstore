<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\StockAdjustment;
use App\Models\StockTransfer;
use App\Models\DamagedGood;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InventoryDashboardController extends Controller
{
    public function index(Request $request)
    {
        // Get filter from request, default to 'day'
        $filter = $request->input('filter', 'day');
        
        // Calculate date range based on filter
        list($startDate, $endDate, $labelFormat) = $this->getDateRange($filter);
        
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

        // Stock movements
        $adjustments = StockAdjustment::whereBetween('created_at', [$startDate, $endDate])->count();
        $transfers = StockTransfer::whereBetween('created_at', [$startDate, $endDate])->count();
        $damaged = DamagedGood::whereBetween('created_at', [$startDate, $endDate])->count();

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

        // Inventory value trend
        $inventoryValueData = [];
        $labels = [];
        $days = $this->getTrendDays($filter);
        for ($i = $days - 1; $i >= 0; $i--) {
            $labels[] = now()->subDays($i)->format($labelFormat);
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
            'labels',
            'filter'
        ));
    }

    private function getDateRange($filter)
    {
        $now = now();
        
        switch ($filter) {
            case 'week':
                return [$now->startOfWeek(), $now->endOfWeek(), 'd M'];
            case 'month':
                return [$now->startOfMonth(), $now->endOfMonth(), 'd M'];
            case '3months':
                return [$now->subMonths(3)->startOfMonth(), $now->endOfMonth(), 'M Y'];
            case '6months':
                return [$now->subMonths(6)->startOfMonth(), $now->endOfMonth(), 'M Y'];
            case 'year':
                return [$now->startOfYear(), $now->endOfYear(), 'M Y'];
            default: // day
                return [$now->startOfDay(), $now->endOfDay(), 'H:i'];
        }
    }

    private function getTrendDays($filter)
    {
        switch ($filter) {
            case 'week':
                return 7;
            case 'month':
                return 30;
            case '3months':
                return 90;
            case '6months':
                return 180;
            case 'year':
                return 365;
            default: // day
                return 24;
        }
    }
}
