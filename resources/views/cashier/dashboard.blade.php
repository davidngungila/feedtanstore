@extends('layouts.app')

@section('page-title', 'Cashier Dashboard')

@section('content')
<div class="animate-[fadeIn_0.4s_ease] min-h-screen p-3 sm:p-4">
    <!-- Toggle for dashboard stats on small screens -->
    <div class="flex justify-end mb-3 lg:hidden">
        <button type="button" onclick="toggleDashboardStats()" class="px-3 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium">
            <i class="fas fa-chart-bar mr-1"></i>Stats
        </button>
    </div>
    
    <!-- Dashboard Stats -->
    <div id="dashboardStats" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-4 hidden lg:grid">
        <div class="card rounded-2xl p-4">
            <h4 class="text-sm font-medium text-gray-600 mb-1">Today's Sales</h4>
            <p class="text-xl font-bold text-primary-900" id="todaySales">TZS 0.00</p>
            <p class="text-xs text-gray-500" id="todayItems">0 items</p>
        </div>
        <div class="card rounded-2xl p-4">
            <h4 class="text-sm font-medium text-gray-600 mb-1">Shift Sales</h4>
            <p class="text-xl font-bold text-primary-900" id="shiftSales">TZS 0.00</p>
            <p class="text-xs text-gray-500" id="shiftItems">0 items</p>
        </div>
        <div class="card rounded-2xl p-4">
            <h4 class="text-sm font-medium text-gray-600 mb-1">Today's Cash</h4>
            <p class="text-xl font-bold text-green-700" id="todayCash">TZS 0.00</p>
        </div>
        <div class="card rounded-2xl p-4">
            <h4 class="text-sm font-medium text-gray-600 mb-1">Today's Mobile</h4>
            <p class="text-xl font-bold text-blue-700" id="todayMobile">TZS 0.00</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
        <!-- Scan, Search & Cart -->
        <div class="lg:col-span-3 space-y-4">
            <!-- Scan & Search + Customer -->
            <div class="card rounded-2xl p-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <h2 class="text-lg font-bold text-primary-900 mb-3">Scan & Search Products</h2>
                        <div class="mb-3 p-2 bg-green-50 border border-green-200 rounded-lg">
                            <p class="text-xs text-green-700"><i class="fas fa-barcode mr-2"></i>Use a barcode scanner, your phone/PC camera, or manual code entry to add products fast.</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-2 sm:items-center mb-3">
                            <div class="flex-1 relative">
                                <i class="fas fa-barcode absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                                <input type="text" id="barcodeInput" placeholder="Scan or type barcode / SKU..." class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm" autofocus>
                            </div>
                            <div class="text-green-600 text-sm font-medium scan-indicator sm:whitespace-nowrap">
                                <i class="fas fa-circle mr-1 text-xs"></i>Scan Ready
                            </div>
                        </div>
                        <div class="flex flex-wrap gap-2 mb-3">
                            <button type="button" id="openCashierCameraBtn" onclick="startCashierCameraScanner()" class="px-3 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium">
                                <i class="fas fa-camera mr-1"></i>Scan With Camera
                            </button>
                            <button type="button" id="stopCashierCameraBtn" onclick="stopCashierCameraScanner()" class="px-3 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hidden">
                                <i class="fas fa-stop-circle mr-1"></i>Stop Camera
                            </button>
                            <span id="cashierScannerStatus" class="text-xs text-gray-500 flex items-center">Camera scanner is off.</span>
                        </div>
                        <div id="cashierCameraPanel" class="hidden mb-3 border border-gray-200 rounded-xl p-3 bg-gray-50">
                            <div id="cashierScannerViewport" class="w-full min-h-[260px] rounded-lg overflow-hidden bg-black"></div>
                            <p class="mt-2 text-xs text-gray-500">Point the camera at a product barcode. Supported on phone and desktop cameras.</p>
                        </div>
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" id="searchProduct" placeholder="Search Products..." class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm">
                            <div id="searchResults" class="absolute w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg max-h-64 overflow-y-auto hidden z-50"></div>
                        </div>
                    </div>
                    <div>
                        <h2 class="text-lg font-bold text-primary-900 mb-3">Customer</h2>
                        <div class="relative">
                            <i class="fas fa-user absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                            <input type="text" id="customerSearchInput" placeholder="Search or select customer..." class="w-full pl-9 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm">
                            <input type="hidden" id="customerSelect" value="">
                            <div id="customerSearchResults" class="absolute w-full bg-white border border-gray-300 rounded-lg mt-1 shadow-lg max-h-64 overflow-y-auto hidden z-50">
                                <div class="px-4 py-2 cursor-pointer hover:bg-gray-100" onclick="selectCustomer(null, 'Walk-in Customer')">
                                    Walk-in Customer
                                </div>
                                @foreach($customers as $customer)
                                    <div class="px-4 py-2 cursor-pointer hover:bg-gray-100" onclick="selectCustomer({{ $customer->id }}, '{{ $customer->name }} ({{ $customer->phone ?? 'No phone' }})')">
                                        {{ $customer->name }} ({{ $customer->phone ?? 'No phone' }})
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="mt-2">
                            <button onclick="showCreateCustomerModal()" class="text-sm text-primary-600 hover:text-primary-800 font-medium">
                                <i class="fas fa-plus mr-1"></i>Add New Customer
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cart -->
            <div class="card rounded-2xl p-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-3">
                    <h2 class="text-lg font-bold text-primary-900">Cart</h2>
                    <div class="flex gap-2">
                        <button type="button" onclick="showAllDetails()" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium">
                            <i class="fas fa-list mr-1"></i>Details
                        </button>
                    </div>
                </div>
                <div id="cartItems" class="mb-3 max-h-64 overflow-y-auto">
                    <!-- Cart items will be added here -->
                </div>
                <div class="border-t pt-3">
                    <div class="flex justify-between mb-1.5 text-sm">
                        <span class="text-gray-600">Subtotal:</span>
                        <span id="subtotal" class="font-semibold">TZS 0.00</span>
                    </div>
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 mb-1.5 text-sm">
                        <label class="text-gray-600">Discount:</label>
                        <div class="flex flex-col sm:flex-row sm:items-center gap-2">
                            <div class="flex w-full sm:w-auto border border-gray-300 rounded overflow-hidden">
                                <select id="discountType" class="px-2 py-1 border-r border-gray-300 bg-gray-50 text-sm" onchange="updateTotals()">
                                    <option value="amount">TZS</option>
                                    <option value="percent">%</option>
                                </select>
                                <input type="number" id="discountInput" placeholder="0" class="w-full sm:w-24 px-2 py-1 border-0 text-sm" onchange="updateTotals()">
                            </div>
                            <span id="discountAmount" class="font-semibold text-red-600">-TZS 0.00</span>
                        </div>
                    </div>
                    <div class="flex justify-between text-lg font-bold">
                        <span>Total:</span>
                        <span id="total">TZS 0.00</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payment & Quick Actions -->
        <div class="space-y-4">
            <!-- Toggle for quick actions on small screens -->
            <div class="lg:hidden">
                <button type="button" onclick="toggleQuickActions()" class="w-full px-3 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg text-sm font-medium">
                    <i class="fas fa-bolt mr-1"></i>Quick Actions
                </button>
            </div>
            
            <!-- Quick Actions -->
            <div id="quickActions" class="card rounded-2xl p-4 hidden lg:block">
                <h2 class="text-lg font-bold text-primary-900 mb-3">Quick Actions</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    <button type="button" onclick="toggleFullscreen()" class="py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700 text-sm font-medium" id="fullscreenBtn">
                        <i class="fas fa-expand mr-1"></i>Fullscreen
                    </button>
                    <button type="button" onclick="holdSale()" class="py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700 text-sm font-medium">
                        <i class="fas fa-pause mr-1"></i>Hold Sale
                    </button>
                    <button type="button" onclick="showHeldSalesModal()" class="py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700 text-sm font-medium">
                        <i class="fas fa-folder-open mr-1"></i>Retrieve Sale
                    </button>
                    <button type="button" onclick="clearCart()" class="py-2 border border-gray-300 rounded-lg hover:bg-gray-50 text-gray-700 text-sm font-medium">
                        <i class="fas fa-trash mr-1"></i>Clear Cart
                    </button>
                    <button type="button" onclick="cancelSale()" class="py-2 border border-red-300 rounded-lg hover:bg-red-50 text-red-700 text-sm font-medium sm:col-span-2">
                        <i class="fas fa-times mr-1"></i>Cancel Sale
                    </button>
                </div>
            </div>

            <!-- Recent Transactions Button -->
            <div class="card rounded-2xl p-4">
                <button type="button" onclick="showTransactionsModal()" class="w-full py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-semibold text-base">
                    <i class="fas fa-history mr-1"></i>Recent Transactions
                </button>
            </div>

            <!-- Payment Panel -->
            <div class="card rounded-2xl p-4">
                <h2 class="text-xl font-bold text-primary-900 mb-3">Payment</h2>
                <div class="mb-4 p-4 bg-primary-50 rounded-lg text-center">
                    <p class="text-base text-primary-600 mb-1 font-semibold">TOTAL</p>
                    <p class="text-xl font-bold text-primary-800 break-words" id="paymentTotal">TZS 0.00</p>
                </div>
                <div class="space-y-3 mb-4">
                    <label class="block">
                        <span class="text-gray-700 font-medium mb-1.5 text-sm">Paid Amount</span>
                        <input type="number" id="paidAmount" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-lg font-semibold" oninput="calculateChange()" placeholder="Enter amount paid">
                    </label>
                    <div class="mt-2">
                        <div class="flex justify-between text-lg font-bold">
                            <span class="text-gray-600 text-sm">Change:</span>
                            <span class="text-green-600" id="changeAmount">TZS 0.00</span>
                        </div>
                    </div>
                </div>
                <div class="mb-4">
                    <p class="text-gray-700 font-medium mb-2 text-sm">Payment Method</p>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        <button type="button" class="flex-1 py-2.5 border-2 border-primary-600 bg-primary-600 text-white rounded-lg font-semibold text-sm" id="methodCash" onclick="selectPaymentMethod('cash')">
                            <i class="fas fa-money-bill mr-1"></i>Cash
                        </button>
                        <button type="button" class="flex-1 py-2.5 border-2 border-gray-300 text-gray-700 hover:border-primary-500 rounded-lg font-semibold text-sm" id="methodCard" onclick="selectPaymentMethod('card')">
                            <i class="fas fa-credit-card mr-1"></i>Card
                        </button>
                        <button type="button" class="flex-1 py-2.5 border-2 border-gray-300 text-gray-700 hover:border-primary-500 rounded-lg font-semibold text-sm" id="methodMobile" onclick="selectPaymentMethod('mobile')">
                            <i class="fas fa-mobile-alt mr-1"></i>Mobile
                        </button>
                    </div>
                </div>

                <button type="button" id="completeSaleBtn" onclick="completeSale()" class="w-full py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold text-lg mt-2">
                    <span id="btnLoading" class="hidden"><i class="fas fa-spinner fa-spin mr-1"></i>Processing...</span>
                    <span id="btnText"><i class="fas fa-check mr-1"></i>Complete Sale</span>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" class="fixed inset-0 bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center hidden z-[100]">
    <div class="bg-white rounded-2xl p-8 text-center">
        <div class="w-20 h-20 mx-auto mb-4">
            <i class="fas fa-spinner fa-spin text-primary-600 text-6xl"></i>
        </div>
        <h3 class="text-xl font-bold text-primary-900 mb-2">Processing Sale</h3>
        <p class="text-gray-600">Please wait...</p>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-5 sm:p-8 max-w-md w-full mx-4">
        <div class="text-center">
            <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-check text-green-600 text-4xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-primary-900 mb-4">Payment Successful!</h2>
            <div class="space-y-2 text-lg mb-6">
                <div class="flex justify-between">
                    <span class="text-gray-600">Total:</span>
                    <span class="font-semibold" id="modalTotal">TZS 0.00</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Paid:</span>
                    <span class="font-semibold" id="modalPaid">TZS 0.00</span>
                </div>
                <div class="flex justify-between text-green-600 font-bold">
                    <span>Change:</span>
                    <span id="modalChange">TZS 0.00</span>
                </div>
            </div>
            <div class="flex flex-col sm:flex-row gap-3">
                <button onclick="printReceipt()" class="flex-1 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-semibold text-lg">
                    <i class="fas fa-print mr-2"></i>Print Receipt
                </button>
                <button onclick="newSale()" class="flex-1 py-3 bg-primary-600 hover:bg-primary-700 text-white rounded-xl font-semibold text-lg">
                    <i class="fas fa-plus mr-2"></i>New Sale
                </button>
            </div>
        </div>
    </div>
