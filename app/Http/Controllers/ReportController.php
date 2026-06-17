<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Sale;
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
use Carbon\Carbon;

class ReportController extends Controller
{
    // ==================== Sales Reports ====================
    public function dailySales()
    {
        return view('reports.sales.daily');
    }

    public function salesByDate()
    {
        return view('reports.sales.by-date');
    }

    public function hourlySales()
    {
        return view('reports.sales.hourly');
    }

    public function salesByProduct()
    {
        return view('reports.sales.by-product');
    }

    public function salesByCategory()
    {
        return view('reports.sales.by-category');
    }

    public function salesByBrand()
    {
        return view('reports.sales.by-brand');
    }

    public function topSelling()
    {
        return view('reports.sales.top-selling');
    }

    public function worstSelling()
    {
        return view('reports.sales.worst-selling');
    }

    // ==================== Profit Reports ====================
    public function grossProfit()
    {
        return view('reports.profit.gross');
    }

    public function profitMargin()
    {
        return view('reports.profit.margin');
    }

    public function profitByCategory()
    {
        return view('reports.profit.by-category');
    }

    public function netProfit()
    {
        return view('reports.profit.net');
    }

    public function lossReport()
    {
        return view('reports.profit.loss');
    }

    // ==================== Inventory Reports ====================
    public function currentStock()
    {
        return view('reports.inventory.current-stock');
    }

    public function inventoryValuation()
    {
        return view('reports.inventory.valuation');
    }

    public function stockMovement()
    {
        return view('reports.inventory.movement');
    }

    public function stockIn()
    {
        return view('reports.inventory.stock-in');
    }

    public function stockOut()
    {
        return view('reports.inventory.stock-out');
    }

    public function stockTransfers()
    {
        return view('reports.inventory.transfers');
    }

    public function lowStock()
    {
        return view('reports.inventory.low-stock');
    }

    public function outOfStock()
    {
        return view('reports.inventory.out-of-stock');
    }

    public function overstock()
    {
        return view('reports.inventory.overstock');
    }

    public function fastMoving()
    {
        return view('reports.inventory.fast-moving');
    }

    public function slowMoving()
    {
        return view('reports.inventory.slow-moving');
    }

    public function deadStock()
    {
        return view('reports.inventory.dead-stock');
    }

    // ==================== Expiry Reports ====================
    public function expiringSoon()
    {
        return view('reports.expiry.soon');
    }

    public function expiredProducts()
    {
        return view('reports.expiry.expired');
    }

    public function batchTracking()
    {
        return view('reports.expiry.batch-tracking');
    }

    // ==================== Purchasing Reports ====================
    public function purchaseSummary()
    {
        return view('reports.purchasing.summary');
    }

    public function purchaseBySupplier()
    {
        return view('reports.purchasing.by-supplier');
    }

    public function supplierPerformance()
    {
        return view('reports.purchasing.supplier-performance');
    }

    public function purchaseVsSales()
    {
        return view('reports.purchasing.vs-sales');
    }

    public function purchaseOrders()
    {
        return view('reports.purchasing.purchase-orders');
    }

    // ==================== Cash & Payment Reports ====================
    public function cashierShift()
    {
        return view('reports.cash.cashier-shift');
    }

    public function cashReconciliation()
    {
        return view('reports.cash.reconciliation');
    }

    public function paymentMethod()
    {
        return view('reports.cash.payment-method');
    }

    public function dailyCashFlow()
    {
        return view('reports.cash.daily-flow');
    }

    // ==================== Cashier / Staff Reports ====================
    public function salesByCashier()
    {
        return view('reports.staff.sales-by-cashier');
    }

    public function transactionCount()
    {
        return view('reports.staff.transaction-count');
    }

    public function cashierActivity()
    {
        return view('reports.staff.activity');
    }

    public function discountReport()
    {
        return view('reports.staff.discounts');
    }

    public function voidTransactions()
    {
        return view('reports.staff.void-transactions');
    }

    public function refundReport()
    {
        return view('reports.staff.refunds');
    }

    // ==================== Customer Reports ====================
    public function customerSales()
    {
        return view('reports.customer.sales');
    }

    public function customerPurchaseHistory()
    {
        return view('reports.customer.purchase-history');
    }

    public function loyaltyReport()
    {
        return view('reports.customer.loyalty');
    }

    // ==================== Security & Audit Reports ====================
    public function auditLog()
    {
        return view('reports.security.audit-log');
    }

    public function priceChanges()
    {
        return view('reports.security.price-changes');
    }

    public function inventoryAdjustments()
    {
        return view('reports.security.inventory-adjustments');
    }

    public function userActivity()
    {
        return view('reports.security.user-activity');
    }

    // ==================== Management Dashboard Reports ====================
    public function executiveDashboard()
    {
        return view('reports.management.executive');
    }

    public function inventoryInvestment()
    {
        return view('reports.management.inventory-investment');
    }

    public function inventoryTurnover()
    {
        return view('reports.management.inventory-turnover');
    }

    public function stockAccuracy()
    {
        return view('reports.management.stock-accuracy');
    }

    public function businessGrowth()
    {
        return view('reports.management.business-growth');
    }

    // ==================== FeedTan Store Advanced Reports ====================
    public function branchComparison()
    {
        return view('reports.advanced.branch-comparison');
    }

    public function branchProfit()
    {
        return view('reports.advanced.branch-profit');
    }

    public function expansionReadiness()
    {
        return view('reports.advanced.expansion-readiness');
    }

    public function memberPurchase()
    {
        return view('reports.advanced.member-purchase');
    }

    public function supplierCredit()
    {
        return view('reports.advanced.supplier-credit');
    }
}
