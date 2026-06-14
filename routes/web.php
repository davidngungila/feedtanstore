<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// Auth Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected Routes (must be authenticated)
Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    // Dashboards
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
        Route::get('/credit', [\App\Http\Controllers\CreditSaleController::class, 'index'])->name('credit');
        Route::get('/receipts', [\App\Http\Controllers\ReceiptController::class, 'index'])->name('receipts');
        Route::get('/receipts/{sale}', [\App\Http\Controllers\ReceiptController::class, 'show'])->name('receipts.show');
        Route::get('/receipts/{sale}/download', [\App\Http\Controllers\ReceiptController::class, 'download'])->name('receipts.download');
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
        Route::get('/payments', function () { return view('finance.payments'); })->name('payments');
        
        Route::get('/expenses', [\App\Http\Controllers\ExpenseController::class, 'index'])->name('expenses');
        Route::get('/expenses/create', [\App\Http\Controllers\ExpenseController::class, 'create'])->name('expenses.create');
        Route::post('/expenses', [\App\Http\Controllers\ExpenseController::class, 'store'])->name('expenses.store');
        
        Route::get('/income', [\App\Http\Controllers\IncomeController::class, 'index'])->name('income');
        Route::get('/income/create', [\App\Http\Controllers\IncomeController::class, 'create'])->name('income.create');
        Route::post('/income', [\App\Http\Controllers\IncomeController::class, 'store'])->name('income.store');
        
        Route::get('/cash', [\App\Http\Controllers\CashManagementController::class, 'index'])->name('cash');
        Route::get('/cash/create', [\App\Http\Controllers\CashManagementController::class, 'create'])->name('cash.create');
        Route::post('/cash', [\App\Http\Controllers\CashManagementController::class, 'store'])->name('cash.store');
        
        Route::get('/bank', [\App\Http\Controllers\BankAccountController::class, 'index'])->name('bank');
        Route::get('/bank/create', [\App\Http\Controllers\BankAccountController::class, 'create'])->name('bank.create');
        Route::post('/bank', [\App\Http\Controllers\BankAccountController::class, 'store'])->name('bank.store');
        Route::get('/bank/{bankAccount}/edit', [\App\Http\Controllers\BankAccountController::class, 'edit'])->name('bank.edit');
        Route::put('/bank/{bankAccount}', [\App\Http\Controllers\BankAccountController::class, 'update'])->name('bank.update');
        Route::delete('/bank/{bankAccount}', [\App\Http\Controllers\BankAccountController::class, 'destroy'])->name('bank.destroy');
        
        Route::get('/mobile-money', [\App\Http\Controllers\MobileMoneyAccountController::class, 'index'])->name('mobile-money');
        Route::get('/mobile-money/create', [\App\Http\Controllers\MobileMoneyAccountController::class, 'create'])->name('mobile-money.create');
        Route::post('/mobile-money', [\App\Http\Controllers\MobileMoneyAccountController::class, 'store'])->name('mobile-money.store');
        Route::get('/mobile-money/{mobileMoneyAccount}/edit', [\App\Http\Controllers\MobileMoneyAccountController::class, 'edit'])->name('mobile-money.edit');
        Route::put('/mobile-money/{mobileMoneyAccount}', [\App\Http\Controllers\MobileMoneyAccountController::class, 'update'])->name('mobile-money.update');
        Route::delete('/mobile-money/{mobileMoneyAccount}', [\App\Http\Controllers\MobileMoneyAccountController::class, 'destroy'])->name('mobile-money.destroy');
        
        Route::get('/reports', [\App\Http\Controllers\FinancialReportController::class, 'index'])->name('reports');
    });

    // Online Sales
    Route::prefix('online')->name('online.')->group(function () {
        Route::get('/orders', [\App\Http\Controllers\OnlineOrderController::class, 'index'])->name('orders');
        Route::get('/orders/create', [\App\Http\Controllers\OnlineOrderController::class, 'create'])->name('orders.create');
        Route::post('/orders', [\App\Http\Controllers\OnlineOrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}', [\App\Http\Controllers\OnlineOrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/edit', [\App\Http\Controllers\OnlineOrderController::class, 'edit'])->name('orders.edit');
        Route::put('/orders/{order}', [\App\Http\Controllers\OnlineOrderController::class, 'update'])->name('orders.update');
        Route::delete('/orders/{order}', [\App\Http\Controllers\OnlineOrderController::class, 'destroy'])->name('orders.destroy');
        Route::put('/orders/{order}/status', [\App\Http\Controllers\OnlineOrderController::class, 'updateStatus'])->name('orders.status');
        Route::post('/orders/{order}/assign-rider', [\App\Http\Controllers\OnlineOrderController::class, 'assignRider'])->name('orders.assign-rider');
        
        Route::get('/catalog', [\App\Http\Controllers\ProductCatalogController::class, 'index'])->name('catalog');
        Route::post('/catalog/{product}/toggle', [\App\Http\Controllers\ProductCatalogController::class, 'toggleOnlineStatus'])->name('catalog.toggle');
        
        Route::get('/delivery', [\App\Http\Controllers\DeliveryManagementController::class, 'index'])->name('delivery');
        
        Route::get('/riders', [\App\Http\Controllers\DeliveryRiderController::class, 'index'])->name('riders');
        Route::get('/riders/create', [\App\Http\Controllers\DeliveryRiderController::class, 'create'])->name('riders.create');
        Route::post('/riders', [\App\Http\Controllers\DeliveryRiderController::class, 'store'])->name('riders.store');
        Route::get('/riders/{rider}/edit', [\App\Http\Controllers\DeliveryRiderController::class, 'edit'])->name('riders.edit');
        Route::put('/riders/{rider}', [\App\Http\Controllers\DeliveryRiderController::class, 'update'])->name('riders.update');
        Route::delete('/riders/{rider}', [\App\Http\Controllers\DeliveryRiderController::class, 'destroy'])->name('riders.destroy');
        
        Route::get('/payments', [\App\Http\Controllers\OnlinePaymentController::class, 'index'])->name('payments');
        
        Route::get('/tracking', [\App\Http\Controllers\OrderTrackingController::class, 'index'])->name('tracking');
        Route::get('/tracking/{orderNumber}', [\App\Http\Controllers\OrderTrackingController::class, 'show'])->name('tracking.show');
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
        Route::get('/employees', function () { return view('hr.employees'); })->name('employees');
        Route::get('/roles', function () { return view('hr.roles'); })->name('roles');
        Route::get('/attendance', function () { return view('hr.attendance'); })->name('attendance');
        Route::get('/shifts', function () { return view('hr.shifts'); })->name('shifts');
        Route::get('/activity', function () { return view('hr.activity'); })->name('activity');
    });

    // Security
    Route::prefix('security')->name('security.')->group(function () {
        Route::get('/users', function () { return view('security.users'); })->name('users');
        Route::get('/access', function () { return view('security.access'); })->name('access');
        Route::get('/audit', function () { return view('security.audit'); })->name('audit');
        Route::get('/logins', function () { return view('security.logins'); })->name('logins');
        Route::get('/devices', function () { return view('security.devices'); })->name('devices');
        Route::get('/settings', function () { return view('security.settings'); })->name('settings');
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
        Route::get('/general', function () { return view('system.general'); })->name('general');
        Route::get('/tax', function () { return view('system.tax'); })->name('tax');
        Route::get('/receipt', function () { return view('system.receipt'); })->name('receipt');
        Route::get('/barcode', function () { return view('system.barcode'); })->name('barcode');
        Route::get('/backup', function () { return view('system.backup'); })->name('backup');
        Route::get('/database', function () { return view('system.database'); })->name('database');
        Route::get('/logs', function () { return view('system.logs'); })->name('logs');
    });
});