</div>

<!-- All Details Modal -->
<div id="detailsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-4 sm:p-6 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between gap-4 mb-6">
            <h2 class="text-2xl font-bold text-primary-900">Sales Details</h2>
            <button type="button" onclick="hideAllDetails()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="detailsContent" class="space-y-6">
            <!-- Details will be loaded here -->
        </div>
    </div>
</div>

<!-- Recent Transactions Modal -->
<div id="transactionsModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-4 sm:p-6 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between gap-4 mb-6">
            <h2 class="text-2xl font-bold text-primary-900">Recent Transactions</h2>
            <button type="button" onclick="hideTransactionsModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="transactionsModalContent" class="space-y-3">
            <!-- Transactions will be loaded here -->
        </div>
    </div>
</div>

<!-- Held Sales Modal -->
<div id="heldSalesModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-4 sm:p-6 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between gap-4 mb-6">
            <h2 class="text-2xl font-bold text-primary-900">Held Sales</h2>
            <button type="button" onclick="hideHeldSalesModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div id="heldSalesModalContent" class="space-y-3">
            <!-- Held sales will be loaded here -->
        </div>
    </div>
</div>

<!-- Create Customer Modal -->
<div id="createCustomerModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center hidden z-50">
    <div class="bg-white rounded-2xl p-4 sm:p-6 max-w-md w-full mx-4">
        <div class="flex items-center justify-between gap-4 mb-6">
            <h2 class="text-xl font-bold text-primary-900">Add New Customer</h2>
            <button type="button" onclick="hideCreateCustomerModal()" class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <form id="createCustomerForm" class="space-y-4">
            <div>
                <label class="block text-gray-700 font-medium mb-2 text-sm">Name <span class="text-red-500">*</span></label>
                <input type="text" id="customerName" required class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm">
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-2 text-sm">Phone</label>
                <input type="text" id="customerPhone" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm">
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-2 text-sm">Email</label>
                <input type="email" id="customerEmail" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm">
            </div>
            <div>
                <label class="block text-gray-700 font-medium mb-2 text-sm">Address</label>
                <textarea id="customerAddress" rows="2" class="w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 text-sm"></textarea>
            </div>
            <div class="flex flex-col sm:flex-row gap-3 pt-2">
                <button type="button" onclick="hideCreateCustomerModal()" class="flex-1 py-2.5 border border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 text-sm">Cancel</button>
                <button type="submit" class="flex-1 py-2.5 bg-primary-600 text-white rounded-lg font-semibold hover:bg-primary-700 text-sm">Add Customer</button>
            </div>
        </form>
    </div>
