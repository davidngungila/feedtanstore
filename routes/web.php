<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Auth Routes
Route::get('/entry', [AuthController::class, 'redirectEntry'])->name('entry');
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::redirect('/admin', '/');
Route::get('/', [\App\Http\Controllers\OnlineOrderController::class, 'shop'])->name('home');

// Public verify route
Route::get('/sales/receipts/{sale}/verify', [\App\Http\Controllers\ReceiptController::class, 'verify'])->name('sales.receipts.verify');
Route::get('/sales/receipts/{sale}/download', [\App\Http\Controllers\ReceiptController::class, 'download'])->name('sales.receipts.download');

// Public Shop Routes
Route::get('/shop', [\App\Http\Controllers\OnlineOrderController::class, 'shop'])->name('shop.index');
Route::get('/shop/product/{product}', [\App\Http\Controllers\OnlineOrderController::class, 'showProduct'])->name('shop.product');
Route::get('/shop/checkout', function () {
    return view('shop.checkout');
})->name('shop.checkout');
Route::get('/shop/tracking', [\App\Http\Controllers\OnlineOrderController::class, 'showTracking'])->name('shop.tracking');
Route::get('/shop/tracking/{orderNumber}', [\App\Http\Controllers\OnlineOrderController::class, 'showTracking'])->name('shop.tracking.show');
Route::get('/shop/tracking/{orderNumber}/pdf', [\App\Http\Controllers\OnlineOrderController::class, 'downloadTrackingPDF'])->name('shop.tracking.pdf');
Route::post('/api/shop/orders', [\App\Http\Controllers\OnlineOrderController::class, 'placeOrder']);
Route::get('/api/shop/orders/{orderNumber}/payment-status', [\App\Http\Controllers\OnlineOrderController::class, 'checkPaymentStatus'])->name('shop.payment-status');
Route::post('/api/shop/orders/{orderNumber}/initiate-payment', [\App\Http\Controllers\OnlineOrderController::class, 'initiatePaymentForOrder'])->name('shop.payment-initiate');

