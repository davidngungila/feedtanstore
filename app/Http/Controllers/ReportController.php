<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\WorkShift;
use App\Models\Attendance;
use App\Models\ActionLog;
use App\Models\Expense;
use App\Models\Income;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\StockAdjustment;
use App\Models\StockTransfer;
use App\Models\Customer;
use App\Models\SaleReturn;
use Dompdf\Dompdf;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    // Helper function for PDF generation
    protected function generatePDF($view, $data, $filename)
    {
        $pdf = new Dompdf();
        $pdf->loadHtml(view($view, $data)->render());
        $pdf->setPaper('A4', 'landscape');
        $pdf->render();
        return $pdf->stream($filename);
    }

    // ==================== Sales Reports ====================
    public function dailySales(Request $request)
    {
        $date = $request->date ?? today()->toDateString();
        
        $todaySales = Sale::whereDate('created_at', $date)->get();
        $totalSales = $todaySales->sum('total');
        $transactionCount = $todaySales->count();
        $averageSale = $transactionCount > 0 ? $totalSales / $transactionCount : 0;
        $itemsSold = SaleItem::whereHas('sale', function($q) use ($date) {
            $q->whereDate('created_at', $date);
        })->sum('quantity');
        
        $paymentMethods = Sale::select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->whereDate('created_at', $date)
            ->groupBy('payment_method')
            ->get();
        $cashTotal = $paymentMethods->where('payment_method', 'cash')->sum('total');
        $cardTotal = $paymentMethods->where('payment_method', 'card')->sum('total');
        $mobileMoneyTotal = $paymentMethods->where('payment_method', 'mobile_money')->sum('total');
        $creditTotal = $paymentMethods->where('payment_method', 'credit')->sum('total');
        
        $transactions = Sale::with(['customer', 'user'])
            ->whereDate('created_at', $date)
            ->latest()
            ->get();
        
        return view('reports.sales.daily', compact(
            'date', 'totalSales', 'transactionCount', 'averageSale', 'itemsSold',
            'cashTotal', 'cardTotal', 'mobileMoneyTotal', 'creditTotal', 'transactions'
        ));
    }

    public function dailySalesPDF(Request $request)
    {
        return $this->generatePDF('reports.sales.daily-pdf', $this->dailySales($request)->getData(), 'daily-sales-report.pdf');
    }

    public function salesByDate(Request $request)
    {
        $startDate = $request->start_date ?? today()->subDays(30)->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $sales = Sale::select(DB::raw('DATE(created_at) as date'), DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get();
        
        return view('reports.sales.by-date', compact('startDate', 'endDate', 'sales'));
    }

    public function salesByDatePDF(Request $request)
    {
        return $this->generatePDF('reports.sales.by-date-pdf', $this->salesByDate($request)->getData(), 'sales-by-date-report.pdf');
    }

    public function hourlySales(Request $request)
    {
        $date = $request->date ?? today()->toDateString();
        
        $sales = Sale::select(DB::raw('HOUR(created_at) as hour'), DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->whereDate('created_at', $date)
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->orderBy('hour')
            ->get();
        
        return view('reports.sales.hourly', compact('date', 'sales'));
    }

    public function hourlySalesPDF(Request $request)
    {
        return $this->generatePDF('reports.sales.hourly-pdf', $this->hourlySales($request)->getData(), 'hourly-sales-report.pdf');
    }

    public function salesByProduct(Request $request)
    {
        $startDate = $request->start_date ?? today()->subDays(30)->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $products = Product::select(
                'products.id',
                'products.name',
                'products.category_id',
                'products.brand_id',
                'products.sku',
                'products.selling_price',
                DB::raw('SUM(sale_items.quantity) as total_qty'),
                DB::raw('SUM(sale_items.total) as total_sales')
            )
            ->with(['category', 'brand'])
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->groupBy('products.id', 'products.name', 'products.category_id', 'products.brand_id', 'products.sku', 'products.selling_price')
            ->orderBy('total_sales', 'desc')
            ->get();
        
        return view('reports.sales.by-product', compact('startDate', 'endDate', 'products'));
    }

    public function salesByProductPDF(Request $request)
    {
        return $this->generatePDF('reports.sales.by-product-pdf', $this->salesByProduct($request)->getData(), 'sales-by-product-report.pdf');
    }

    public function salesByCategory(Request $request)
    {
        $startDate = $request->start_date ?? today()->subDays(30)->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $categories = Category::select(
                'categories.id',
                'categories.name',
                DB::raw('SUM(sale_items.quantity) as total_qty'),
                DB::raw('SUM(sale_items.total) as total_sales')
            )
            ->leftJoin('products', 'categories.id', '=', 'products.category_id')
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->groupBy('categories.id', 'categories.name')
            ->orderBy('total_sales', 'desc')
            ->get();
        
        return view('reports.sales.by-category', compact('startDate', 'endDate', 'categories'));
    }

    public function salesByCategoryPDF(Request $request)
    {
        return $this->generatePDF('reports.sales.by-category-pdf', $this->salesByCategory($request)->getData(), 'sales-by-category-report.pdf');
    }

    public function salesByBrand(Request $request)
    {
        $startDate = $request->start_date ?? today()->subDays(30)->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $brands = Brand::select(
                'brands.id',
                'brands.name',
                DB::raw('SUM(sale_items.quantity) as total_qty'),
                DB::raw('SUM(sale_items.total) as total_sales')
            )
            ->leftJoin('products', 'brands.id', '=', 'products.brand_id')
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->groupBy('brands.id', 'brands.name')
            ->orderBy('total_sales', 'desc')
            ->get();
        
        return view('reports.sales.by-brand', compact('startDate', 'endDate', 'brands'));
    }

    public function salesByBrandPDF(Request $request)
    {
        return $this->generatePDF('reports.sales.by-brand-pdf', $this->salesByBrand($request)->getData(), 'sales-by-brand-report.pdf');
    }

    public function topSelling(Request $request)
    {
        $limit = $request->limit ?? 10;
        $startDate = $request->start_date ?? today()->subDays(30)->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $products = Product::select(
                'products.id',
                'products.name',
                'products.category_id',
                'products.brand_id',
                'products.sku',
                'products.selling_price',
                DB::raw('SUM(sale_items.quantity) as total_qty'),
                DB::raw('SUM(sale_items.total) as total_sales')
            )
            ->with(['category', 'brand'])
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->groupBy('products.id', 'products.name', 'products.category_id', 'products.brand_id', 'products.sku', 'products.selling_price')
            ->orderBy('total_qty', 'desc')
            ->limit($limit)
            ->get();
        
        return view('reports.sales.top-selling', compact('startDate', 'endDate', 'products', 'limit'));
    }

    public function topSellingPDF(Request $request)
    {
        return $this->generatePDF('reports.sales.top-selling-pdf', $this->topSelling($request)->getData(), 'top-selling-products.pdf');
    }

    public function worstSelling(Request $request)
    {
        $limit = $request->limit ?? 10;
        $startDate = $request->start_date ?? today()->subDays(30)->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $products = Product::select(
                'products.id',
                'products.name',
                'products.category_id',
                'products.brand_id',
                'products.sku',
                'products.selling_price',
                DB::raw('COALESCE(SUM(sale_items.quantity), 0) as total_qty'),
                DB::raw('COALESCE(SUM(sale_items.total), 0) as total_sales')
            )
            ->with(['category', 'brand'])
            ->leftJoin('sale_items', function($join) use ($startDate, $endDate) {
                $join->on('products.id', '=', 'sale_items.product_id')
                     ->whereHas('sale', function($q) use ($startDate, $endDate) {
                         $q->whereBetween('sales.created_at', [$startDate, $endDate]);
                     });
            })
            ->groupBy('products.id', 'products.name', 'products.category_id', 'products.brand_id', 'products.sku', 'products.selling_price')
            ->orderBy('total_qty', 'asc')
            ->limit($limit)
            ->get();
        
        return view('reports.sales.worst-selling', compact('startDate', 'endDate', 'products', 'limit'));
    }

    public function worstSellingPDF(Request $request)
    {
        return $this->generatePDF('reports.sales.worst-selling-pdf', $this->worstSelling($request)->getData(), 'worst-selling-products.pdf');
    }

    // ==================== Profit Reports ====================
    public function grossProfit(Request $request)
    {
        $startDate = $request->start_date ?? today()->subDays(30)->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $products = Product::select(
                'products.id',
                'products.name',
                'products.category_id',
                'products.brand_id',
                'products.sku',
                'products.cost_price',
                'products.selling_price',
                DB::raw('SUM(sale_items.quantity) as total_qty'),
                DB::raw('SUM(sale_items.total) as total_sales'),
                DB::raw('SUM(sale_items.quantity * products.cost_price) as total_cost')
            )
            ->with(['category', 'brand'])
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->groupBy('products.id', 'products.name', 'products.category_id', 'products.brand_id', 'products.sku', 'products.cost_price', 'products.selling_price')
            ->orderBy('total_sales', 'desc')
            ->get();
        
        $totalSales = $products->sum('total_sales');
        $totalCost = $products->sum('total_cost');
        $totalProfit = $totalSales - $totalCost;
        
        return view('reports.profit.gross', compact('startDate', 'endDate', 'products', 'totalSales', 'totalCost', 'totalProfit'));
    }

    public function grossProfitPDF(Request $request)
    {
        return $this->generatePDF('reports.profit.gross-pdf', $this->grossProfit($request)->getData(), 'gross-profit-report.pdf');
    }

    public function profitMargin(Request $request)
    {
        $startDate = $request->start_date ?? today()->subDays(30)->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $products = Product::select(
                'products.id',
                'products.name',
                'products.category_id',
                'products.brand_id',
                'products.sku',
                'products.cost_price',
                'products.selling_price',
                DB::raw('SUM(sale_items.quantity) as total_qty'),
                DB::raw('SUM(sale_items.total) as total_sales'),
                DB::raw('SUM(sale_items.quantity * products.cost_price) as total_cost')
            )
            ->with(['category', 'brand'])
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->groupBy('products.id', 'products.name', 'products.category_id', 'products.brand_id', 'products.sku', 'products.cost_price', 'products.selling_price')
            ->get()
            ->map(function($product) {
                $product->profit = $product->total_sales - $product->total_cost;
                $product->margin_percent = $product->total_sales > 0 ? round(($product->profit / $product->total_sales) * 100, 2) : 0;
                return $product;
            })
            ->sortByDesc('margin_percent');
        
        return view('reports.profit.margin', compact('startDate', 'endDate', 'products'));
    }

    public function profitMarginPDF(Request $request)
    {
        return $this->generatePDF('reports.profit.margin-pdf', $this->profitMargin($request)->getData(), 'profit-margin-report.pdf');
    }

    public function profitByCategory(Request $request)
    {
        $startDate = $request->start_date ?? today()->subDays(30)->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $categories = Category::select(
                'categories.id',
                'categories.name',
                DB::raw('SUM(sale_items.total) as total_sales'),
                DB::raw('SUM(sale_items.quantity * products.cost_price) as total_cost')
            )
            ->leftJoin('products', 'categories.id', '=', 'products.category_id')
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->groupBy('categories.id', 'categories.name')
            ->get()
            ->map(function($category) {
                $category->profit = $category->total_sales - $category->total_cost;
                $category->margin_percent = $category->total_sales > 0 ? round(($category->profit / $category->total_sales) * 100, 2) : 0;
                return $category;
            });
        
        return view('reports.profit.by-category', compact('startDate', 'endDate', 'categories'));
    }

    public function profitByCategoryPDF(Request $request)
    {
        return $this->generatePDF('reports.profit.by-category-pdf', $this->profitByCategory($request)->getData(), 'profit-by-category-report.pdf');
    }

    public function netProfit(Request $request)
    {
        $startDate = $request->start_date ?? today()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $sales = Sale::whereBetween('created_at', [$startDate, $endDate])->sum('total');
        $expenses = Expense::whereBetween('created_at', [$startDate, $endDate])->sum('amount');
        
        $productCosts = Product::select(DB::raw('SUM(sale_items.quantity * products.cost_price) as total_cost'))
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->first()
            ->total_cost ?? 0;
        
        $costOfGoodsSold = $productCosts;
        $grossProfit = $sales - $costOfGoodsSold;
        $netProfit = $grossProfit - $expenses;
        
        return view('reports.profit.net', compact('startDate', 'endDate', 'sales', 'costOfGoodsSold', 'grossProfit', 'expenses', 'netProfit'));
    }

    public function netProfitPDF(Request $request)
    {
        return $this->generatePDF('reports.profit.net-pdf', $this->netProfit($request)->getData(), 'net-profit-report.pdf');
    }

    public function lossReport(Request $request)
    {
        $startDate = $request->start_date ?? today()->subDays(30)->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $adjustments = StockAdjustment::with('product', 'user')
            ->where('type', 'decrease')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
        
        $totalLoss = $adjustments->sum(function($adj) {
            return $adj->quantity * ($adj->product->cost_price ?? 0);
        });
        
        return view('reports.profit.loss', compact('startDate', 'endDate', 'adjustments', 'totalLoss'));
    }

    public function lossReportPDF(Request $request)
    {
        return $this->generatePDF('reports.profit.loss-pdf', $this->lossReport($request)->getData(), 'loss-report.pdf');
    }

    // ==================== Inventory Reports ====================
    public function currentStock()
    {
        $products = Product::with(['category', 'brand'])
            ->orderBy('name')
            ->get();
        
        $totalStockValue = $products->sum(function($product) {
            return $product->quantity * $product->cost_price;
        });
        
        return view('reports.inventory.current-stock', compact('products', 'totalStockValue'));
    }

    public function currentStockPDF()
    {
        return $this->generatePDF('reports.inventory.current-stock-pdf', $this->currentStock()->getData(), 'current-stock-report.pdf');
    }

    public function inventoryValuation()
    {
        $products = Product::with(['category', 'brand'])
            ->orderBy('name')
            ->get();
        
        $totalCostValue = $products->sum(function($product) {
            return $product->quantity * $product->cost_price;
        });
        $totalRetailValue = $products->sum(function($product) {
            return $product->quantity * $product->selling_price;
        });
        
        return view('reports.inventory.valuation', compact('products', 'totalCostValue', 'totalRetailValue'));
    }

    public function inventoryValuationPDF()
    {
        return $this->generatePDF('reports.inventory.valuation-pdf', $this->inventoryValuation()->getData(), 'inventory-valuation-report.pdf');
    }

    public function stockMovement(Request $request)
    {
        $startDate = $request->start_date ?? today()->subDays(30)->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        $productId = $request->product_id;
        
        $query = Product::with(['category', 'brand']);
        
        if ($productId) {
            $query->where('id', $productId);
        }
        
        $products = $query->get();
        $allProducts = Product::orderBy('name')->get();
        
        return view('reports.inventory.movement', compact('startDate', 'endDate', 'products', 'productId', 'allProducts'));
    }

    public function stockMovementPDF(Request $request)
    {
        return $this->generatePDF('reports.inventory.movement-pdf', $this->stockMovement($request)->getData(), 'stock-movement-report.pdf');
    }

    public function stockIn(Request $request)
    {
        $startDate = $request->start_date ?? today()->subDays(30)->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $grns = \App\Models\GoodsReceivedNote::with(['supplier', 'items.product'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();
        
        return view('reports.inventory.stock-in', compact('startDate', 'endDate', 'grns'));
    }

    public function stockInPDF(Request $request)
    {
        return $this->generatePDF('reports.inventory.stock-in-pdf', $this->stockIn($request)->getData(), 'stock-in-report.pdf');
    }

    public function stockOut(Request $request)
    {
        $startDate = $request->start_date ?? today()->subDays(30)->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $adjustments = StockAdjustment::with(['product', 'user'])
            ->where('type', 'decrease')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();
        
        $returns = SaleReturn::with(['sale', 'items.product'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();
        
        return view('reports.inventory.stock-out', compact('startDate', 'endDate', 'adjustments', 'returns'));
    }

    public function stockOutPDF(Request $request)
    {
        return $this->generatePDF('reports.inventory.stock-out-pdf', $this->stockOut($request)->getData(), 'stock-out-report.pdf');
    }

    public function stockTransfers(Request $request)
    {
        $startDate = $request->start_date ?? today()->subDays(30)->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $transfers = StockTransfer::with(['product', 'fromLocation', 'toLocation', 'user'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();
        
        return view('reports.inventory.transfers', compact('startDate', 'endDate', 'transfers'));
    }

    public function stockTransfersPDF(Request $request)
    {
        return $this->generatePDF('reports.inventory.transfers-pdf', $this->stockTransfers($request)->getData(), 'stock-transfers-report.pdf');
    }

    public function lowStock()
    {
        $products = Product::with(['category', 'brand'])
            ->whereColumn('quantity', '<=', 'reorder_level')
            ->where('quantity', '>', 0)
            ->orderBy('name')
            ->get();
        
        return view('reports.inventory.low-stock', compact('products'));
    }

    public function lowStockPDF()
    {
        return $this->generatePDF('reports.inventory.low-stock-pdf', $this->lowStock()->getData(), 'low-stock-report.pdf');
    }

    public function outOfStock()
    {
        $products = Product::with(['category', 'brand'])
            ->where('quantity', 0)
            ->orderBy('name')
            ->get();
        
        return view('reports.inventory.out-of-stock', compact('products'));
    }

    public function outOfStockPDF()
    {
        return $this->generatePDF('reports.inventory.out-of-stock-pdf', $this->outOfStock()->getData(), 'out-of-stock-report.pdf');
    }

    public function overstock(Request $request)
    {
        $threshold = $request->threshold ?? 100;
        
        $products = Product::with(['category', 'brand'])
            ->where('quantity', '>', $threshold)
            ->orderBy('quantity', 'desc')
            ->get();
        
        return view('reports.inventory.overstock', compact('products', 'threshold'));
    }

    public function overstockPDF(Request $request)
    {
        return $this->generatePDF('reports.inventory.overstock-pdf', $this->overstock($request)->getData(), 'overstock-report.pdf');
    }

    public function fastMoving(Request $request)
    {
        $days = $request->days ?? 30;
        $threshold = $request->threshold ?? 50;
        
        $products = Product::select(
                'products.id',
                'products.name',
                'products.category_id',
                'products.brand_id',
                'products.sku',
                'products.quantity',
                DB::raw('SUM(sale_items.quantity) as total_sold')
            )
            ->with(['category', 'brand'])
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->where('sales.created_at', '>=', today()->subDays($days))
            ->groupBy('products.id', 'products.name', 'products.category_id', 'products.brand_id', 'products.sku', 'products.quantity')
            ->having('total_sold', '>=', $threshold)
            ->orderBy('total_sold', 'desc')
            ->get();
        
        return view('reports.inventory.fast-moving', compact('products', 'days', 'threshold'));
    }

    public function fastMovingPDF(Request $request)
    {
        return $this->generatePDF('reports.inventory.fast-moving-pdf', $this->fastMoving($request)->getData(), 'fast-moving-items-report.pdf');
    }

    public function slowMoving(Request $request)
    {
        $days = $request->days ?? 30;
        $threshold = $request->threshold ?? 10;
        
        $products = Product::select(
                'products.id',
                'products.name',
                'products.category_id',
                'products.brand_id',
                'products.sku',
                'products.quantity',
                DB::raw('COALESCE(SUM(sale_items.quantity), 0) as total_sold')
            )
            ->with(['category', 'brand'])
            ->leftJoin('sale_items', function($join) use ($days) {
                $join->on('products.id', '=', 'sale_items.product_id')
                     ->whereHas('sale', function($q) use ($days) {
                         $q->where('sales.created_at', '>=', today()->subDays($days));
                     });
            })
            ->groupBy('products.id', 'products.name', 'products.category_id', 'products.brand_id', 'products.sku', 'products.quantity')
            ->having('total_sold', '<=', $threshold)
            ->orderBy('total_sold', 'asc')
            ->get();
        
        return view('reports.inventory.slow-moving', compact('products', 'days', 'threshold'));
    }

    public function slowMovingPDF(Request $request)
    {
        return $this->generatePDF('reports.inventory.slow-moving-pdf', $this->slowMoving($request)->getData(), 'slow-moving-items-report.pdf');
    }

    public function deadStock(Request $request)
    {
        $days = $request->days ?? 90;
        
        $products = Product::select('products.*')
            ->whereDoesntHave('saleItems', function($q) use ($days) {
                $q->whereHas('sale', function($sq) use ($days) {
                    $sq->where('sales.created_at', '>=', today()->subDays($days));
                });
            })
            ->where('quantity', '>', 0)
            ->orderBy('name')
            ->get();
        
        return view('reports.inventory.dead-stock', compact('products', 'days'));
    }

    public function deadStockPDF(Request $request)
    {
        return $this->generatePDF('reports.inventory.dead-stock-pdf', $this->deadStock($request)->getData(), 'dead-stock-report.pdf');
    }

    // ==================== Expiry Reports ====================
    public function expiringSoon(Request $request)
    {
        $days = $request->days ?? 30;
        
        $products = Product::with(['category', 'brand'])
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<=', today()->addDays($days))
            ->where('expiry_date', '>=', today())
            ->orderBy('expiry_date')
            ->get();
        
        return view('reports.expiry.soon', compact('products', 'days'));
    }

    public function expiringSoonPDF(Request $request)
    {
        return $this->generatePDF('reports.expiry.soon-pdf', $this->expiringSoon($request)->getData(), 'expiring-soon-report.pdf');
    }

    public function expiredProducts()
    {
        $products = Product::with(['category', 'brand'])
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', today())
            ->orderBy('expiry_date')
            ->get();
        
        $totalValue = $products->sum(function($product) {
            return $product->quantity * $product->cost_price;
        });
        
        return view('reports.expiry.expired', compact('products', 'totalValue'));
    }

    public function expiredProductsPDF()
    {
        return $this->generatePDF('reports.expiry.expired-pdf', $this->expiredProducts()->getData(), 'expired-products-report.pdf');
    }

    public function batchTracking()
    {
        $products = Product::with(['category', 'brand'])
            ->whereNotNull('batch_number')
            ->orderBy('batch_number')
            ->get();
        
        return view('reports.expiry.batch-tracking', compact('products'));
    }

    public function batchTrackingPDF()
    {
        return $this->generatePDF('reports.expiry.batch-tracking-pdf', $this->batchTracking()->getData(), 'batch-tracking-report.pdf');
    }

    // ==================== Purchasing Reports ====================
    public function purchaseSummary(Request $request)
    {
        $startDate = $request->start_date ?? today()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $purchaseOrders = PurchaseOrder::with(['supplier'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->get();
        
        $totalPurchases = $purchaseOrders->sum('total');
        
        return view('reports.purchasing.summary', compact('startDate', 'endDate', 'purchaseOrders', 'totalPurchases'));
    }

    public function purchaseSummaryPDF(Request $request)
    {
        return $this->generatePDF('reports.purchasing.summary-pdf', $this->purchaseSummary($request)->getData(), 'purchase-summary-report.pdf');
    }

    public function purchaseBySupplier(Request $request)
    {
        $startDate = $request->start_date ?? today()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $suppliers = Supplier::select('suppliers.*', DB::raw('SUM(purchase_orders.total) as total_purchases'))
            ->leftJoin('purchase_orders', 'suppliers.id', '=', 'purchase_orders.supplier_id')
            ->whereBetween('purchase_orders.created_at', [$startDate, $endDate])
            ->groupBy('suppliers.id')
            ->orderBy('total_purchases', 'desc')
            ->get();
        
        return view('reports.purchasing.by-supplier', compact('startDate', 'endDate', 'suppliers'));
    }

    public function purchaseBySupplierPDF(Request $request)
    {
        return $this->generatePDF('reports.purchasing.by-supplier-pdf', $this->purchaseBySupplier($request)->getData(), 'purchase-by-supplier-report.pdf');
    }

    public function supplierPerformance(Request $request)
    {
        $startDate = $request->start_date ?? today()->subDays(90)->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $suppliers = Supplier::with(['purchaseOrders' => function($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        }])->get();
        
        return view('reports.purchasing.supplier-performance', compact('startDate', 'endDate', 'suppliers'));
    }

    public function supplierPerformancePDF(Request $request)
    {
        return $this->generatePDF('reports.purchasing.supplier-performance-pdf', $this->supplierPerformance($request)->getData(), 'supplier-performance-report.pdf');
    }

    public function purchaseVsSales(Request $request)
    {
        $startDate = $request->start_date ?? today()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $purchases = PurchaseOrder::whereBetween('created_at', [$startDate, $endDate])->sum('total');
        $sales = Sale::whereBetween('created_at', [$startDate, $endDate])->sum('total');
        
        return view('reports.purchasing.vs-sales', compact('startDate', 'endDate', 'purchases', 'sales'));
    }

    public function purchaseVsSalesPDF(Request $request)
    {
        return $this->generatePDF('reports.purchasing.vs-sales-pdf', $this->purchaseVsSales($request)->getData(), 'purchase-vs-sales-report.pdf');
    }

    public function purchaseOrders(Request $request)
    {
        $startDate = $request->start_date ?? today()->subDays(30)->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        $status = $request->status;
        
        $query = PurchaseOrder::with(['supplier']);
        
        if ($status) {
            $query->where('status', $status);
        }
        
        $purchaseOrders = $query->whereBetween('created_at', [$startDate, $endDate])->latest()->get();
        
        return view('reports.purchasing.purchase-orders', compact('startDate', 'endDate', 'status', 'purchaseOrders'));
    }

    public function purchaseOrdersPDF(Request $request)
    {
        return $this->generatePDF('reports.purchasing.purchase-orders-pdf', $this->purchaseOrders($request)->getData(), 'purchase-orders-report.pdf');
    }

    // ==================== Cash & Payment Reports ====================
    public function cashierShift(Request $request)
    {
        $startDate = $request->start_date ?? today()->subDays(7)->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $shifts = WorkShift::with(['user', 'cashier'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();
        
        return view('reports.cash.cashier-shift', compact('startDate', 'endDate', 'shifts'));
    }

    public function cashierShiftPDF(Request $request)
    {
        return $this->generatePDF('reports.cash.cashier-shift-pdf', $this->cashierShift($request)->getData(), 'cashier-shift-report.pdf');
    }

    public function cashReconciliation(Request $request)
    {
        $date = $request->date ?? today()->toDateString();
        
        $sales = Sale::whereDate('created_at', $date)->where('payment_method', 'cash')->sum('total');
        $expenses = Expense::whereDate('created_at', $date)->sum('amount');
        
        return view('reports.cash.reconciliation', compact('date', 'sales', 'expenses'));
    }

    public function cashReconciliationPDF(Request $request)
    {
        return $this->generatePDF('reports.cash.reconciliation-pdf', $this->cashReconciliation($request)->getData(), 'cash-reconciliation-report.pdf');
    }

    public function paymentMethod(Request $request)
    {
        $startDate = $request->start_date ?? today()->subDays(30)->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $methods = Sale::select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->whereBetween('created_at', [$startDate, $endDate])
            ->groupBy('payment_method')
            ->get();
        
        return view('reports.cash.payment-method', compact('startDate', 'endDate', 'methods'));
    }

    public function paymentMethodPDF(Request $request)
    {
        return $this->generatePDF('reports.cash.payment-method-pdf', $this->paymentMethod($request)->getData(), 'payment-method-report.pdf');
    }

    public function dailyCashFlow(Request $request)
    {
        $startDate = $request->start_date ?? today()->subDays(30)->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $incomes = Income::whereBetween('created_at', [$startDate, $endDate])->get();
        $expenses = Expense::whereBetween('created_at', [$startDate, $endDate])->get();
        
        $totalIncome = $incomes->sum('amount');
        $totalExpenses = $expenses->sum('amount');
        $netCashFlow = $totalIncome - $totalExpenses;
        
        return view('reports.cash.daily-flow', compact('startDate', 'endDate', 'incomes', 'expenses', 'totalIncome', 'totalExpenses', 'netCashFlow'));
    }

    public function dailyCashFlowPDF(Request $request)
    {
        return $this->generatePDF('reports.cash.daily-flow-pdf', $this->dailyCashFlow($request)->getData(), 'daily-cash-flow-report.pdf');
    }

    // ==================== Cashier / Staff Reports ====================
    public function salesByCashier(Request $request)
    {
        $startDate = $request->start_date ?? today()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $cashiers = User::select('users.*', DB::raw('COUNT(sales.id) as transaction_count'), DB::raw('SUM(sales.total) as total_sales'))
            ->leftJoin('sales', 'users.id', '=', 'sales.user_id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->groupBy('users.id')
            ->orderBy('total_sales', 'desc')
            ->get();
        
        return view('reports.staff.sales-by-cashier', compact('startDate', 'endDate', 'cashiers'));
    }

    public function salesByCashierPDF(Request $request)
    {
        return $this->generatePDF('reports.staff.sales-by-cashier-pdf', $this->salesByCashier($request)->getData(), 'sales-by-cashier-report.pdf');
    }

    public function transactionCount(Request $request)
    {
        $startDate = $request->start_date ?? today()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $cashiers = User::select('users.*', DB::raw('COUNT(sales.id) as transaction_count'))
            ->leftJoin('sales', 'users.id', '=', 'sales.user_id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->groupBy('users.id')
            ->orderBy('transaction_count', 'desc')
            ->get();
        
        return view('reports.staff.transaction-count', compact('startDate', 'endDate', 'cashiers'));
    }

    public function transactionCountPDF(Request $request)
    {
        return $this->generatePDF('reports.staff.transaction-count-pdf', $this->transactionCount($request)->getData(), 'transaction-count-report.pdf');
    }

    public function cashierActivity(Request $request)
    {
        $startDate = $request->start_date ?? today()->subDays(30)->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $activities = ActionLog::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();
        
        return view('reports.staff.activity', compact('startDate', 'endDate', 'activities'));
    }

    public function cashierActivityPDF(Request $request)
    {
        return $this->generatePDF('reports.staff.activity-pdf', $this->cashierActivity($request)->getData(), 'cashier-activity-report.pdf');
    }

    public function discountReport(Request $request)
    {
        $startDate = $request->start_date ?? today()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $sales = Sale::with(['user'])
            ->whereNotNull('discount')
            ->where('discount', '>', 0)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();
        
        $totalDiscounts = $sales->sum('discount');
        
        return view('reports.staff.discounts', compact('startDate', 'endDate', 'sales', 'totalDiscounts'));
    }

    public function discountReportPDF(Request $request)
    {
        return $this->generatePDF('reports.staff.discounts-pdf', $this->discountReport($request)->getData(), 'discount-report.pdf');
    }

    public function voidTransactions(Request $request)
    {
        $startDate = $request->start_date ?? today()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $cancelledSales = \App\Models\CancelledSale::with(['sale', 'user'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();
        
        return view('reports.staff.void-transactions', compact('startDate', 'endDate', 'cancelledSales'));
    }

    public function voidTransactionsPDF(Request $request)
    {
        return $this->generatePDF('reports.staff.void-transactions-pdf', $this->voidTransactions($request)->getData(), 'void-transactions-report.pdf');
    }

    public function refundReport(Request $request)
    {
        $startDate = $request->start_date ?? today()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $returns = SaleReturn::with(['sale', 'user', 'items.product'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();
        
        $totalRefunded = $returns->sum('total');
        
        return view('reports.staff.refunds', compact('startDate', 'endDate', 'returns', 'totalRefunded'));
    }

    public function refundReportPDF(Request $request)
    {
        return $this->generatePDF('reports.staff.refunds-pdf', $this->refundReport($request)->getData(), 'refund-report.pdf');
    }

    // ==================== Customer Reports ====================
    public function customerSales(Request $request)
    {
        $startDate = $request->start_date ?? today()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $customers = Customer::select('customers.*', DB::raw('COUNT(sales.id) as purchase_count'), DB::raw('SUM(sales.total) as total_spent'))
            ->leftJoin('sales', 'customers.id', '=', 'sales.customer_id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->groupBy('customers.id')
            ->orderBy('total_spent', 'desc')
            ->get();
        
        return view('reports.customer.sales', compact('startDate', 'endDate', 'customers'));
    }

    public function customerSalesPDF(Request $request)
    {
        return $this->generatePDF('reports.customer.sales-pdf', $this->customerSales($request)->getData(), 'customer-sales-report.pdf');
    }

    public function customerPurchaseHistory(Request $request)
    {
        $customerId = $request->customer_id;
        $startDate = $request->start_date ?? today()->subDays(90)->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $query = Sale::with(['customer', 'items.product']);
        
        if ($customerId) {
            $query->where('customer_id', $customerId);
        }
        
        $sales = $query->whereBetween('created_at', [$startDate, $endDate])->latest()->get();
        $customers = Customer::orderBy('name')->get();
        
        return view('reports.customer.purchase-history', compact('startDate', 'endDate', 'sales', 'customers', 'customerId'));
    }

    public function customerPurchaseHistoryPDF(Request $request)
    {
        return $this->generatePDF('reports.customer.purchase-history-pdf', $this->customerPurchaseHistory($request)->getData(), 'customer-purchase-history.pdf');
    }

    public function loyaltyReport(Request $request)
    {
        $customers = Customer::orderBy('loyalty_points', 'desc')->get();
        
        return view('reports.customer.loyalty', compact('customers'));
    }

    public function loyaltyReportPDF(Request $request)
    {
        return $this->generatePDF('reports.customer.loyalty-pdf', $this->loyaltyReport($request)->getData(), 'loyalty-report.pdf');
    }

    // ==================== Security & Audit Reports ====================
    public function auditLog(Request $request)
    {
        $startDate = $request->start_date ?? today()->subDays(30)->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $logs = ActionLog::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();
        
        return view('reports.security.audit-log', compact('startDate', 'endDate', 'logs'));
    }

    public function auditLogPDF(Request $request)
    {
        return $this->generatePDF('reports.security.audit-log-pdf', $this->auditLog($request)->getData(), 'audit-log-report.pdf');
    }

    public function priceChanges(Request $request)
    {
        $startDate = $request->start_date ?? today()->subDays(30)->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $logs = ActionLog::with('user')
            ->where('action', 'like', '%price%')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();
        
        return view('reports.security.price-changes', compact('startDate', 'endDate', 'logs'));
    }

    public function priceChangesPDF(Request $request)
    {
        return $this->generatePDF('reports.security.price-changes-pdf', $this->priceChanges($request)->getData(), 'price-changes-report.pdf');
    }

    public function inventoryAdjustments(Request $request)
    {
        $startDate = $request->start_date ?? today()->subDays(30)->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $adjustments = StockAdjustment::with(['product', 'user'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->latest()
            ->get();
        
        return view('reports.security.inventory-adjustments', compact('startDate', 'endDate', 'adjustments'));
    }

    public function inventoryAdjustmentsPDF(Request $request)
    {
        return $this->generatePDF('reports.security.inventory-adjustments-pdf', $this->inventoryAdjustments($request)->getData(), 'inventory-adjustments-report.pdf');
    }

    public function userActivity(Request $request)
    {
        $startDate = $request->start_date ?? today()->subDays(30)->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        $userId = $request->user_id;
        
        $query = ActionLog::with('user');
        
        if ($userId) {
            $query->where('user_id', $userId);
        }
        
        $logs = $query->whereBetween('created_at', [$startDate, $endDate])->latest()->get();
        $users = User::orderBy('name')->get();
        
        return view('reports.security.user-activity', compact('startDate', 'endDate', 'logs', 'users', 'userId'));
    }

    public function userActivityPDF(Request $request)
    {
        return $this->generatePDF('reports.security.user-activity-pdf', $this->userActivity($request)->getData(), 'user-activity-report.pdf');
    }

    // ==================== Management Dashboard Reports ====================
    public function executiveDashboard(Request $request)
    {
        $today = today();
        $thisMonth = $today->format('Y-m');
        
        $todaySales = Sale::whereDate('created_at', $today)->sum('total');
        $todayTransactions = Sale::whereDate('created_at', $today)->count();
        $monthSales = Sale::whereMonth('created_at', $today->month)->sum('total');
        $lowStockCount = Product::whereColumn('quantity', '<=', 'reorder_level')->where('quantity', '>', 0)->count();
        $outOfStockCount = Product::where('quantity', 0)->count();
        $totalStockValue = Product::sum(DB::raw('quantity * cost_price'));
        
        $topProducts = Product::select('products.*', DB::raw('SUM(sale_items.quantity) as total_qty'))
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereMonth('sales.created_at', $today->month)
            ->groupBy('products.id')
            ->orderBy('total_qty', 'desc')
            ->limit(5)
            ->get();
        
        $recentSales = Sale::with(['customer', 'user'])->latest()->limit(10)->get();
        
        return view('reports.management.executive', compact(
            'todaySales', 'todayTransactions', 'monthSales', 'lowStockCount', 
            'outOfStockCount', 'totalStockValue', 'topProducts', 'recentSales'
        ));
    }

    public function executiveDashboardPDF()
    {
        return $this->generatePDF('reports.management.executive-pdf', $this->executiveDashboard()->getData(), 'executive-dashboard-report.pdf');
    }

    public function inventoryInvestment()
    {
        $categories = Category::select('categories.*', DB::raw('SUM(products.quantity * products.cost_price) as investment_value'))
            ->leftJoin('products', 'categories.id', '=', 'products.category_id')
            ->groupBy('categories.id')
            ->orderBy('investment_value', 'desc')
            ->get();
        
        $totalInvestment = $categories->sum('investment_value');
        
        return view('reports.management.inventory-investment', compact('categories', 'totalInvestment'));
    }

    public function inventoryInvestmentPDF()
    {
        return $this->generatePDF('reports.management.inventory-investment-pdf', $this->inventoryInvestment()->getData(), 'inventory-investment-report.pdf');
    }

    public function inventoryTurnover(Request $request)
    {
        $startDate = $request->start_date ?? today()->startOfYear()->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $categories = Category::select('categories.*', DB::raw('SUM(sale_items.quantity * products.cost_price) as cogs'))
            ->leftJoin('products', 'categories.id', '=', 'products.category_id')
            ->join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->groupBy('categories.id')
            ->get()
            ->map(function($category) {
                $avgInventory = Product::where('category_id', $category->id)
                    ->sum(DB::raw('quantity * cost_price'));
                $category->turnover = $avgInventory > 0 ? round($category->cogs / $avgInventory, 2) : 0;
                return $category;
            });
        
        return view('reports.management.inventory-turnover', compact('startDate', 'endDate', 'categories'));
    }

    public function inventoryTurnoverPDF(Request $request)
    {
        return $this->generatePDF('reports.management.inventory-turnover-pdf', $this->inventoryTurnover($request)->getData(), 'inventory-turnover-report.pdf');
    }

    public function stockAccuracy()
    {
        $products = Product::with(['category'])->get();
        
        return view('reports.management.stock-accuracy', compact('products'));
    }

    public function stockAccuracyPDF()
    {
        return $this->generatePDF('reports.management.stock-accuracy-pdf', $this->stockAccuracy()->getData(), 'stock-accuracy-report.pdf');
    }

    public function businessGrowth(Request $request)
    {
        $months = [];
        $salesData = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $date = today()->subMonths($i);
            $monthStart = $date->startOfMonth();
            $monthEnd = $date->endOfMonth();
            
            $sales = Sale::whereBetween('created_at', [$monthStart, $monthEnd])->sum('total');
            $profit = $this->calculateProfitForPeriod($monthStart, $monthEnd);
            
            $months[] = $date->format('M Y');
            $salesData[] = [
                'month' => $date->format('M Y'),
                'sales' => $sales,
                'profit' => $profit
            ];
        }
        
        return view('reports.management.business-growth', compact('months', 'salesData'));
    }

    protected function calculateProfitForPeriod($start, $end)
    {
        $sales = Sale::whereBetween('created_at', [$start, $end])->sum('total');
        $costs = Product::join('sale_items', 'products.id', '=', 'sale_items.product_id')
            ->join('sales', 'sales.id', '=', 'sale_items.sale_id')
            ->whereBetween('sales.created_at', [$start, $end])
            ->sum(DB::raw('sale_items.quantity * products.cost_price'));
        
        return $sales - $costs;
    }

    public function businessGrowthPDF(Request $request)
    {
        return $this->generatePDF('reports.management.business-growth-pdf', $this->businessGrowth($request)->getData(), 'business-growth-report.pdf');
    }

    // ==================== FeedTan Store Advanced Reports ====================
    public function branchComparison()
    {
        $branches = \App\Models\Branch::all();
        
        return view('reports.advanced.branch-comparison', compact('branches'));
    }

    public function branchComparisonPDF()
    {
        return $this->generatePDF('reports.advanced.branch-comparison-pdf', $this->branchComparison()->getData(), 'branch-comparison-report.pdf');
    }

    public function branchProfit()
    {
        $branches = \App\Models\Branch::all();
        
        return view('reports.advanced.branch-profit', compact('branches'));
    }

    public function branchProfitPDF()
    {
        return $this->generatePDF('reports.advanced.branch-profit-pdf', $this->branchProfit()->getData(), 'branch-profit-report.pdf');
    }

    public function expansionReadiness()
    {
        $branches = \App\Models\Branch::all();
        
        return view('reports.advanced.expansion-readiness', compact('branches'));
    }

    public function expansionReadinessPDF()
    {
        return $this->generatePDF('reports.advanced.expansion-readiness-pdf', $this->expansionReadiness()->getData(), 'expansion-readiness-report.pdf');
    }

    public function memberPurchase(Request $request)
    {
        $startDate = $request->start_date ?? today()->startOfMonth()->toDateString();
        $endDate = $request->end_date ?? today()->toDateString();
        
        $members = Customer::select('customers.*', DB::raw('COUNT(sales.id) as purchase_count'), DB::raw('SUM(sales.total) as total_spent'))
            ->leftJoin('sales', 'customers.id', '=', 'sales.customer_id')
            ->whereBetween('sales.created_at', [$startDate, $endDate])
            ->where('customers.is_member', true)
            ->groupBy('customers.id')
            ->orderBy('total_spent', 'desc')
            ->get();
        
        return view('reports.advanced.member-purchase', compact('startDate', 'endDate', 'members'));
    }

    public function memberPurchasePDF(Request $request)
    {
        return $this->generatePDF('reports.advanced.member-purchase-pdf', $this->memberPurchase($request)->getData(), 'member-purchase-report.pdf');
    }

    public function supplierCredit(Request $request)
    {
        $suppliers = Supplier::with(['purchaseOrders' => function($q) {
            $q->where('status', 'pending');
        }])->get();
        
        $totalCredit = $suppliers->sum(function($supplier) {
            return $supplier->purchaseOrders->sum('total');
        });
        
        return view('reports.advanced.supplier-credit', compact('suppliers', 'totalCredit'));
    }

    public function supplierCreditPDF(Request $request)
    {
        return $this->generatePDF('reports.advanced.supplier-credit-pdf', $this->supplierCredit($request)->getData(), 'supplier-credit-report.pdf');
    }
}