</div>

<script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
<script>
let cart = [];
let selectedPaymentMethod = 'cash';
let productsData = @json($products).map(p => ({...p, selling_price: parseFloat(p.selling_price)}));
let customersData = @json($customers);
let currentSaleId = null;
let isProcessing = false;
let cashierQrScanner = null;
let cashierScannerActive = false;
let cashierLastScannedCode = null;
let cashierLastScanAt = 0;
let dashboardData = {
    todaySales: 0,
    shiftSales: 0,
    todayItems: 0,
    shiftItems: 0,
    todayBreakdown: {cash: 0, card: 0, mobile: 0},
    shiftBreakdown: {cash: 0, card: 0, mobile: 0},
    transactions: []
};

function toggleDashboardStats() {
    const statsEl = document.getElementById('dashboardStats');
    statsEl.classList.toggle('hidden');
    statsEl.classList.toggle('grid');
}

function toggleQuickActions() {
    const quickActionsEl = document.getElementById('quickActions');
    quickActionsEl.classList.toggle('hidden');
}

document.addEventListener('DOMContentLoaded', function() {
    updateTime();
    setInterval(updateTime, 1000);
    setupBarcodeScanner();
    setupProductSearch();
    renderCart();
    loadDashboardData();
    setInterval(loadDashboardData, 10000); // Refresh every 10 seconds
    setupCreateCustomerForm();
    setupCustomerSearch();

    // Kiosk Mode
    const kioskModeEnabled = {{ $storeSetting->kiosk_mode_enabled ? 'true' : 'false' }};
    const kioskForceFullscreen = {{ $storeSetting->kiosk_force_fullscreen ? 'true' : 'false' }};
    const kioskBlockRightClick = {{ $storeSetting->kiosk_block_right_click ? 'true' : 'false' }};
    const kioskPreventTabSwitch = {{ $storeSetting->kiosk_prevent_tab_switch ? 'true' : 'false' }};
    const kioskLockKeyboardShortcuts = {{ $storeSetting->kiosk_lock_keyboard_shortcuts ? 'true' : 'false' }};
    const kioskAutoFocusCashier = {{ $storeSetting->kiosk_auto_focus_cashier ? 'true' : 'false' }};

    if (kioskModeEnabled) {
        // Auto-focus cashier
        if (kioskAutoFocusCashier) {
            const barcodeInput = document.getElementById('barcodeInput');
            if (barcodeInput) {
                barcodeInput.focus();
                // Re-focus if user clicks away
                document.addEventListener('click', function() {
                    setTimeout(() => barcodeInput.focus(), 100);
                });
            }
        }

        // Force fullscreen
        if (kioskForceFullscreen) {
            const enterFullscreen = () => {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen().catch(err => {
                        console.log('Fullscreen request failed:', err);
                    });
                }
            };

            enterFullscreen();
            document.addEventListener('fullscreenchange', function() {
                if (!document.fullscreenElement) {
                    setTimeout(enterFullscreen, 100);
                }
            });
            document.addEventListener('visibilitychange', function() {
                if (!document.hidden) {
                    enterFullscreen();
                }
            });
        }

        // Block right-click
        if (kioskBlockRightClick) {
            document.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                return false;
            });
        }

        // Prevent tab switching
        if (kioskPreventTabSwitch) {
            window.addEventListener('beforeunload', function(e) {
                e.preventDefault();
                e.returnValue = '';
                return '';
            });
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    window.focus();
                }
            });
        }

        // Lock keyboard shortcuts
        if (kioskLockKeyboardShortcuts) {
            document.addEventListener('keydown', function(e) {
                if (e.key.startsWith('F') && !isNaN(e.key.slice(1))) {
                    e.preventDefault();
                    return false;
                }
                if (e.ctrlKey || e.metaKey) {
                    if (['w', 'n', 't', 'r', 'q', 'Tab'].includes(e.key.toLowerCase())) {
                        e.preventDefault();
                        return false;
                    }
                    if (e.shiftKey && ['i', 'j', 'c', 't', 'n'].includes(e.key.toLowerCase())) {
                        e.preventDefault();
                        return false;
                    }
                }
                if (e.altKey) {
                    if (['F4', 'Tab'].includes(e.key)) {
                        e.preventDefault();
                        return false;
                    }
                }
            });
        }
    }
});