// Protected Routes (must be authenticated)
Route::middleware('auth')->group(function () {
    // Messaging Test
    Route::get('/messaging/test', [App\Http\Controllers\MessagingTestController::class, 'index'])->name('messaging.test');
    Route::post('/messaging/test/send', [App\Http\Controllers\MessagingTestController::class, 'sendTestMessage'])->name('messaging.test.send');

    // Dashboards
    Route::get('/cashier', [\App\Http\Controllers\CashierController::class, 'index'])->name('cashier.dashboard');
    Route::get('/cashier/product/{barcode}', [\App\Http\Controllers\CashierController::class, 'getProductByBarcode'])->name('cashier.product');
    Route::get('/cashier/search', [\App\Http\Controllers\CashierController::class, 'searchProducts'])->name('cashier.search');
    Route::get('/cashier/dashboard-data', [\App\Http\Controllers\CashierController::class, 'getDashboardData'])->name('cashier.dashboard-data');
    Route::post('/cashier/sale', [\App\Http\Controllers\CashierController::class, 'completeSale'])->name('cashier.sale');
    
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/sales', [\App\Http\Controllers\SalesDashboardController::class, 'index'])->name('dashboard.sales');
    Route::get('/dashboard/online-orders', [\App\Http\Controllers\OnlineOrdersDashboardController::class, 'index'])->name('dashboard.online-orders');
    Route::get('/dashboard/purchases', [\App\Http\Controllers\PurchasesDashboardController::class, 'index'])->name('dashboard.purchases');
    Route::get('/dashboard/inventory', [\App\Http\Controllers\InventoryDashboardController::class, 'index'])->name('dashboard.inventory');
    
    // Profile
    Route::get('/profile', [\App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [\App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.password');

    // Analytics
    Route::get('/analytics', function () {
        return view('analytics');
    })->name('analytics');

    // Sales Management
    Route::prefix('sales')->name('sales.')->group(function () {
        Route::get('/new', [\App\Http\Controllers\SaleController::class, 'create'])->name('new');
        Route::post('/new', [\App\Http\Controllers\SaleController::class, 'store'])->name('store');
        Route::get('/history', [\App\Http\Controllers\SaleController::class, 'index'])->name('history');
        Route::get('/history/{sale}', [\App\Http\Controllers\SaleController::class, 'show'])->name('show');
        Route::delete('/history/{sale}', [\App\Http\Controllers\SaleController::class, 'destroy'])->name('destroy');
        Route::post('/history/{id}/restore', [\App\Http\Controllers\SaleController::class, 'restore'])->name('restore');
        Route::get('/returns', [\App\Http\Controllers\SaleReturnController::class, 'index'])->name('returns');
        Route::get('/returns/{return}', [\App\Http\Controllers\SaleReturnController::class, 'show'])->name('returns.show');
        Route::get('/returns/{return}/download', [\App\Http\Controllers\SaleReturnController::class, 'downloadPDF'])->name('returns.download');
        Route::post('/returns', [\App\Http\Controllers\SaleReturnController::class, 'store'])->name('returns.store');
        Route::get('/cancelled', [\App\Http\Controllers\CancelledSaleController::class, 'index'])->name('cancelled');
        Route::get('/discounts', [\App\Http\Controllers\DiscountController::class, 'index'])->name('discounts');
        Route::get('/discounts/create', [\App\Http\Controllers\DiscountController::class, 'create'])->name('discounts.create');
        Route::post('/discounts', [\App\Http\Controllers\DiscountController::class, 'store'])->name('discounts.store');
        Route::get('/discounts/{discount}/edit', [\App\Http\Controllers\DiscountController::class, 'edit'])->name('discounts.edit');
        Route::put('/discounts/{discount}', [\App\Http\Controllers\DiscountController::class, 'update'])->name('discounts.update');
        Route::post('/discounts/{discount}/toggle', [\App\Http\Controllers\DiscountController::class, 'toggleActive'])->name('discounts.toggle');
        Route::delete('/discounts/{discount}', [\App\Http\Controllers\DiscountController::class, 'destroy'])->name('discounts.destroy');

        Route::get('/receipts', [\App\Http\Controllers\ReceiptController::class, 'index'])->name('receipts');
        Route::get('/receipts/{sale}', [\App\Http\Controllers\ReceiptController::class, 'show'])->name('receipts.show');
        Route::get('/receipts/{sale}/print', [\App\Http\Controllers\ReceiptController::class, 'print'])->name('receipts.print');
        Route::get('/shifts', [\App\Http\Controllers\ShiftController::class, 'index'])->name('shifts');
        Route::post('/shifts/open', [\App\Http\Controllers\ShiftController::class, 'open'])->name('shifts.open');
        Route::post('/shifts/{shift}/close', [\App\Http\Controllers\ShiftController::class, 'close'])->name('shifts.close');
    });

    // Inventory Management
    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/products', [\App\Http\Controllers\ProductController::class, 'index'])->name('products');
        Route::get('/products/create', [\App\Http\Controllers\ProductController::class, 'create'])->name('products.create');
        Route::post('/products', [\App\Http\Controllers\ProductController::class, 'store'])->name('products.store');
        Route::get('/products/{product}', [\App\Http\Controllers\ProductController::class, 'show'])->name('products.show');
        Route::get('/products/{product}/edit', [\App\Http\Controllers\ProductController::class, 'edit'])->name('products.edit');
        Route::put('/products/{product}', [\App\Http\Controllers\ProductController::class, 'update'])->name('products.update');
        Route::delete('/products/{product}', [\App\Http\Controllers\ProductController::class, 'destroy'])->name('products.destroy');
        
        Route::get('/categories', [\App\Http\Controllers\CategoryController::class, 'index'])->name('categories');
        Route::get('/categories/create', [\App\Http\Controllers\CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [\App\Http\Controllers\CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}', [\App\Http\Controllers\CategoryController::class, 'show'])->name('categories.show');
        Route::get('/categories/{category}/edit', [\App\Http\Controllers\CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [\App\Http\Controllers\CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [\App\Http\Controllers\CategoryController::class, 'destroy'])->name('categories.destroy');
        
        Route::get('/brands', [\App\Http\Controllers\BrandController::class, 'index'])->name('brands');
        Route::get('/brands/create', [\App\Http\Controllers\BrandController::class, 'create'])->name('brands.create');
        Route::post('/brands', [\App\Http\Controllers\BrandController::class, 'store'])->name('brands.store');
        Route::get('/brands/{brand}', [\App\Http\Controllers\BrandController::class, 'show'])->name('brands.show');
        Route::get('/brands/{brand}/edit', [\App\Http\Controllers\BrandController::class, 'edit'])->name('brands.edit');
        Route::put('/brands/{brand}', [\App\Http\Controllers\BrandController::class, 'update'])->name('brands.update');
        Route::delete('/brands/{brand}', [\App\Http\Controllers\BrandController::class, 'destroy'])->name('brands.destroy');
        
        Route::get('/units', [\App\Http\Controllers\UnitController::class, 'index'])->name('units');
        Route::get('/units/create', [\App\Http\Controllers\UnitController::class, 'create'])->name('units.create');
        Route::post('/units', [\App\Http\Controllers\UnitController::class, 'store'])->name('units.store');
        Route::get('/units/{unit}', [\App\Http\Controllers\UnitController::class, 'show'])->name('units.show');
        Route::get('/units/{unit}/edit', [\App\Http\Controllers\UnitController::class, 'edit'])->name('units.edit');
        Route::put('/units/{unit}', [\App\Http\Controllers\UnitController::class, 'update'])->name('units.update');
        Route::delete('/units/{unit}', [\App\Http\Controllers\UnitController::class, 'destroy'])->name('units.destroy');
        
        Route::get('/receiving', [\App\Http\Controllers\GoodsReceivedNoteController::class, 'index'])->name('receiving');
        Route::get('/receiving/create', [\App\Http\Controllers\GoodsReceivedNoteController::class, 'create'])->name('receiving.create');
        Route::post('/receiving', [\App\Http\Controllers\GoodsReceivedNoteController::class, 'store'])->name('receiving.store');
        Route::get('/adjustments', [\App\Http\Controllers\StockAdjustmentController::class, 'index'])->name('adjustments');
        Route::get('/adjustments/create', [\App\Http\Controllers\StockAdjustmentController::class, 'create'])->name('adjustments.create');
        Route::post('/adjustments', [\App\Http\Controllers\StockAdjustmentController::class, 'store'])->name('adjustments.store');
        Route::get('/transfers', [\App\Http\Controllers\StockTransferController::class, 'index'])->name('transfers');
        Route::get('/transfers/create', [\App\Http\Controllers\StockTransferController::class, 'create'])->name('transfers.create');
        Route::post('/transfers', [\App\Http\Controllers\StockTransferController::class, 'store'])->name('transfers.store');
        Route::get('/transfers/{id}', [\App\Http\Controllers\StockTransferController::class, 'show'])->name('transfers.show');
        Route::get('/transfers/{id}/edit', [\App\Http\Controllers\StockTransferController::class, 'edit'])->name('transfers.edit');
        Route::put('/transfers/{id}', [\App\Http\Controllers\StockTransferController::class, 'update'])->name('transfers.update');
        Route::delete('/transfers/{id}', [\App\Http\Controllers\StockTransferController::class, 'destroy'])->name('transfers.destroy');
        Route::get('/count', [\App\Http\Controllers\StockCountController::class, 'index'])->name('count');
        Route::get('/count/create', [\App\Http\Controllers\StockCountController::class, 'create'])->name('count.create');
        Route::post('/count', [\App\Http\Controllers\StockCountController::class, 'store'])->name('count.store');
        Route::get('/low-stock', [\App\Http\Controllers\ProductController::class, 'lowStock'])->name('low-stock');
        Route::get('/expiry', [\App\Http\Controllers\ProductController::class, 'expiry'])->name('expiry');
        Route::get('/damaged', [\App\Http\Controllers\DamagedGoodController::class, 'index'])->name('damaged');
        Route::get('/reports', [\App\Http\Controllers\ProductController::class, 'reports'])->name('reports');
        Route::get('/barcodes', [\App\Http\Controllers\ProductController::class, 'barcodes'])->name('barcodes');
        Route::post('/barcodes/print', [\App\Http\Controllers\ProductController::class, 'printBarcodes'])->name('barcodes.print');
        Route::post('/barcodes/print-all', [\App\Http\Controllers\ProductController::class, 'printAllBarcodes'])->name('barcodes.print-all');
    });

    // Purchasing & Suppliers
    Route::prefix('purchasing')->name('purchasing.')->group(function () {
        Route::get('/suppliers', [\App\Http\Controllers\SupplierController::class, 'index'])->name('suppliers');
        Route::get('/suppliers/create', [\App\Http\Controllers\SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('/suppliers', [\App\Http\Controllers\SupplierController::class, 'store'])->name('suppliers.store');
        Route::get('/suppliers/{supplier}', [\App\Http\Controllers\SupplierController::class, 'show'])->name('suppliers.show');
        Route::get('/suppliers/{supplier}/edit', [\App\Http\Controllers\SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('/suppliers/{supplier}', [\App\Http\Controllers\SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('/suppliers/{supplier}', [\App\Http\Controllers\SupplierController::class, 'destroy'])->name('suppliers.destroy');
        Route::get('/orders', [\App\Http\Controllers\PurchaseOrderController::class, 'index'])->name('orders');
        Route::get('/orders/create', [\App\Http\Controllers\PurchaseOrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [\App\Http\Controllers\PurchaseOrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{purchaseOrder}', [\App\Http\Controllers\PurchaseOrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{purchaseOrder}/download', [\App\Http\Controllers\PurchaseOrderController::class, 'downloadPDF'])->name('orders.download');
        Route::get('/orders/{purchaseOrder}/edit', [\App\Http\Controllers\PurchaseOrderController::class, 'edit'])->name('orders.edit');
        Route::put('/orders/{purchaseOrder}', [\App\Http\Controllers\PurchaseOrderController::class, 'update'])->name('orders.update');
        Route::delete('/orders/{purchaseOrder}', [\App\Http\Controllers\PurchaseOrderController::class, 'destroy'])->name('orders.destroy');
        Route::get('/orders/{purchaseOrder}/review', [\App\Http\Controllers\PurchaseOrderController::class, 'review'])->name('orders.review');
        Route::post('/orders/{purchaseOrder}/review-approve', [\App\Http\Controllers\PurchaseOrderController::class, 'reviewApprove'])->name('orders.review.approve');
        Route::post('/orders/{purchaseOrder}/review-reject', [\App\Http\Controllers\PurchaseOrderController::class, 'reviewReject'])->name('orders.review.reject');
        Route::post('/orders/{purchaseOrder}/send', [\App\Http\Controllers\PurchaseOrderController::class, 'send'])->name('orders.send');
        Route::get('/grn', [\App\Http\Controllers\GoodsReceivedNoteController::class, 'index'])->name('grn');
        Route::get('/grn/create', [\App\Http\Controllers\GoodsReceivedNoteController::class, 'create'])->name('grn.create');
        Route::post('/grn', [\App\Http\Controllers\GoodsReceivedNoteController::class, 'store'])->name('grn.store');
        Route::get('/grn/{grn}', [\App\Http\Controllers\GoodsReceivedNoteController::class, 'show'])->name('grn.show');
        Route::get('/grn/{grn}/download', [\App\Http\Controllers\GoodsReceivedNoteController::class, 'downloadPDF'])->name('grn.download');
        Route::get('/grn/{grn}/edit', [\App\Http\Controllers\GoodsReceivedNoteController::class, 'edit'])->name('grn.edit');
        Route::put('/grn/{grn}', [\App\Http\Controllers\GoodsReceivedNoteController::class, 'update'])->name('grn.update');
        Route::delete('/grn/{grn}', [\App\Http\Controllers\GoodsReceivedNoteController::class, 'destroy'])->name('grn.destroy');
        Route::get('/payments', [\App\Http\Controllers\SupplierPaymentController::class, 'index'])->name('payments');
        Route::get('/payments/create', [\App\Http\Controllers\SupplierPaymentController::class, 'create'])->name('payments.create');
        Route::post('/payments', [\App\Http\Controllers\SupplierPaymentController::class, 'store'])->name('payments.store');
        Route::get('/payments/{payment}', [\App\Http\Controllers\SupplierPaymentController::class, 'show'])->name('payments.show');
        Route::get('/payments/{payment}/download', [\App\Http\Controllers\SupplierPaymentController::class, 'downloadPDF'])->name('payments.download');
        Route::get('/payments/{payment}/edit', [\App\Http\Controllers\SupplierPaymentController::class, 'edit'])->name('payments.edit');
        Route::put('/payments/{payment}', [\App\Http\Controllers\SupplierPaymentController::class, 'update'])->name('payments.update');
        Route::delete('/payments/{payment}', [\App\Http\Controllers\SupplierPaymentController::class, 'destroy'])->name('payments.destroy');
        Route::get('/reports', [\App\Http\Controllers\PurchaseReportController::class, 'index'])->name('reports');
    });

    // Customers
    Route::prefix('customers')->name('customers.')->group(function () {
        Route::get('/list', [\App\Http\Controllers\CustomerController::class, 'index'])->name('list');
        Route::get('/create', [\App\Http\Controllers\CustomerController::class, 'create'])->name('create');
        Route::post('/', [\App\Http\Controllers\CustomerController::class, 'store'])->name('store');
        
        Route::prefix('groups')->name('groups.')->group(function () {
            Route::get('/', [\App\Http\Controllers\CustomerGroupController::class, 'index'])->name('index');
            Route::get('/create', [\App\Http\Controllers\CustomerGroupController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\CustomerGroupController::class, 'store'])->name('store');
            Route::get('/{group}/edit', [\App\Http\Controllers\CustomerGroupController::class, 'edit'])->name('edit');
            Route::put('/{group}', [\App\Http\Controllers\CustomerGroupController::class, 'update'])->name('update');
            Route::delete('/{group}', [\App\Http\Controllers\CustomerGroupController::class, 'destroy'])->name('destroy');
        });
        
        Route::get('/groups', [\App\Http\Controllers\CustomerGroupController::class, 'index'])->name('groups');
        Route::get('/loyalty', [\App\Http\Controllers\CustomerController::class, 'loyalty'])->name('loyalty');
        Route::get('/credit', [\App\Http\Controllers\CustomerController::class, 'credit'])->name('credit');
        Route::get('/history', [\App\Http\Controllers\CustomerController::class, 'history'])->name('history');
        
        Route::get('/{customer}', [\App\Http\Controllers\CustomerController::class, 'show'])->name('show');
        Route::get('/{customer}/edit', [\App\Http\Controllers\CustomerController::class, 'edit'])->name('edit');
        Route::put('/{customer}', [\App\Http\Controllers\CustomerController::class, 'update'])->name('update');
        Route::delete('/{customer}', [\App\Http\Controllers\CustomerController::class, 'destroy'])->name('destroy');
        Route::post('/{customer}/payments', [\App\Http\Controllers\CustomerController::class, 'addPayment'])->name('add-payment');
        Route::post('/{customer}/loyalty', [\App\Http\Controllers\CustomerController::class, 'addLoyaltyPoints'])->name('add-loyalty');
    });

    // Finance
    Route::prefix('finance')->name('finance.')->group(function () {
        Route::get('/', [\App\Http\Controllers\FinanceController::class, 'dashboard'])->name('dashboard');
        Route::get('/payments', function () { return view('finance.payments'); })->name('payments');
        
        // Journal Entries
        Route::get('/journal-entries', [\App\Http\Controllers\JournalEntryController::class, 'index'])->name('journal-entries');
        Route::get('/journal-entries/create', [\App\Http\Controllers\JournalEntryController::class, 'create'])->name('journal-entries.create');
        Route::post('/journal-entries', [\App\Http\Controllers\JournalEntryController::class, 'store'])->name('journal-entries.store');
        Route::get('/journal-entries/{journalEntry}', [\App\Http\Controllers\JournalEntryController::class, 'show'])->name('journal-entries.show');
        Route::get('/journal-entries/{journalEntry}/download', [\App\Http\Controllers\JournalEntryController::class, 'downloadPDF'])->name('journal-entries.download');
        
        // General Ledger
        Route::get('/general-ledger', [\App\Http\Controllers\FinanceController::class, 'generalLedger'])->name('general-ledger');
        
        // Expenses
        Route::get('/expenses', [\App\Http\Controllers\ExpenseController::class, 'index'])->name('expenses');
        Route::get('/expenses/create', [\App\Http\Controllers\ExpenseController::class, 'create'])->name('expenses.create');
        Route::post('/expenses', [\App\Http\Controllers\ExpenseController::class, 'store'])->name('expenses.store');
        Route::get('/expenses/{expense}', [\App\Http\Controllers\ExpenseController::class, 'show'])->name('expenses.show');
        Route::get('/expenses/{expense}/edit', [\App\Http\Controllers\ExpenseController::class, 'edit'])->name('expenses.edit');
        Route::put('/expenses/{expense}', [\App\Http\Controllers\ExpenseController::class, 'update'])->name('expenses.update');
        Route::delete('/expenses/{expense}', [\App\Http\Controllers\ExpenseController::class, 'destroy'])->name('expenses.destroy');
        
        // Income
        Route::get('/income', [\App\Http\Controllers\IncomeController::class, 'index'])->name('income');
        Route::get('/income/create', [\App\Http\Controllers\IncomeController::class, 'create'])->name('income.create');
        Route::post('/income', [\App\Http\Controllers\IncomeController::class, 'store'])->name('income.store');
        Route::get('/income/{income}', [\App\Http\Controllers\IncomeController::class, 'show'])->name('income.show');
        Route::get('/income/{income}/edit', [\App\Http\Controllers\IncomeController::class, 'edit'])->name('income.edit');
        Route::put('/income/{income}', [\App\Http\Controllers\IncomeController::class, 'update'])->name('income.update');
        Route::delete('/income/{income}', [\App\Http\Controllers\IncomeController::class, 'destroy'])->name('income.destroy');
        
        // Cash Management
        Route::get('/cash', [\App\Http\Controllers\CashManagementController::class, 'index'])->name('cash');
        Route::get('/cash/create', [\App\Http\Controllers\CashManagementController::class, 'create'])->name('cash.create');
        Route::post('/cash', [\App\Http\Controllers\CashManagementController::class, 'store'])->name('cash.store');
        
        // Bank Accounts
        Route::get('/bank', [\App\Http\Controllers\BankAccountController::class, 'index'])->name('bank');
        Route::get('/bank/create', [\App\Http\Controllers\BankAccountController::class, 'create'])->name('bank.create');
        Route::post('/bank', [\App\Http\Controllers\BankAccountController::class, 'store'])->name('bank.store');
        Route::get('/bank/{bankAccount}', [\App\Http\Controllers\BankAccountController::class, 'show'])->name('bank.show');
        Route::get('/bank/{bankAccount}/edit', [\App\Http\Controllers\BankAccountController::class, 'edit'])->name('bank.edit');
        Route::put('/bank/{bankAccount}', [\App\Http\Controllers\BankAccountController::class, 'update'])->name('bank.update');
        Route::delete('/bank/{bankAccount}', [\App\Http\Controllers\BankAccountController::class, 'destroy'])->name('bank.destroy');
        
        // Mobile Money
        Route::get('/mobile-money', [\App\Http\Controllers\MobileMoneyAccountController::class, 'index'])->name('mobile-money');
        Route::get('/mobile-money/create', [\App\Http\Controllers\MobileMoneyAccountController::class, 'create'])->name('mobile-money.create');
        Route::post('/mobile-money', [\App\Http\Controllers\MobileMoneyAccountController::class, 'store'])->name('mobile-money.store');
        Route::get('/mobile-money/{mobileMoneyAccount}', [\App\Http\Controllers\MobileMoneyAccountController::class, 'show'])->name('mobile-money.show');
        Route::get('/mobile-money/{mobileMoneyAccount}/edit', [\App\Http\Controllers\MobileMoneyAccountController::class, 'edit'])->name('mobile-money.edit');
        Route::put('/mobile-money/{mobileMoneyAccount}', [\App\Http\Controllers\MobileMoneyAccountController::class, 'update'])->name('mobile-money.update');
        Route::delete('/mobile-money/{mobileMoneyAccount}', [\App\Http\Controllers\MobileMoneyAccountController::class, 'destroy'])->name('mobile-money.destroy');
        
        // Capital Management
        Route::get('/capital', [\App\Http\Controllers\CapitalController::class, 'index'])->name('capital');
        Route::get('/capital/create', [\App\Http\Controllers\CapitalController::class, 'create'])->name('capital.create');
        Route::post('/capital', [\App\Http\Controllers\CapitalController::class, 'store'])->name('capital.store');
        Route::get('/capital/{capital}', [\App\Http\Controllers\CapitalController::class, 'show'])->name('capital.show');
        Route::get('/capital/{capital}/edit', [\App\Http\Controllers\CapitalController::class, 'edit'])->name('capital.edit');
        Route::put('/capital/{capital}', [\App\Http\Controllers\CapitalController::class, 'update'])->name('capital.update');
        Route::delete('/capital/{capital}', [\App\Http\Controllers\CapitalController::class, 'destroy'])->name('capital.destroy');

        // Shareholders Management
        Route::get('/shareholders', [\App\Http\Controllers\ShareholderController::class, 'index'])->name('shareholders');
        Route::get('/shareholders/create', [\App\Http\Controllers\ShareholderController::class, 'create'])->name('shareholders.create');
        Route::post('/shareholders', [\App\Http\Controllers\ShareholderController::class, 'store'])->name('shareholders.store');
        Route::post('/shareholders/import', [\App\Http\Controllers\ShareholderController::class, 'import'])->name('shareholders.import');
        Route::get('/shareholders/sample/download', [\App\Http\Controllers\ShareholderController::class, 'downloadSample'])->name('shareholders.sample.download');
        Route::get('/shareholders/{shareholder}', [\App\Http\Controllers\ShareholderController::class, 'show'])->name('shareholders.show');
        Route::get('/shareholders/{shareholder}/edit', [\App\Http\Controllers\ShareholderController::class, 'edit'])->name('shareholders.edit');
        Route::put('/shareholders/{shareholder}', [\App\Http\Controllers\ShareholderController::class, 'update'])->name('shareholders.update');
        Route::delete('/shareholders/{shareholder}', [\App\Http\Controllers\ShareholderController::class, 'destroy'])->name('shareholders.destroy');
        Route::get('/shareholders/{shareholder}/add-share', [\App\Http\Controllers\ShareholderController::class, 'addShare'])->name('shareholders.add-share');
        Route::post('/shareholders/{shareholder}/shares', [\App\Http\Controllers\ShareholderController::class, 'storeShare'])->name('shareholders.store-share');
        
        // Balance Sheet
        Route::get('/balance-sheet', [\App\Http\Controllers\FinanceController::class, 'balanceSheet'])->name('balance-sheet');
        // Income Statement
        Route::get('/income-statement', [\App\Http\Controllers\FinanceController::class, 'incomeStatement'])->name('income-statement');
        
        // Other Finance Pages
        Route::get('/mobile-money-reconciliation', [\App\Http\Controllers\FinanceController::class, 'mobileMoneyReconciliation'])->name('mobile-money-reconciliation');
        Route::get('/accounts-receivable', [\App\Http\Controllers\FinanceController::class, 'accountsReceivable'])->name('accounts-receivable');
        Route::get('/accounts-payable', [\App\Http\Controllers\FinanceController::class, 'accountsPayable'])->name('accounts-payable');
        Route::get('/transactions', [\App\Http\Controllers\FinanceController::class, 'transactions'])->name('transactions');
        Route::get('/transactions/{entry}', [\App\Http\Controllers\FinanceController::class, 'showTransaction'])->name('transactions.show');
        Route::get('/tax-management', [\App\Http\Controllers\FinanceController::class, 'taxManagement'])->name('tax-management');
        // Chart of Accounts
        Route::get('/chart-of-accounts', [\App\Http\Controllers\ChartOfAccountsController::class, 'index'])->name('chart-of-accounts');
        Route::get('/chart-of-accounts/create', [\App\Http\Controllers\ChartOfAccountsController::class, 'create'])->name('chart-of-accounts.create');
        Route::post('/chart-of-accounts', [\App\Http\Controllers\ChartOfAccountsController::class, 'store'])->name('chart-of-accounts.store');
        Route::get('/chart-of-accounts/{account}', [\App\Http\Controllers\ChartOfAccountsController::class, 'show'])->name('chart-of-accounts.show');
        Route::get('/chart-of-accounts/{account}/edit', [\App\Http\Controllers\ChartOfAccountsController::class, 'edit'])->name('chart-of-accounts.edit');
        Route::put('/chart-of-accounts/{account}', [\App\Http\Controllers\ChartOfAccountsController::class, 'update'])->name('chart-of-accounts.update');
        Route::delete('/chart-of-accounts/{account}', [\App\Http\Controllers\ChartOfAccountsController::class, 'destroy'])->name('chart-of-accounts.destroy');
        
        // Budgets
        Route::get('/budgets', [\App\Http\Controllers\BudgetController::class, 'index'])->name('budgets');
        Route::get('/budgets/create', [\App\Http\Controllers\BudgetController::class, 'create'])->name('budgets.create');
        Route::post('/budgets', [\App\Http\Controllers\BudgetController::class, 'store'])->name('budgets.store');
        Route::get('/budgets/{budget}', [\App\Http\Controllers\BudgetController::class, 'show'])->name('budgets.show');
        Route::get('/budgets/{budget}/edit', [\App\Http\Controllers\BudgetController::class, 'edit'])->name('budgets.edit');
        Route::put('/budgets/{budget}', [\App\Http\Controllers\BudgetController::class, 'update'])->name('budgets.update');
        Route::delete('/budgets/{budget}', [\App\Http\Controllers\BudgetController::class, 'destroy'])->name('budgets.destroy');
        
        Route::get('/assets', [\App\Http\Controllers\FinanceController::class, 'assets'])->name('assets');
        Route::get('/settings', [\App\Http\Controllers\FinanceController::class, 'settings'])->name('settings');
        
        Route::get('/reports', [\App\Http\Controllers\FinanceController::class, 'reports'])->name('reports');
    });

    // Online Sales
    Route::prefix('online')->name('online.')->group(function () {
        Route::get('/orders', [\App\Http\Controllers\OnlineOrderController::class, 'index'])->name('orders');
        Route::get('/orders/create', [\App\Http\Controllers\OnlineOrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [\App\Http\Controllers\OnlineOrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}', [\App\Http\Controllers\OnlineOrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/download', [\App\Http\Controllers\OnlineOrderController::class, 'downloadPDF'])->name('orders.download');
        Route::get('/orders/{order}/edit', [\App\Http\Controllers\OnlineOrderController::class, 'edit'])->name('orders.edit');
        Route::put('/orders/{order}', [\App\Http\Controllers\OnlineOrderController::class, 'update'])->name('orders.update');
        Route::delete('/orders/{order}', [\App\Http\Controllers\OnlineOrderController::class, 'destroy'])->name('orders.destroy');
        Route::put('/orders/{order}/status', [\App\Http\Controllers\OnlineOrderController::class, 'updateStatus'])->name('orders.status');
        Route::post('/orders/{order}/assign-rider', [\App\Http\Controllers\OnlineOrderController::class, 'assignRider'])->name('orders.assign-rider');
        
        Route::get('/catalog', [\App\Http\Controllers\ProductCatalogController::class, 'index'])->name('catalog');
        Route::get('/catalog/{product}', [\App\Http\Controllers\ProductCatalogController::class, 'show'])->name('catalog.show');
        Route::post('/catalog/{product}/toggle', [\App\Http\Controllers\ProductCatalogController::class, 'toggleOnlineStatus'])->name('catalog.toggle');
        Route::post('/catalog/{product}/images', [\App\Http\Controllers\ProductCatalogController::class, 'uploadImage'])->name('catalog.images.upload');
        Route::delete('/catalog/{product}/images/{image}', [\App\Http\Controllers\ProductCatalogController::class, 'deleteImage'])->name('catalog.images.delete');
        Route::post('/catalog/{product}/images/{image}/primary', [\App\Http\Controllers\ProductCatalogController::class, 'setPrimaryImage'])->name('catalog.images.primary');
        
        Route::get('/carousel', [\App\Http\Controllers\CarouselController::class, 'index'])->name('carousel');
        Route::get('/carousel/create', [\App\Http\Controllers\CarouselController::class, 'create'])->name('carousel.create');
        Route::post('/carousel', [\App\Http\Controllers\CarouselController::class, 'store'])->name('carousel.store');
        Route::get('/carousel/{carousel}/edit', [\App\Http\Controllers\CarouselController::class, 'edit'])->name('carousel.edit');
        Route::put('/carousel/{carousel}', [\App\Http\Controllers\CarouselController::class, 'update'])->name('carousel.update');
        Route::delete('/carousel/{carousel}', [\App\Http\Controllers\CarouselController::class, 'destroy'])->name('carousel.destroy');
        
        Route::get('/delivery', [\App\Http\Controllers\DeliveryManagementController::class, 'index'])->name('delivery');
        
        Route::get('/riders', [\App\Http\Controllers\DeliveryRiderController::class, 'index'])->name('riders');
        Route::get('/riders/create', [\App\Http\Controllers\DeliveryRiderController::class, 'create'])->name('riders.create');
        Route::post('/riders', [\App\Http\Controllers\DeliveryRiderController::class, 'store'])->name('riders.store');
        Route::get('/riders/{rider}/edit', [\App\Http\Controllers\DeliveryRiderController::class, 'edit'])->name('riders.edit');
        Route::put('/riders/{rider}', [\App\Http\Controllers\DeliveryRiderController::class, 'update'])->name('riders.update');
        Route::delete('/riders/{rider}', [\App\Http\Controllers\DeliveryRiderController::class, 'destroy'])->name('riders.destroy');
        Route::post('/riders/{rider}/toggle', [\App\Http\Controllers\DeliveryRiderController::class, 'toggleActive'])->name('riders.toggle');
        
        Route::get('/payments', [\App\Http\Controllers\OnlinePaymentController::class, 'index'])->name('payments');
        
        Route::get('/tracking', [\App\Http\Controllers\OrderTrackingController::class, 'index'])->name('tracking');
        Route::get('/tracking/{orderNumber}', [\App\Http\Controllers\OrderTrackingController::class, 'show'])->name('tracking.show');
        Route::get('/delivery-map', [\App\Http\Controllers\DeliveryManagementController::class, 'map'])->name('delivery.map');
        Route::get('/customer-locations', [\App\Http\Controllers\DeliveryManagementController::class, 'customerMap'])->name('customer.locations');
        Route::get('/riders-livemap', [\App\Http\Controllers\DeliveryManagementController::class, 'ridersLiveMap'])->name('riders.livemap');
    });

    // Store Management
    Route::prefix('store')->name('store.')->group(function () {
        Route::get('/profile', [\App\Http\Controllers\StoreSettingController::class, 'index'])->name('profile');
        Route::put('/profile', [\App\Http\Controllers\StoreSettingController::class, 'update'])->name('profile.update');
        
        Route::get('/branches', [\App\Http\Controllers\BranchController::class, 'index'])->name('branches');
        Route::get('/branches/create', [\App\Http\Controllers\BranchController::class, 'create'])->name('branches.create');
        Route::post('/branches', [\App\Http\Controllers\BranchController::class, 'store'])->name('branches.store');
        Route::get('/branches/{branch}/edit', [\App\Http\Controllers\BranchController::class, 'edit'])->name('branches.edit');
        Route::put('/branches/{branch}', [\App\Http\Controllers\BranchController::class, 'update'])->name('branches.update');
        Route::delete('/branches/{branch}', [\App\Http\Controllers\BranchController::class, 'destroy'])->name('branches.destroy');
        
        Route::get('/locations', [\App\Http\Controllers\LocationController::class, 'index'])->name('locations');
        Route::get('/locations/create', [\App\Http\Controllers\LocationController::class, 'create'])->name('locations.create');
        Route::post('/locations', [\App\Http\Controllers\LocationController::class, 'store'])->name('locations.store');
        Route::get('/locations/{location}/edit', [\App\Http\Controllers\LocationController::class, 'edit'])->name('locations.edit');
        Route::put('/locations/{location}', [\App\Http\Controllers\LocationController::class, 'update'])->name('locations.update');
        Route::delete('/locations/{location}', [\App\Http\Controllers\LocationController::class, 'destroy'])->name('locations.destroy');
        
        Route::get('/warehouses', [\App\Http\Controllers\LocationController::class, 'index'])->name('warehouses');
        
        Route::get('/settings', [\App\Http\Controllers\StoreSettingController::class, 'settingsPage'])->name('settings');
        Route::put('/settings', [\App\Http\Controllers\StoreSettingController::class, 'update'])->name('settings.update');
    });

    // HR
    Route::prefix('hr')->name('hr.')->group(function () {
        // Employees
        Route::get('/employees', [\App\Http\Controllers\HRController::class, 'employees'])->name('employees');
        Route::get('/employees/create', [\App\Http\Controllers\HRController::class, 'createEmployee'])->name('employees.create');
        Route::post('/employees', [\App\Http\Controllers\HRController::class, 'storeEmployee'])->name('employees.store');
        Route::get('/employees/{id}/edit', [\App\Http\Controllers\HRController::class, 'editEmployee'])->name('employees.edit');
        Route::put('/employees/{id}', [\App\Http\Controllers\HRController::class, 'updateEmployee'])->name('employees.update');
        Route::delete('/employees/{id}', [\App\Http\Controllers\HRController::class, 'deleteEmployee'])->name('employees.delete');

        // Roles & Permissions
        Route::get('/roles', [\App\Http\Controllers\HRController::class, 'roles'])->name('roles');

        // Attendance
        Route::get('/attendance', [\App\Http\Controllers\HRController::class, 'attendance'])->name('attendance');
        Route::post('/attendance/check-in', [\App\Http\Controllers\HRController::class, 'checkIn'])->name('attendance.check-in');
        Route::post('/attendance/check-out', [\App\Http\Controllers\HRController::class, 'checkOut'])->name('attendance.check-out');

        // Work Shifts
        Route::get('/shifts', [\App\Http\Controllers\HRController::class, 'shifts'])->name('shifts');
        Route::get('/shifts/create', [\App\Http\Controllers\HRController::class, 'createShift'])->name('shifts.create');
        Route::post('/shifts', [\App\Http\Controllers\HRController::class, 'storeShift'])->name('shifts.store');
        Route::get('/shifts/{id}/edit', [\App\Http\Controllers\HRController::class, 'editShift'])->name('shifts.edit');
        Route::put('/shifts/{id}', [\App\Http\Controllers\HRController::class, 'updateShift'])->name('shifts.update');
        Route::post('/shifts/{id}/toggle', [\App\Http\Controllers\HRController::class, 'toggleShift'])->name('shifts.toggle');
        Route::delete('/shifts/{id}', [\App\Http\Controllers\HRController::class, 'deleteShift'])->name('shifts.delete');

        // Activity Logs
        Route::get('/activity', [\App\Http\Controllers\HRController::class, 'activity'])->name('activity');
    });

    // Reports
    Route::prefix('reports')->name('reports.')->group(function () {
        // Sales Reports
        Route::prefix('sales')->name('sales.')->group(function () {
            Route::get('/daily', [\App\Http\Controllers\ReportController::class, 'dailySales'])->name('daily');
            Route::get('/daily/download', [\App\Http\Controllers\ReportController::class, 'dailySalesPDF'])->name('daily.download');
            Route::get('/by-date', [\App\Http\Controllers\ReportController::class, 'salesByDate'])->name('by-date');
            Route::get('/by-date/download', [\App\Http\Controllers\ReportController::class, 'salesByDatePDF'])->name('by-date.download');
            Route::get('/hourly', [\App\Http\Controllers\ReportController::class, 'hourlySales'])->name('hourly');
            Route::get('/hourly/download', [\App\Http\Controllers\ReportController::class, 'hourlySalesPDF'])->name('hourly.download');
            Route::get('/by-product', [\App\Http\Controllers\ReportController::class, 'salesByProduct'])->name('by-product');
            Route::get('/by-product/download', [\App\Http\Controllers\ReportController::class, 'salesByProductPDF'])->name('by-product.download');
            Route::get('/by-category', [\App\Http\Controllers\ReportController::class, 'salesByCategory'])->name('by-category');
            Route::get('/by-category/download', [\App\Http\Controllers\ReportController::class, 'salesByCategoryPDF'])->name('by-category.download');
            Route::get('/by-brand', [\App\Http\Controllers\ReportController::class, 'salesByBrand'])->name('by-brand');
            Route::get('/by-brand/download', [\App\Http\Controllers\ReportController::class, 'salesByBrandPDF'])->name('by-brand.download');
            Route::get('/top-selling', [\App\Http\Controllers\ReportController::class, 'topSelling'])->name('top-selling');
            Route::get('/top-selling/download', [\App\Http\Controllers\ReportController::class, 'topSellingPDF'])->name('top-selling.download');
            Route::get('/worst-selling', [\App\Http\Controllers\ReportController::class, 'worstSelling'])->name('worst-selling');
            Route::get('/worst-selling/download', [\App\Http\Controllers\ReportController::class, 'worstSellingPDF'])->name('worst-selling.download');
        });

        // Profit Reports
        Route::prefix('profit')->name('profit.')->group(function () {
            Route::get('/gross', [\App\Http\Controllers\ReportController::class, 'grossProfit'])->name('gross');
            Route::get('/gross/download', [\App\Http\Controllers\ReportController::class, 'grossProfitPDF'])->name('gross.download');
            Route::get('/margin', [\App\Http\Controllers\ReportController::class, 'profitMargin'])->name('margin');
            Route::get('/margin/download', [\App\Http\Controllers\ReportController::class, 'profitMarginPDF'])->name('margin.download');
            Route::get('/by-category', [\App\Http\Controllers\ReportController::class, 'profitByCategory'])->name('by-category');
            Route::get('/by-category/download', [\App\Http\Controllers\ReportController::class, 'profitByCategoryPDF'])->name('by-category.download');
            Route::get('/net', [\App\Http\Controllers\ReportController::class, 'netProfit'])->name('net');
            Route::get('/net/download', [\App\Http\Controllers\ReportController::class, 'netProfitPDF'])->name('net.download');
            Route::get('/loss', [\App\Http\Controllers\ReportController::class, 'lossReport'])->name('loss');
            Route::get('/loss/download', [\App\Http\Controllers\ReportController::class, 'lossReportPDF'])->name('loss.download');
        });

        // Inventory Reports
        Route::prefix('inventory')->name('inventory.')->group(function () {
            Route::get('/current-stock', [\App\Http\Controllers\ReportController::class, 'currentStock'])->name('current-stock');
            Route::get('/current-stock/download', [\App\Http\Controllers\ReportController::class, 'currentStockPDF'])->name('current-stock.download');
            Route::get('/valuation', [\App\Http\Controllers\ReportController::class, 'inventoryValuation'])->name('valuation');
            Route::get('/valuation/download', [\App\Http\Controllers\ReportController::class, 'inventoryValuationPDF'])->name('valuation.download');
            Route::get('/movement', [\App\Http\Controllers\ReportController::class, 'stockMovement'])->name('movement');
            Route::get('/movement/download', [\App\Http\Controllers\ReportController::class, 'stockMovementPDF'])->name('movement.download');
            Route::get('/stock-in', [\App\Http\Controllers\ReportController::class, 'stockIn'])->name('stock-in');
            Route::get('/stock-in/download', [\App\Http\Controllers\ReportController::class, 'stockInPDF'])->name('stock-in.download');
            Route::get('/stock-out', [\App\Http\Controllers\ReportController::class, 'stockOut'])->name('stock-out');
            Route::get('/stock-out/download', [\App\Http\Controllers\ReportController::class, 'stockOutPDF'])->name('stock-out.download');
            Route::get('/transfers', [\App\Http\Controllers\ReportController::class, 'stockTransfers'])->name('transfers');
            Route::get('/transfers/download', [\App\Http\Controllers\ReportController::class, 'stockTransfersPDF'])->name('transfers.download');
            Route::get('/low-stock', [\App\Http\Controllers\ReportController::class, 'lowStock'])->name('low-stock');
            Route::get('/low-stock/download', [\App\Http\Controllers\ReportController::class, 'lowStockPDF'])->name('low-stock.download');
            Route::get('/out-of-stock', [\App\Http\Controllers\ReportController::class, 'outOfStock'])->name('out-of-stock');
            Route::get('/out-of-stock/download', [\App\Http\Controllers\ReportController::class, 'outOfStockPDF'])->name('out-of-stock.download');
            Route::get('/overstock', [\App\Http\Controllers\ReportController::class, 'overstock'])->name('overstock');
            Route::get('/overstock/download', [\App\Http\Controllers\ReportController::class, 'overstockPDF'])->name('overstock.download');
            Route::get('/fast-moving', [\App\Http\Controllers\ReportController::class, 'fastMoving'])->name('fast-moving');
            Route::get('/fast-moving/download', [\App\Http\Controllers\ReportController::class, 'fastMovingPDF'])->name('fast-moving.download');
            Route::get('/slow-moving', [\App\Http\Controllers\ReportController::class, 'slowMoving'])->name('slow-moving');
            Route::get('/slow-moving/download', [\App\Http\Controllers\ReportController::class, 'slowMovingPDF'])->name('slow-moving.download');
            Route::get('/dead-stock', [\App\Http\Controllers\ReportController::class, 'deadStock'])->name('dead-stock');
            Route::get('/dead-stock/download', [\App\Http\Controllers\ReportController::class, 'deadStockPDF'])->name('dead-stock.download');
        });

        // Expiry Reports
        Route::prefix('expiry')->name('expiry.')->group(function () {
            Route::get('/soon', [\App\Http\Controllers\ReportController::class, 'expiringSoon'])->name('soon');
            Route::get('/soon/download', [\App\Http\Controllers\ReportController::class, 'expiringSoonPDF'])->name('soon.download');
            Route::get('/expired', [\App\Http\Controllers\ReportController::class, 'expiredProducts'])->name('expired');
            Route::get('/expired/download', [\App\Http\Controllers\ReportController::class, 'expiredProductsPDF'])->name('expired.download');
            Route::get('/batch-tracking', [\App\Http\Controllers\ReportController::class, 'batchTracking'])->name('batch-tracking');
            Route::get('/batch-tracking/download', [\App\Http\Controllers\ReportController::class, 'batchTrackingPDF'])->name('batch-tracking.download');
        });

        // Purchasing Reports
        Route::prefix('purchasing')->name('purchasing.')->group(function () {
            Route::get('/summary', [\App\Http\Controllers\ReportController::class, 'purchaseSummary'])->name('summary');
            Route::get('/summary/download', [\App\Http\Controllers\ReportController::class, 'purchaseSummaryPDF'])->name('summary.download');
            Route::get('/by-supplier', [\App\Http\Controllers\ReportController::class, 'purchaseBySupplier'])->name('by-supplier');
            Route::get('/by-supplier/download', [\App\Http\Controllers\ReportController::class, 'purchaseBySupplierPDF'])->name('by-supplier.download');
            Route::get('/supplier-performance', [\App\Http\Controllers\ReportController::class, 'supplierPerformance'])->name('supplier-performance');
            Route::get('/supplier-performance/download', [\App\Http\Controllers\ReportController::class, 'supplierPerformancePDF'])->name('supplier-performance.download');
            Route::get('/vs-sales', [\App\Http\Controllers\ReportController::class, 'purchaseVsSales'])->name('vs-sales');
            Route::get('/vs-sales/download', [\App\Http\Controllers\ReportController::class, 'purchaseVsSalesPDF'])->name('vs-sales.download');
            Route::get('/purchase-orders', [\App\Http\Controllers\ReportController::class, 'purchaseOrders'])->name('purchase-orders');
            Route::get('/purchase-orders/download', [\App\Http\Controllers\ReportController::class, 'purchaseOrdersPDF'])->name('purchase-orders.download');
        });

        // Cash & Payment Reports
        Route::prefix('cash')->name('cash.')->group(function () {
            Route::get('/cashier-shift', [\App\Http\Controllers\ReportController::class, 'cashierShift'])->name('cashier-shift');
            Route::get('/cashier-shift/download', [\App\Http\Controllers\ReportController::class, 'cashierShiftPDF'])->name('cashier-shift.download');
            Route::get('/reconciliation', [\App\Http\Controllers\ReportController::class, 'cashReconciliation'])->name('reconciliation');
            Route::get('/reconciliation/download', [\App\Http\Controllers\ReportController::class, 'cashReconciliationPDF'])->name('reconciliation.download');
            Route::get('/payment-method', [\App\Http\Controllers\ReportController::class, 'paymentMethod'])->name('payment-method');
            Route::get('/payment-method/download', [\App\Http\Controllers\ReportController::class, 'paymentMethodPDF'])->name('payment-method.download');
            Route::get('/daily-flow', [\App\Http\Controllers\ReportController::class, 'dailyCashFlow'])->name('daily-flow');
            Route::get('/daily-flow/download', [\App\Http\Controllers\ReportController::class, 'dailyCashFlowPDF'])->name('daily-flow.download');
        });

        // Cashier / Staff Reports
        Route::prefix('staff')->name('staff.')->group(function () {
            Route::get('/sales-by-cashier', [\App\Http\Controllers\ReportController::class, 'salesByCashier'])->name('sales-by-cashier');
            Route::get('/sales-by-cashier/download', [\App\Http\Controllers\ReportController::class, 'salesByCashierPDF'])->name('sales-by-cashier.download');
            Route::get('/transaction-count', [\App\Http\Controllers\ReportController::class, 'transactionCount'])->name('transaction-count');
            Route::get('/transaction-count/download', [\App\Http\Controllers\ReportController::class, 'transactionCountPDF'])->name('transaction-count.download');
            Route::get('/activity', [\App\Http\Controllers\ReportController::class, 'cashierActivity'])->name('activity');
            Route::get('/activity/download', [\App\Http\Controllers\ReportController::class, 'cashierActivityPDF'])->name('activity.download');
            Route::get('/discounts', [\App\Http\Controllers\ReportController::class, 'discountReport'])->name('discounts');
            Route::get('/discounts/download', [\App\Http\Controllers\ReportController::class, 'discountReportPDF'])->name('discounts.download');
            Route::get('/void-transactions', [\App\Http\Controllers\ReportController::class, 'voidTransactions'])->name('void-transactions');
            Route::get('/void-transactions/download', [\App\Http\Controllers\ReportController::class, 'voidTransactionsPDF'])->name('void-transactions.download');
            Route::get('/refunds', [\App\Http\Controllers\ReportController::class, 'refundReport'])->name('refunds');
            Route::get('/refunds/download', [\App\Http\Controllers\ReportController::class, 'refundReportPDF'])->name('refunds.download');
        });

        // Customer Reports
        Route::prefix('customer')->name('customer.')->group(function () {
            Route::get('/sales', [\App\Http\Controllers\ReportController::class, 'customerSales'])->name('sales');
            Route::get('/sales/download', [\App\Http\Controllers\ReportController::class, 'customerSalesPDF'])->name('sales.download');
            Route::get('/purchase-history', [\App\Http\Controllers\ReportController::class, 'customerPurchaseHistory'])->name('purchase-history');
            Route::get('/purchase-history/download', [\App\Http\Controllers\ReportController::class, 'customerPurchaseHistoryPDF'])->name('purchase-history.download');
            Route::get('/loyalty', [\App\Http\Controllers\ReportController::class, 'loyaltyReport'])->name('loyalty');
            Route::get('/loyalty/download', [\App\Http\Controllers\ReportController::class, 'loyaltyReportPDF'])->name('loyalty.download');
        });

        // Security & Audit Reports
        Route::prefix('security')->name('security.')->group(function () {
            Route::get('/audit-log', [\App\Http\Controllers\ReportController::class, 'auditLog'])->name('audit-log');
            Route::get('/audit-log/download', [\App\Http\Controllers\ReportController::class, 'auditLogPDF'])->name('audit-log.download');
            Route::get('/price-changes', [\App\Http\Controllers\ReportController::class, 'priceChanges'])->name('price-changes');
            Route::get('/price-changes/download', [\App\Http\Controllers\ReportController::class, 'priceChangesPDF'])->name('price-changes.download');
            Route::get('/inventory-adjustments', [\App\Http\Controllers\ReportController::class, 'inventoryAdjustments'])->name('inventory-adjustments');
            Route::get('/inventory-adjustments/download', [\App\Http\Controllers\ReportController::class, 'inventoryAdjustmentsPDF'])->name('inventory-adjustments.download');
            Route::get('/user-activity', [\App\Http\Controllers\ReportController::class, 'userActivity'])->name('user-activity');
            Route::get('/user-activity/download', [\App\Http\Controllers\ReportController::class, 'userActivityPDF'])->name('user-activity.download');
        });

        // Management Dashboard Reports
        Route::prefix('management')->name('management.')->group(function () {
            Route::get('/executive', [\App\Http\Controllers\ReportController::class, 'executiveDashboard'])->name('executive');
            Route::get('/executive/download', [\App\Http\Controllers\ReportController::class, 'executiveDashboardPDF'])->name('executive.download');
            Route::get('/inventory-investment', [\App\Http\Controllers\ReportController::class, 'inventoryInvestment'])->name('inventory-investment');
            Route::get('/inventory-investment/download', [\App\Http\Controllers\ReportController::class, 'inventoryInvestmentPDF'])->name('inventory-investment.download');
            Route::get('/inventory-turnover', [\App\Http\Controllers\ReportController::class, 'inventoryTurnover'])->name('inventory-turnover');
            Route::get('/inventory-turnover/download', [\App\Http\Controllers\ReportController::class, 'inventoryTurnoverPDF'])->name('inventory-turnover.download');
            Route::get('/stock-accuracy', [\App\Http\Controllers\ReportController::class, 'stockAccuracy'])->name('stock-accuracy');
            Route::get('/stock-accuracy/download', [\App\Http\Controllers\ReportController::class, 'stockAccuracyPDF'])->name('stock-accuracy.download');
            Route::get('/business-growth', [\App\Http\Controllers\ReportController::class, 'businessGrowth'])->name('business-growth');
            Route::get('/business-growth/download', [\App\Http\Controllers\ReportController::class, 'businessGrowthPDF'])->name('business-growth.download');
        });

        // FeedTan Store Advanced Reports
        Route::prefix('advanced')->name('advanced.')->group(function () {
            Route::get('/branch-comparison', [\App\Http\Controllers\ReportController::class, 'branchComparison'])->name('branch-comparison');
            Route::get('/branch-comparison/download', [\App\Http\Controllers\ReportController::class, 'branchComparisonPDF'])->name('branch-comparison.download');
            Route::get('/branch-profit', [\App\Http\Controllers\ReportController::class, 'branchProfit'])->name('branch-profit');
            Route::get('/branch-profit/download', [\App\Http\Controllers\ReportController::class, 'branchProfitPDF'])->name('branch-profit.download');
            Route::get('/expansion-readiness', [\App\Http\Controllers\ReportController::class, 'expansionReadiness'])->name('expansion-readiness');
            Route::get('/expansion-readiness/download', [\App\Http\Controllers\ReportController::class, 'expansionReadinessPDF'])->name('expansion-readiness.download');
            Route::get('/member-purchase', [\App\Http\Controllers\ReportController::class, 'memberPurchase'])->name('member-purchase');
            Route::get('/member-purchase/download', [\App\Http\Controllers\ReportController::class, 'memberPurchasePDF'])->name('member-purchase.download');
            Route::get('/supplier-credit', [\App\Http\Controllers\ReportController::class, 'supplierCredit'])->name('supplier-credit');
            Route::get('/supplier-credit/download', [\App\Http\Controllers\ReportController::class, 'supplierCreditPDF'])->name('supplier-credit.download');
        });
    });

    // Marketing
    Route::prefix('marketing')->name('marketing.')->group(function () {
        Route::get('/promotions', function () { return view('marketing.promotions'); })->name('promotions');
        Route::get('/discounts', function () { return view('marketing.discounts'); })->name('discounts');
        Route::get('/ads', function () { return view('marketing.ads'); })->name('ads');
        Route::get('/campaigns', function () { return view('marketing.campaigns'); })->name('campaigns');
        Route::get('/notifications', function () { return view('marketing.notifications'); })->name('notifications');
    });

    // System Administration
    Route::prefix('system')->name('system.')->group(function () {
        Route::get('/general', [\App\Http\Controllers\StoreSettingController::class, 'general'])->name('general');
        Route::get('/tax', [\App\Http\Controllers\StoreSettingController::class, 'tax'])->name('tax');
        Route::get('/receipt', [\App\Http\Controllers\StoreSettingController::class, 'receipt'])->name('receipt');
        Route::get('/barcode', [\App\Http\Controllers\StoreSettingController::class, 'barcode'])->name('barcode');
        Route::get('/communication', [\App\Http\Controllers\StoreSettingController::class, 'communication'])->name('communication');
        Route::get('/vfd', [\App\Http\Controllers\StoreSettingController::class, 'vfd'])->name('vfd');
        Route::get('/backup', [\App\Http\Controllers\StoreSettingController::class, 'backup'])->name('backup');
        Route::get('/database', [\App\Http\Controllers\StoreSettingController::class, 'database'])->name('database');
        Route::get('/logs', [\App\Http\Controllers\StoreSettingController::class, 'logs'])->name('logs');
        Route::post('/update', [\App\Http\Controllers\StoreSettingController::class, 'update'])->name('update');
        Route::post('/backup/create', [\App\Http\Controllers\StoreSettingController::class, 'createBackup'])->name('backup.create');
        Route::get('/backup/download/{filename}', [\App\Http\Controllers\StoreSettingController::class, 'downloadBackup'])->name('backup.download');
        Route::post('/logs/clear', [\App\Http\Controllers\StoreSettingController::class, 'clearLogs'])->name('logs.clear');
        
        // Communication Profiles
        Route::get('/communication-profiles', [\App\Http\Controllers\CommunicationProfileController::class, 'index'])->name('communication-profiles');
        Route::get('/communication-profiles/create', [\App\Http\Controllers\CommunicationProfileController::class, 'create'])->name('communication-profiles.create');
        Route::post('/communication-profiles', [\App\Http\Controllers\CommunicationProfileController::class, 'store'])->name('communication-profiles.store');
        Route::get('/communication-profiles/{communicationProfile}', [\App\Http\Controllers\CommunicationProfileController::class, 'show'])->name('communication-profiles.show');
        Route::get('/communication-profiles/{communicationProfile}/edit', [\App\Http\Controllers\CommunicationProfileController::class, 'edit'])->name('communication-profiles.edit');
        Route::put('/communication-profiles/{communicationProfile}', [\App\Http\Controllers\CommunicationProfileController::class, 'update'])->name('communication-profiles.update');
        Route::delete('/communication-profiles/{communicationProfile}', [\App\Http\Controllers\CommunicationProfileController::class, 'destroy'])->name('communication-profiles.destroy');
        Route::get('/communication-profiles/{communicationProfile}/test', [\App\Http\Controllers\CommunicationProfileController::class, 'test'])->name('communication-profiles.test');
        Route::post('/communication-profiles/{communicationProfile}/test', [\App\Http\Controllers\CommunicationProfileController::class, 'sendTest'])->name('communication-profiles.send-test');
    });
    
    // Security & Control
    Route::prefix('security')->name('security.')->group(function () {
        // User Accounts
        Route::get('/users', [\App\Http\Controllers\UserController::class, 'index'])->name('users');
        Route::get('/users/create', [\App\Http\Controllers\UserController::class, 'create'])->name('users.create');
        Route::post('/users', [\App\Http\Controllers\UserController::class, 'store'])->name('users.store');
        Route::get('/users/{id}', [\App\Http\Controllers\UserController::class, 'show'])->name('users.show');
        Route::get('/users/{id}/edit', [\App\Http\Controllers\UserController::class, 'edit'])->name('users.edit');
        Route::put('/users/{id}', [\App\Http\Controllers\UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{id}', [\App\Http\Controllers\UserController::class, 'destroy'])->name('users.destroy');
        
        // Security Pages
        Route::get('/access', [\App\Http\Controllers\SecurityController::class, 'access'])->name('access');
        Route::get('/audit', [\App\Http\Controllers\SecurityController::class, 'audit'])->name('audit');
        Route::get('/logins', [\App\Http\Controllers\SecurityController::class, 'logins'])->name('logins');
        Route::get('/devices', [\App\Http\Controllers\SecurityController::class, 'devices'])->name('devices');
        Route::get('/settings', [\App\Http\Controllers\SecurityController::class, 'settings'])->name('settings');
        Route::post('/devices/{id}/revoke', [\App\Http\Controllers\SecurityController::class, 'revokeDevice'])->name('devices.revoke');
    });
    
    // VFD Customer Display Routes
    Route::prefix('vfd')->name('vfd.')->group(function () {
        Route::post('/welcome', [\App\Http\Controllers\VFDController::class, 'welcome'])->name('welcome');
        Route::post('/product', [\App\Http\Controllers\VFDController::class, 'product'])->name('product');
        Route::post('/payment', [\App\Http\Controllers\VFDController::class, 'payment'])->name('payment');
        Route::post('/thank-you', [\App\Http\Controllers\VFDController::class, 'thankYou'])->name('thank-you');
    });
});

Route::get('/{entryToken}', [AuthController::class, 'showEntry'])
    ->where('entryToken', '[A-Za-z0-9\-_]{80,}')
    ->name('admin.entry');

Route::fallback(function () {
    return redirect()->route('home');
});