window.addEventListener('beforeunload', function() {
    stopCashierCameraScanner();
});

function showCreateCustomerModal() {
    document.getElementById('loadingOverlay').classList.add('hidden');
    document.getElementById('createCustomerModal').classList.remove('hidden');
}

function hideCreateCustomerModal() {
    document.getElementById('createCustomerModal').classList.add('hidden');
    document.getElementById('createCustomerForm').reset();
    document.getElementById('loadingOverlay').classList.add('hidden');
}

function selectCustomer(id, displayText) {
    document.getElementById('customerSelect').value = id || '';
    document.getElementById('customerSearchInput').value = displayText;
    document.getElementById('customerSearchResults').classList.add('hidden');
}

function setupCustomerSearch() {
    const searchInput = document.getElementById('customerSearchInput');
    const resultsDiv = document.getElementById('customerSearchResults');
    
    // Show results when input is focused
    searchInput.addEventListener('focus', function() {
        renderCustomerSearchResults('');
        resultsDiv.classList.remove('hidden');
    });
    
    // Filter results when typing
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        renderCustomerSearchResults(searchTerm);
        resultsDiv.classList.remove('hidden');
    });
    
    // Hide results when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !resultsDiv.contains(e.target)) {
            resultsDiv.classList.add('hidden');
        }
    });
}

function renderCustomerSearchResults(searchTerm) {
    const resultsDiv = document.getElementById('customerSearchResults');
    let html = `
        <div class="px-4 py-2 cursor-pointer hover:bg-gray-100" onclick="selectCustomer(null, 'Walk-in Customer')">
            Walk-in Customer
        </div>
    `;
    
    // Filter customers
    const filteredCustomers = customersData.filter(customer => {
        if (!searchTerm) return true;
        const name = customer.name ? customer.name.toLowerCase() : '';
        const phone = customer.phone ? customer.phone.toLowerCase() : '';
        const email = customer.email ? customer.email.toLowerCase() : '';
        return name.includes(searchTerm) || phone.includes(searchTerm) || email.includes(searchTerm);
    });
    
    // Add filtered customers to results
    filteredCustomers.forEach(customer => {
        html += `
            <div class="px-4 py-2 cursor-pointer hover:bg-gray-100" onclick="selectCustomer(${customer.id}, '${customer.name} (${customer.phone || 'No phone'})')">
                ${customer.name} (${customer.phone || 'No phone'})
            </div>
        `;
    });
    
    resultsDiv.innerHTML = html;
}

function setupCreateCustomerForm() {
    document.getElementById('createCustomerForm').addEventListener('submit', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Make sure loading overlay is definitely hidden
        document.getElementById('loadingOverlay').classList.add('hidden');
        
        const form = this;
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Adding...';
        
        const name = document.getElementById('customerName').value;
        const phone = document.getElementById('customerPhone').value;
        const email = document.getElementById('customerEmail').value;
        const address = document.getElementById('customerAddress').value;

        fetch('/customers', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({name, phone, email, address})
        })
        .then(response => {
            console.log('Response status:', response.status);
            if (!response.ok) {
                return response.text().then(text => {
                    console.error('Response text:', text);
                    try {
                        return JSON.parse(text);
                    } catch {
                        return { success: false, message: text || 'Server error' };
                    }
                });
            }
            return response.json();
        })
        .then(data => {
            console.log('Response data:', data);
            if (data.success) {
                customersData.push(data.customer);
                const displayText = data.customer.name + ' (' + (data.customer.phone || 'No phone') + ')';
                selectCustomer(data.customer.id, displayText);
                hideCreateCustomerModal();
                showNotification('Customer added successfully!', 'success');
            } else {
                showNotification(data.message || 'Failed to add customer', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Error adding customer: ' + error.message, 'error');
        })
        .finally(() => {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        });
    });
}

async function loadDashboardData() {
    try {
        const response = await fetch('/cashier/dashboard-data');
        dashboardData = await response.json();
        updateDashboardDisplay();
    } catch (e) {
        console.error('Error loading dashboard data', e);
    }
}

function updateDashboardDisplay() {
    // Safely parse all numbers
    const todayTotal = parseFloat(dashboardData.todayTotal || 0);
    const shiftTotal = parseFloat(dashboardData.shiftTotal || 0);
    const todayItemsCount = parseInt(dashboardData.todayItems || 0);
    const shiftItemsCount = parseInt(dashboardData.shiftItems || 0);
    const todayCash = parseFloat(dashboardData.todayBreakdown?.cash || 0);
    const todayMobile = parseFloat(dashboardData.todayBreakdown?.mobile || 0);
    const todayCard = parseFloat(dashboardData.todayBreakdown?.card || 0);
    const shiftCash = parseFloat(dashboardData.shiftBreakdown?.cash || 0);
    const shiftCard = parseFloat(dashboardData.shiftBreakdown?.card || 0);
    const shiftMobile = parseFloat(dashboardData.shiftBreakdown?.mobile || 0);
    
    document.getElementById('todaySales').textContent = 'TZS ' + formatNumber(todayTotal);
    document.getElementById('shiftSales').textContent = 'TZS ' + formatNumber(shiftTotal);
    document.getElementById('todayItems').textContent = todayItemsCount + ' items';
    document.getElementById('shiftItems').textContent = shiftItemsCount + ' items';
    document.getElementById('todayCash').textContent = 'TZS ' + formatNumber(todayCash);
    document.getElementById('todayMobile').textContent = 'TZS ' + formatNumber(todayMobile + todayCard);
}

function updateTime() {
    const now = new Date();
}

function toggleFullscreen() {
    const btn = document.getElementById('fullscreenBtn');
    
    if (!document.fullscreenElement) {
        stayInFullscreen = true;
        document.documentElement.requestFullscreen().catch(err => {
            console.log('Fullscreen request failed:', err);
        });
    } else {
        stayInFullscreen = false;
        if (document.exitFullscreen) {
            document.exitFullscreen();
        }
    }
}

let stayInFullscreen = false;

document.addEventListener('fullscreenchange', function() {
    const btn = document.getElementById('fullscreenBtn');
    
    if (document.fullscreenElement) {
        btn.innerHTML = '<i class="fas fa-compress mr-1"></i>Exit Fullscreen';
        stayInFullscreen = true;
    } else {
        btn.innerHTML = '<i class="fas fa-expand mr-1"></i>Fullscreen';
        
        // If we were supposed to stay in fullscreen, re-enter it
        if (stayInFullscreen) {
            document.documentElement.requestFullscreen().catch(err => {
                console.log('Failed to re-enter fullscreen:', err);
            });
        }
    }
});

// Keep fullscreen mode during printing
let fullscreenBeforePrint = false;

window.addEventListener('beforeprint', function() {
    fullscreenBeforePrint = !!document.fullscreenElement;
});

window.addEventListener('afterprint', function() {
    if (fullscreenBeforePrint && !document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch(err => {
            console.log('Failed to re-enter fullscreen after print:', err);
        });
    }
});

function setupBarcodeScanner() {
    let barcodeBuffer = '';
    let lastInputTime = 0;

    document.addEventListener('keydown', function(e) {
        const now = Date.now();
        if (now - lastInputTime > 100) {
            barcodeBuffer = '';
        }
        lastInputTime = now;

        if (e.key === 'Enter') {
            e.preventDefault(); // Prevent default action to keep fullscreen
            if (barcodeBuffer.length > 0) {
                addProductByBarcode(barcodeBuffer.trim());
                barcodeBuffer = '';
            } else if (document.getElementById('barcodeInput') === document.activeElement) {
                // If barcode input has focus and Enter is pressed, use its value
                addProductByBarcode(document.getElementById('barcodeInput').value.trim());
                document.getElementById('barcodeInput').value = '';
            }
            return;
        }
        // Only add to buffer if it's a printable character and not a modifier key
        if (e.key.length === 1 && !e.ctrlKey && !e.metaKey && !e.altKey) {
            barcodeBuffer += e.key;
        }
    });

    document.getElementById('barcodeInput').addEventListener('keydown', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault(); // Prevent default action
            // This is handled by the global listener, so just clear the buffer
            barcodeBuffer = '';
        }
    });
}

function updateCashierScannerStatus(message, tone = 'muted') {
    const status = document.getElementById('cashierScannerStatus');
    if (!status) return;
    status.textContent = message;
    status.classList.remove('text-gray-500', 'text-green-600', 'text-red-600', 'text-blue-600');
    status.classList.add(
        tone === 'success' ? 'text-green-600' :
        tone === 'error' ? 'text-red-600' :
        tone === 'info' ? 'text-blue-600' :
        'text-gray-500'
    );
}

function handleCashierScannedCode(code) {
    const normalized = String(code || '').trim();
    if (!normalized) return;

    const now = Date.now();
    if (cashierLastScannedCode === normalized && (now - cashierLastScanAt) < 1500) {
        return;
    }

    cashierLastScannedCode = normalized;
    cashierLastScanAt = now;
    const added = addProductByBarcode(normalized, { fromCamera: true });
    if (added) {
        stopCashierCameraScanner('Product added. Tap "Scan With Camera" for the next item.');
    }
}

async function startCashierCameraScanner() {
    if (cashierScannerActive) return;
    if (typeof Html5Qrcode === 'undefined') {
        updateCashierScannerStatus('Camera scanner library failed to load.', 'error');
        return;
    }

    const panel = document.getElementById('cashierCameraPanel');
    const openBtn = document.getElementById('openCashierCameraBtn');
    const stopBtn = document.getElementById('stopCashierCameraBtn');
    if (panel) panel.classList.remove('hidden');
    if (openBtn) openBtn.classList.add('hidden');
    if (stopBtn) stopBtn.classList.remove('hidden');
    updateCashierScannerStatus('Starting camera scanner...', 'info');

    try {
        cashierQrScanner = new Html5Qrcode('cashierScannerViewport');
        const cameras = await Html5Qrcode.getCameras();
        const backCamera = cameras.find(camera => /back|rear|environment/i.test(camera.label));
        const cameraConfig = backCamera ? { deviceId: { exact: backCamera.id } } : { facingMode: 'environment' };

        await cashierQrScanner.start(
            cameraConfig,
            {
                fps: 10,
                qrbox: { width: 260, height: 160 },
                aspectRatio: 1.777778,
                formatsToSupport: [
                    Html5QrcodeSupportedFormats.CODE_128,
                    Html5QrcodeSupportedFormats.CODE_39,
                    Html5QrcodeSupportedFormats.CODE_93,
                    Html5QrcodeSupportedFormats.EAN_13,
                    Html5QrcodeSupportedFormats.EAN_8,
                    Html5QrcodeSupportedFormats.UPC_A,
                    Html5QrcodeSupportedFormats.UPC_E,
                    Html5QrcodeSupportedFormats.QR_CODE
                ]
            },
            decodedText => handleCashierScannedCode(decodedText),
            () => {}
        );

        cashierScannerActive = true;
        updateCashierScannerStatus('Camera scanner is live. Point it at a barcode.', 'success');
    } catch (error) {
        console.error('Failed to start cashier camera scanner', error);
        cashierScannerActive = false;
        updateCashierScannerStatus('Unable to start camera scanner. Check camera permission.', 'error');
        stopCashierCameraScanner();
    }
}

async function stopCashierCameraScanner(message = 'Camera scanner is off.') {
    const panel = document.getElementById('cashierCameraPanel');
    const openBtn = document.getElementById('openCashierCameraBtn');
    const stopBtn = document.getElementById('stopCashierCameraBtn');

    try {
        if (cashierQrScanner) {
            if (cashierScannerActive) {
                await cashierQrScanner.stop();
            }
            await cashierQrScanner.clear();
        }
    } catch (error) {
        console.error('Failed to stop cashier camera scanner', error);
    } finally {
        cashierQrScanner = null;
        cashierScannerActive = false;
        if (panel) panel.classList.add('hidden');
        if (openBtn) openBtn.classList.remove('hidden');
        if (stopBtn) stopBtn.classList.add('hidden');
        updateCashierScannerStatus(message, message === 'Camera scanner is off.' ? 'muted' : 'success');
    }
}

function setupProductSearch() {
    const searchInput = document.getElementById('searchProduct');
    const searchResults = document.getElementById('searchResults');

    function renderProducts(filtered) {
        if (filtered.length > 0) {
            searchResults.innerHTML = filtered.map(p => `
                <div class="px-4 py-3 hover:bg-gray-100 cursor-pointer border-b border-gray-100 last:border-0" onclick="addProductToCart(${p.id}, '${p.name.replace(/'/g, "\\'")}', ${parseFloat(p.selling_price)})">
                    <p class="font-semibold text-primary-900">${p.name}</p>
                    <p class="text-sm text-gray-600">TZS ${parseFloat(p.selling_price).toFixed(2)}</p>
                </div>
            `).join('');
        } else {
            searchResults.innerHTML = '<div class="px-4 py-3 text-gray-500">No products found</div>';
        }
        searchResults.classList.remove('hidden');
    }

    searchInput.addEventListener('input', function() {
        const term = this.value.toLowerCase();
        if (term.length < 2) {
            renderProducts(productsData);
            return;
        }

        const filtered = productsData.filter(p => 
            p.name.toLowerCase().includes(term) || 
            (p.barcode && p.barcode.includes(term))
        );
        renderProducts(filtered);
    });

    searchInput.addEventListener('focus', function() {
        if (this.value.length < 2) {
            renderProducts(productsData);
        }
    });

    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.classList.add('hidden');
        }
    });
}

function addProductByBarcode(barcode, options = {}) {
    const normalized = String(barcode || '').trim();
    if (!normalized) {
        document.getElementById('barcodeInput').value = '';
        return false;
    }

    const product = productsData.find(p => p.barcode === normalized || p.sku === normalized);
    if (product) {
        addProductToCart(product.id, product.name, parseFloat(product.selling_price));
        updateCashierScannerStatus(
            options.fromCamera
                ? 'Added ' + product.name + '. Tap "Scan With Camera" to scan another product.'
                : 'Added ' + product.name + ' from scan.',
            'success'
        );
        document.getElementById('barcodeInput').value = '';
        return true;
    } else {
        showNotification('No product found for code: ' + normalized, 'error');
        updateCashierScannerStatus('No product found for code: ' + normalized, 'error');
    }
    document.getElementById('barcodeInput').value = '';
    return false;
}

function addProductToCart(id, name, price) {
    const existing = cart.find(item => item.id === id);
    if (existing) {
        existing.quantity += 1;
    } else {
        cart.push({id, name, price: parseFloat(price), quantity: 1});
    }
    renderCart();
    showNotification('Added ' + name + ' to cart', 'success');
    document.getElementById('barcodeInput').focus();
}

function renderCart() {
    const container = document.getElementById('cartItems');
    if (cart.length === 0) {
        container.innerHTML = '<p class="text-gray-500 text-center py-8">Cart is empty</p>';
    } else {
        container.innerHTML = cart.map((item, index) => `
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 py-3 border-b border-gray-100">
                <div class="flex-1 min-w-0">
                    <p class="font-medium text-primary-900">${item.name}</p>
                    <p class="text-sm text-gray-600">TZS ${parseFloat(item.price).toFixed(2)}</p>
                </div>
                <div class="flex items-center justify-between sm:justify-end gap-2 w-full sm:w-auto">
                    <button type="button" class="w-8 h-8 rounded-lg border border-gray-300 flex items-center justify-center hover:bg-gray-100" onclick="updateQuantity(${index}, -1)">-</button>
                    <span class="font-semibold text-gray-800 w-8 text-center">${item.quantity}</span>
                    <button type="button" class="w-8 h-8 rounded-lg border border-gray-300 flex items-center justify-center hover:bg-gray-100" onclick="updateQuantity(${index}, 1)">+</button>
                    <button type="button" onclick="removeFromCart(${index})" class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg ml-2" title="Remove Item">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }
    updateTotals();
}

function updateQuantity(index, delta) {
    cart[index].quantity += delta;
    if (cart[index].quantity <= 0) {
        cart.splice(index, 1);
    }
    renderCart();
}

function removeFromCart(index) {
    cart.splice(index, 1);
    renderCart();
}

function clearCart() {
    if (confirm('Clear entire cart?')) {
        cart = [];
        renderCart();
    }
}

function formatNumber(num) {
    return num.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function updateTotals() {
    const subtotal = cart.reduce((sum, item) => sum + (parseFloat(item.price) * item.quantity), 0);
    const discountType = document.getElementById('discountType').value;
    const discountValue = parseFloat(document.getElementById('discountInput').value) || 0;
    let discount = 0;

    if (discountType === 'percent') {
        discount = (discountValue / 100) * subtotal;
    } else {
        discount = discountValue;
    }

    const total = Math.max(0, subtotal - discount);

    document.getElementById('subtotal').textContent = 'TZS ' + formatNumber(subtotal);
    document.getElementById('discountAmount').textContent = '-TZS ' + formatNumber(discount);
    document.getElementById('total').textContent = 'TZS ' + formatNumber(total);
    document.getElementById('paymentTotal').textContent = 'TZS ' + formatNumber(total);

    // Set paid amount automatically to total
    document.getElementById('paidAmount').value = total;
    calculateChange();
}

function selectPaymentMethod(method) {
    selectedPaymentMethod = method;
    document.querySelectorAll('[id^="method"]').forEach(btn => {
        btn.classList.remove('border-primary-600', 'bg-primary-600', 'text-white');
        btn.classList.add('border-gray-300', 'text-gray-700');
    });

    document.getElementById('method' + method.charAt(0).toUpperCase() + method.slice(1)).classList.remove('border-gray-300', 'text-gray-700');
    document.getElementById('method' + method.charAt(0).toUpperCase() + method.slice(1)).classList.add('border-primary-600', 'bg-primary-600', 'text-white');
}

function setPaidAmount(amount) {
    document.getElementById('paidAmount').value = amount;
    calculateChange();
}

function calculateChange() {
    const total = cart.reduce((sum, item) => sum + (parseFloat(item.price) * item.quantity), 0) - (parseFloat(document.getElementById('discountInput').value) || 0);
    const paid = parseFloat(document.getElementById('paidAmount').value) || 0;
    const change = paid - total;
    document.getElementById('changeAmount').textContent = 'TZS ' + formatNumber(change);
}

function completeSale() {
    if (isProcessing) return;
    
    if (cart.length === 0) {
        showNotification('Cart is empty!', 'error');
        return;
    }
    const paid = parseFloat(document.getElementById('paidAmount').value) || 0;
    const subtotal = cart.reduce((sum, item) => sum + (parseFloat(item.price) * item.quantity), 0);
    const discountType = document.getElementById('discountType').value;
    const discountValue = parseFloat(document.getElementById('discountInput').value) || 0;
    let discount = 0;
    if (discountType === 'percent') {
        discount = (discountValue / 100) * subtotal;
    } else {
        discount = discountValue;
    }
    const total = Math.max(0, subtotal - discount);

    if (paid < total) {
        showNotification('Insufficient payment!', 'error');
        return;
    }

    isProcessing = true;
    document.getElementById('loadingOverlay').classList.remove('hidden');

    const formattedCart = cart.map(item => ({
        id: item.id,
        name: item.name,
        price: parseFloat(item.price),
        quantity: item.quantity
    }));

    const customerId = document.getElementById('customerSelect').value;

    fetch('/cashier/sale', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            items: formattedCart,
            total,
            discount,
            discount_type: discountType,
            discount_value: discountValue,
            paid,
            payment_method: selectedPaymentMethod,
            customer_id: customerId ? parseInt(customerId) : null
        })
    })
    .then(r => {
        if (!r.ok) {
            return r.json().then(err => Promise.reject(err));
        }
        return r.json();
    })
    .then(data => {
        currentSaleId = data.sale_id;
        document.getElementById('modalTotal').textContent = 'TZS ' + formatNumber(total);
        document.getElementById('modalPaid').textContent = 'TZS ' + formatNumber(paid);
        document.getElementById('modalChange').textContent = 'TZS ' + formatNumber(paid - total);
        document.getElementById('successModal').classList.remove('hidden');
        loadDashboardData();
        
        setTimeout(() => {
            if (currentSaleId) {
                printReceipt();
            }
        }, 500);
    })
    .catch(e => {
        console.error(e);
        showNotification(e.error || 'Error completing sale', 'error');
    })
    .finally(() => {
        isProcessing = false;
        document.getElementById('loadingOverlay').classList.add('hidden');
    });
}

function printReceipt() {
    if (currentSaleId) {
        // Save current fullscreen state
        const wasFullscreen = !!document.fullscreenElement;
        
        // Create an iframe to load and print the receipt
        const iframe = document.createElement('iframe');
        iframe.style.position = 'fixed';
        iframe.style.right = '0';
        iframe.style.bottom = '0';
        iframe.style.width = '0';
        iframe.style.height = '0';
        iframe.style.border = '0';
        iframe.src = '/sales/receipts/' + currentSaleId + '/print';
        document.body.appendChild(iframe);
        
        // Wait for iframe to load, then print
        iframe.onload = function() {
            setTimeout(() => {
                iframe.contentWindow.print();
                // Remove iframe after printing
                setTimeout(() => {
                    document.body.removeChild(iframe);
                    
                    // Re-enter fullscreen if we were in fullscreen before
                    if (wasFullscreen && !document.fullscreenElement) {
                        document.documentElement.requestFullscreen().catch(err => {
                            console.log('Failed to re-enter fullscreen:', err);
                        });
                    }
                }, 500);
            }, 500);
        };
    }
}

function showTransactionsModal() {
    const contentDiv = document.getElementById('transactionsModalContent');
    if (dashboardData.transactions.length > 0) {
        contentDiv.innerHTML = dashboardData.transactions.map(t => `
            <div class="p-4 border border-gray-200 rounded-lg">
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2 mb-2">
                    <span class="font-bold text-lg text-primary-900">${t.invoice_number}</span>
                    <span class="text-sm text-gray-500">${t.created_at}</span>
                </div>
                <div class="flex justify-between gap-3 text-sm mb-1">
                    <span class="text-gray-600">${t.items_count} items</span>
                    <span class="font-semibold text-gray-800">TZS ${formatNumber(parseFloat(t.total))}</span>
                </div>
                <div class="flex flex-col sm:flex-row sm:justify-between gap-2 text-xs">
                    <span class="text-gray-500">${t.payment_method.toUpperCase()}</span>
                    <button type="button" onclick="printSpecificReceipt('${t.id}')" class="text-blue-600 hover:text-blue-800">
                        <i class="fas fa-print mr-1"></i>Print
                    </button>
                </div>
            </div>
        `).join('');
    } else {
        contentDiv.innerHTML = '<p class="text-gray-500 text-center py-8">No transactions yet</p>';
    }
    document.getElementById('transactionsModal').classList.remove('hidden');
}

function hideTransactionsModal() {
    document.getElementById('transactionsModal').classList.add('hidden');
}

function printSpecificReceipt(saleId) {
    const iframe = document.createElement('iframe');
    iframe.style.position = 'fixed';
    iframe.style.right = '0';
    iframe.style.bottom = '0';
    iframe.style.width = '0';
    iframe.style.height = '0';
    iframe.style.border = '0';
    iframe.src = '/sales/receipts/' + saleId + '/print';
    document.body.appendChild(iframe);
    
    iframe.onload = function() {
        setTimeout(() => {
            iframe.contentWindow.print();
            setTimeout(() => {
                document.body.removeChild(iframe);
            }, 500);
        }, 500);
    };
}

function newSale() {
    cart = [];
    currentSaleId = null;
    document.getElementById('successModal').classList.add('hidden');
    document.getElementById('paidAmount').value = '';
    document.getElementById('discountInput').value = '';
    document.getElementById('discountType').value = 'amount';
    selectCustomer(null, '');
    selectPaymentMethod('cash');
    renderCart();
}

function showAllDetails() {
    const detailsContent = document.getElementById('detailsContent');
    let html = '';

    // Safely parse all numbers
    const todayTotal = parseFloat(dashboardData.todayTotal || 0);
    const shiftTotal = parseFloat(dashboardData.shiftTotal || 0);
    const todayItemsCount = parseInt(dashboardData.todayItems || 0);
    const shiftItemsCount = parseInt(dashboardData.shiftItems || 0);
    const todayCash = parseFloat(dashboardData.todayBreakdown?.cash || 0);
    const todayCard = parseFloat(dashboardData.todayBreakdown?.card || 0);
    const todayMobile = parseFloat(dashboardData.todayBreakdown?.mobile || 0);
    const shiftCash = parseFloat(dashboardData.shiftBreakdown?.cash || 0);
    const shiftCard = parseFloat(dashboardData.shiftBreakdown?.card || 0);
    const shiftMobile = parseFloat(dashboardData.shiftBreakdown?.mobile || 0);

        html += '<div class="grid grid-cols-1 md:grid-cols-2 gap-4">';
    html += '<div class="p-4 bg-gray-50 rounded-xl">';
    html += '<h4 class="font-bold text-lg text-primary-900 mb-3">Today</h4>';
    html += '<p class="mb-2"><strong>Total:</strong> TZS ' + todayTotal.toFixed(2) + '</p>';
    html += '<p class="mb-2"><strong>Items:</strong> ' + todayItemsCount + '</p>';
    html += '<p class="mb-2"><strong>Cash:</strong> TZS ' + todayCash.toFixed(2) + '</p>';
    html += '<p class="mb-2"><strong>Card:</strong> TZS ' + todayCard.toFixed(2) + '</p>';
    html += '<p><strong>Mobile:</strong> TZS ' + todayMobile.toFixed(2) + '</p>';
    html += '</div>';

    html += '<div class="p-4 bg-gray-50 rounded-xl">';
    html += '<h4 class="font-bold text-lg text-primary-900 mb-3">Current Shift</h4>';
    html += '<p class="mb-2"><strong>Total:</strong> TZS ' + shiftTotal.toFixed(2) + '</p>';
    html += '<p class="mb-2"><strong>Items:</strong> ' + shiftItemsCount + '</p>';
    html += '<p class="mb-2"><strong>Cash:</strong> TZS ' + shiftCash.toFixed(2) + '</p>';
    html += '<p class="mb-2"><strong>Card:</strong> TZS ' + shiftCard.toFixed(2) + '</p>';
    html += '<p><strong>Mobile:</strong> TZS ' + shiftMobile.toFixed(2) + '</p>';
    html += '</div>';
    html += '</div>';

    html += '<div class="mt-6">';
    html += '<h4 class="font-bold text-lg text-primary-900 mb-3">All Transactions</h4>';
    if (dashboardData.transactions.length > 0) {
        html += '<div class="overflow-x-auto">';
        html += '<table class="min-w-full divide-y divide-gray-200">';
        html += '<thead class="bg-gray-50">';
        html += '<tr>';
        html += '<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Invoice</th>';
        html += '<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Time</th>';
        html += '<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Items</th>';
        html += '<th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Method</th>';
        html += '<th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">Total</th>';
        html += '</tr>';
        html += '</thead>';
        html += '<tbody class="bg-white divide-y divide-gray-200">';
        dashboardData.transactions.forEach(t => {
            html += '<tr>';
            html += '<td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">' + t.invoice_number + '</td>';
            html += '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">' + t.created_at + '</td>';
            html += '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">' + t.items_count + '</td>';
            html += '<td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">' + t.payment_method.toUpperCase() + '</td>';
            html += '<td class="px-4 py-3 whitespace-nowrap text-sm text-right font-semibold text-gray-900">TZS ' + parseFloat(t.total).toFixed(2) + '</td>';
            html += '</tr>';
        });
        html += '</tbody>';
        html += '</table>';
        html += '</div>';
    } else {
        html += '<p class="text-gray-500">No transactions yet</p>';
    }
    html += '</div>';

    detailsContent.innerHTML = html;
    document.getElementById('detailsModal').classList.remove('hidden');
}

function hideAllDetails() {
    document.getElementById('detailsModal').classList.add('hidden');
}

function showNotification(message, type) {
    const notification = document.createElement('div');
    notification.className = 'fixed bottom-4 right-4 px-6 py-4 rounded-lg shadow-lg z-50 ' + 
        (type === 'success' ? 'bg-green-500 text-white' : 'bg-red-500 text-white');
    notification.textContent = message;
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 3000);
}

// Held Sales Functions
function holdSale() {
    if (cart.length === 0) {
        showNotification('Cart is empty!', 'error');
        return;
    }

    const heldSales = JSON.parse(localStorage.getItem('heldSales') || '[]');
    const heldSale = {
        id: Date.now(),
        timestamp: new Date().toLocaleString(),
        cart: [...cart],
        customerId: document.getElementById('customerSelect').value,
        discountType: document.getElementById('discountType').value,
        discountValue: parseFloat(document.getElementById('discountInput').value) || 0
    };

    heldSales.push(heldSale);
    localStorage.setItem('heldSales', JSON.stringify(heldSales));

    cart = [];
    renderCart();
    showNotification('Sale held successfully!', 'success');
}

function showHeldSalesModal() {
    const heldSales = JSON.parse(localStorage.getItem('heldSales') || '[]');
    const content = document.getElementById('heldSalesModalContent');

    if (heldSales.length === 0) {
        content.innerHTML = '<p class="text-gray-500 text-center py-8">No held sales</p>';
    } else {
        content.innerHTML = heldSales.map((sale, index) => `
            <div class="border border-gray-200 rounded-xl p-4 hover:border-gray-300 transition-colors">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-3">
                    <div>
                        <p class="font-semibold text-primary-900">Held Sale #${sale.id}</p>
                        <p class="text-sm text-gray-500">${sale.timestamp}</p>
                    </div>
                    <div class="flex flex-col sm:flex-row gap-2 w-full sm:w-auto">
                        <button onclick="retrieveSale(${index})" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-medium">
                            <i class="fas fa-folder-open mr-1"></i>Retrieve
                        </button>
                        <button onclick="deleteHeldSale(${index})" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-sm font-medium">
                            <i class="fas fa-trash mr-1"></i>Delete
                        </button>
                    </div>
                </div>
                <div class="text-sm text-gray-600">
                    <p class="font-medium mb-1">${sale.cart.length} items</p>
                </div>
            </div>
        `).join('');
    }

    document.getElementById('heldSalesModal').classList.remove('hidden');
}

function hideHeldSalesModal() {
    document.getElementById('heldSalesModal').classList.add('hidden');
}

function retrieveSale(index) {
    const heldSales = JSON.parse(localStorage.getItem('heldSales') || '[]');
    const sale = heldSales[index];

    cart = [...sale.cart];
    document.getElementById('discountType').value = sale.discountType;
    document.getElementById('discountInput').value = sale.discountValue;
    
    // Restore customer selection
    if (sale.customerId) {
        const customer = customersData.find(c => c.id === sale.customerId);
        if (customer) {
            const displayText = customer.name + ' (' + (customer.phone || 'No phone') + ')';
            selectCustomer(customer.id, displayText);
        } else {
            selectCustomer(null, '');
        }
    } else {
        selectCustomer(null, '');
    }

    // Remove from held sales
    heldSales.splice(index, 1);
    localStorage.setItem('heldSales', JSON.stringify(heldSales));

    hideHeldSalesModal();
    renderCart();
    showNotification('Sale retrieved successfully!', 'success');
}

function deleteHeldSale(index) {
    if (confirm('Delete this held sale?')) {
        const heldSales = JSON.parse(localStorage.getItem('heldSales') || '[]');
        heldSales.splice(index, 1);
        localStorage.setItem('heldSales', JSON.stringify(heldSales));
        showHeldSalesModal();
        showNotification('Held sale deleted', 'success');
    }
}

function cancelSale() {
    if (cart.length === 0) {
        return;
    }

    if (confirm('Cancel this sale? All items will be cleared.')) {
        cart = [];
        document.getElementById('discountInput').value = '';
        selectCustomer(null, '');
        renderCart();
        showNotification('Sale cancelled', 'success');
    }
}
</script>
@endsection
