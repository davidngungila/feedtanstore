@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<div class="animate-[fadeIn_0.4s_ease] space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold" :class="darkMode?'text-white':'text-primary-900'">Good morning, {{ auth()->user()->name ?? 'Admin' }} 👋</h2>
            <p class="text-sm mt-0.5" :class="darkMode?'text-primary-400':'text-primary-600'">Here's what's happening today.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-mono" :class="darkMode?'text-primary-400':'text-primary-500'">{{ date('l, F j, Y') }}</span>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card rounded-2xl p-5">
        <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">Quick Actions</h3>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            <a href="{{ route('sales.new') }}" class="flex items-center gap-3 p-4 rounded-xl bg-gradient-to-r from-primary-600 to-primary-700 text-white hover:from-primary-700 hover:to-primary-800 transition-all transform hover:scale-105">
                <i class="fa-solid fa-cash-register text-2xl"></i>
                <span class="font-semibold text-sm">New Sale</span>
            </a>
            <a href="{{ route('inventory.products.create') }}" class="flex items-center gap-3 p-4 rounded-xl bg-gradient-to-r from-green-600 to-green-700 text-white hover:from-green-700 hover:to-green-800 transition-all transform hover:scale-105">
                <i class="fa-solid fa-box text-2xl"></i>
                <span class="font-semibold text-sm">Add Product</span>
            </a>
            <a href="{{ route('purchasing.suppliers.create') }}" class="flex items-center gap-3 p-4 rounded-xl bg-gradient-to-r from-blue-600 to-blue-700 text-white hover:from-blue-700 hover:to-blue-800 transition-all transform hover:scale-105">
                <i class="fa-solid fa-truck text-2xl"></i>
                <span class="font-semibold text-sm">Add Supplier</span>
            </a>
            <a href="{{ route('customers.create') }}" class="flex items-center gap-3 p-4 rounded-xl bg-gradient-to-r from-purple-600 to-purple-700 text-white hover:from-purple-700 hover:to-purple-800 transition-all transform hover:scale-105">
                <i class="fa-solid fa-user-plus text-2xl"></i>
                <span class="font-semibold text-sm">Add Customer</span>
            </a>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card card" style="animation-delay:0s">
            <div class="bg-blob rounded-full" style="background:#10b981"></div>
            <div class="flex items-start justify-between mb-4">
                <div class="icon-wrap" style="background:#10b98122">
                    <i class="fa-solid fa-dollar-sign" style="color:#10b981"></i>
                </div>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" :class="darkMode?'text-primary-400':'text-primary-500'">Sales</p>
                <p class="text-xl font-bold font-mono" :class="darkMode?'text-white':'text-primary-900'">TZS {{ number_format($todayRevenue, 2) }}</p>
                <p class="text-[11px] mt-1" :class="darkMode?'text-primary-500':'text-gray-500'">{{ $todaySales->count() }} transactions</p>
            </div>
        </div>
        <div class="stat-card card" style="animation-delay:80ms">
            <div class="bg-blob rounded-full" style="background:#3b82f6"></div>
            <div class="flex items-start justify-between mb-4">
                <div class="icon-wrap" style="background:#3b82f622">
                    <i class="fa-solid fa-wallet" style="color:#3b82f6"></i>
                </div>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" :class="darkMode?'text-primary-400':'text-primary-500'">Items Sold</p>
                <p class="text-xl font-bold font-mono" :class="darkMode?'text-white':'text-primary-900'">{{ $todayItems }}</p>
                <p class="text-[11px] mt-1" :class="darkMode?'text-primary-500':'text-gray-500'">Today</p>
            </div>
        </div>
        <div class="stat-card card" style="animation-delay:160ms">
            <div class="bg-blob rounded-full" style="background:#f59e0b"></div>
            <div class="flex items-start justify-between mb-4">
                <div class="icon-wrap" style="background:#f59e0b22">
                    <i class="fa-solid fa-box" style="color:#f59e0b"></i>
                </div>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" :class="darkMode?'text-primary-400':'text-primary-500'">Stock</p>
                <p class="text-xl font-bold font-mono" :class="darkMode?'text-white':'text-primary-900'">{{ $totalProducts }}</p>
                <p class="text-[11px] mt-1" :class="darkMode?'text-primary-500':'text-gray-500'">Total Products</p>
            </div>
        </div>
        <div class="stat-card card" style="animation-delay:240ms">
            <div class="bg-blob rounded-full" style="background:#8b5cf6"></div>
            <div class="flex items-start justify-between mb-4">
                <div class="icon-wrap" style="background:#8b5cf622">
                    <i class="fa-solid fa-users" style="color:#8b5cf6"></i>
                </div>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" :class="darkMode?'text-primary-400':'text-primary-500'">Customers</p>
                <p class="text-xl font-bold font-mono" :class="darkMode?'text-white':'text-primary-900'">{{ $totalCustomers }}</p>
                <p class="text-[11px] mt-1" :class="darkMode?'text-primary-500':'text-gray-500'">Registered</p>
            </div>
        </div>
    </div>

    <!-- Charts Row 1: Sales Line & Payment Pie & Target Gauge -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Sales Trend Chart (Line) -->
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">📈 Sales Trend (Last 7 Days)</h3>
            <canvas id="salesChart" height="200"></canvas>
        </div>

        <!-- Payment Methods Chart (Pie) -->
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">🥧 Payment Methods</h3>
            <canvas id="paymentChart" height="200"></canvas>
        </div>

        <!-- Target Achievement (Gauge Chart) -->
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">🎯 Monthly Target</h3>
            <div class="flex flex-col items-center justify-center">
                <div class="relative w-48 h-24 overflow-hidden">
                    <div class="absolute bottom-0 w-full h-48 border-8 border-gray-200 rounded-t-full" style="border-bottom: none;"></div>
                    <div id="gaugeFill" class="absolute bottom-0 w-full h-48 rounded-t-full transition-all duration-1000" style="border: none; transform-origin: bottom center;"></div>
                    <div class="absolute inset-0 flex items-end justify-center">
                        <div class="bg-white dark:bg-gray-800 w-40 h-20 rounded-t-full flex items-center justify-center mb-1">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-primary-600">{{ $targetPercentage }}%</div>
                                <div class="text-xs text-gray-500">TZS {{ number_format($thisMonthRevenue, 0, ',', '.') }} / {{ number_format($monthlyTarget, 0, ',', '.') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row 2: Top Products Bar & Stock Doughnut -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Top Selling Products (Bar Chart) -->
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">📊 Top Selling Products</h3>
            <canvas id="topProductsChart" height="200"></canvas>
        </div>

        <!-- Stock Status (Doughnut Chart) -->
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">🍩 Stock Status</h3>
            <canvas id="stockChart" height="200"></canvas>
        </div>
    </div>

    <!-- Charts Row 3: Sales by Category & Cashier Performance -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Sales by Category (Bar) -->
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">📊 Sales by Category</h3>
            <canvas id="categoryChart" height="200"></canvas>
        </div>

        <!-- Cashier Performance (Bar) -->
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">🎯 Cashier Performance (This Month)</h3>
            <canvas id="cashierChart" height="200"></canvas>
        </div>
    </div>

    <!-- Charts Row 4: Combo Chart & Treemap -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Combo Chart: Sales & Items (Combo) -->
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">📊 Sales & Items Sold (Last 7 Days)</h3>
            <canvas id="comboChart" height="200"></canvas>
        </div>

        <!-- Treemap: Category Treemap -->
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">📦 Category Sales Distribution</h3>
            <div id="categoryTreemap" class="grid grid-cols-1 gap-2">
                @foreach($salesByCategory as $category)
                @php $opacity = $categoryTotalSales > 0 ? ($category->total / $categoryTotalSales) * 0.8 + 0.2 : 1; @endphp
                <div class="flex items-center justify-between p-3 rounded-lg" style="background: rgba(139, 92, 246, {{ $opacity }});">
                    <span class="font-semibold text-white">{{ $category->name }}</span>
                    <span class="text-white font-mono text-sm">TZS {{ number_format($category->total, 0) }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Notifications / Alerts Section -->
    <div class="card rounded-2xl p-5">
        <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">🔔 Important Alerts</h3>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <!-- Out of Stock -->
            <div class="space-y-2">
                <h4 class="text-xs font-semibold uppercase tracking-wider text-red-600">🚨 Out of Stock ({{ $outOfStockCount }})</h4>
                @if($outOfStockCount > 0)
                <div class="space-y-2 max-h-32 overflow-y-auto">
                    @foreach($outOfStockProducts as $product)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-red-50 dark:bg-red-900/20">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-red-200 flex items-center justify-center">
                                <i class="fa-solid fa-circle-xmark text-red-700"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-red-900">{{ $product->name }}</p>
                                <p class="text-[10px] text-red-700">Qty: {{ $product->quantity }}</p>
                            </div>
                        </div>
                        <a href="{{ route('inventory.products.show', $product) }}" class="text-xs text-red-600 hover:text-red-800">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-xs text-gray-500">No products out of stock! 🎉</p>
                @endif
            </div>

            <!-- Low Stock -->
            <div class="space-y-2">
                <h4 class="text-xs font-semibold uppercase tracking-wider text-amber-600">⚠️ Low Stock ({{ $lowStockCount }})</h4>
                @if($lowStockCount > 0)
                <div class="space-y-2 max-h-32 overflow-y-auto">
                    @foreach($lowStockProducts->take(5) as $product)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-amber-200 flex items-center justify-center">
                                <i class="fa-solid fa-triangle-exclamation text-amber-700"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-amber-900">{{ $product->name }}</p>
                                <p class="text-[10px] text-amber-700">Qty: {{ $product->quantity }} / Reorder: {{ $product->reorder_level }}</p>
                            </div>
                        </div>
                        <a href="{{ route('inventory.products.show', $product) }}" class="text-xs text-amber-600 hover:text-amber-800">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-xs text-gray-500">All products are well stocked! 🎉</p>
                @endif
            </div>

            <!-- Expiring Soon -->
            <div class="space-y-2">
                <h4 class="text-xs font-semibold uppercase tracking-wider text-orange-600">⏰ Expiring Soon ({{ $expiringCount }})</h4>
                @if($expiringCount > 0)
                <div class="space-y-2 max-h-32 overflow-y-auto">
                    @foreach($expiringProducts as $product)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-orange-50 dark:bg-orange-900/20">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-orange-200 flex items-center justify-center">
                                <i class="fa-solid fa-clock text-orange-700"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-orange-900">{{ $product->name }}</p>
                                <p class="text-[10px] text-orange-700">Expires: {{ $product->expiry_date->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('inventory.products.show', $product) }}" class="text-xs text-orange-600 hover:text-orange-800">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-xs text-gray-500">No products expiring soon! 🎉</p>
                @endif
            </div>

            <!-- Expired -->
            <div class="space-y-2">
                <h4 class="text-xs font-semibold uppercase tracking-wider text-red-700">💀 Expired Products ({{ $expiredCount }})</h4>
                @if($expiredCount > 0)
                <div class="space-y-2 max-h-32 overflow-y-auto">
                    @foreach($expiredProducts as $product)
                    <div class="flex items-center justify-between p-3 rounded-xl bg-red-100 dark:bg-red-900/30">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-red-300 flex items-center justify-center">
                                <i class="fa-solid fa-skull-crossbones text-red-800"></i>
                            </div>
                            <div>
                                <p class="text-xs font-semibold text-red-900">{{ $product->name }}</p>
                                <p class="text-[10px] text-red-700">Expired: {{ $product->expiry_date->format('M d, Y') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('inventory.products.show', $product) }}" class="text-xs text-red-700 hover:text-red-900">
                            <i class="fa-solid fa-eye"></i>
                        </a>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-xs text-gray-500">No expired products! 🎉</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Recent Transactions -->
    <div class="card rounded-2xl p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-bold text-sm" :class="darkMode?'text-white':'text-primary-900'">Recent Transactions</h3>
            <a href="{{ route('sales.history') }}" class="text-xs text-primary-600 hover:text-primary-800">View all</a>
        </div>
        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th>Invoice</th>
                        <th>Customer</th>
                        <th>Cashier</th>
                        <th>Amount</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentSales as $sale)
                    <tr class="table-row">
                        <td class="font-mono text-xs">
                            <a href="{{ route('sales.show', $sale) }}" class="text-primary-600 hover:text-primary-800">{{ $sale->invoice_number }}</a>
                        </td>
                        <td>{{ $sale->customer->name ?? 'Walk-in' }}</td>
                        <td>{{ $sale->user->name ?? '-' }}</td>
                        <td class="font-mono">TZS {{ number_format($sale->total, 2) }}</td>
                        <td class="text-xs">{{ $sale->created_at->format('M d, H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Low Stock & Quick Info -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <!-- Low Stock Products -->
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">⚠️ Low Stock Products</h3>
            @if($lowStockProducts->count() > 0)
            <div class="space-y-3">
                @foreach($lowStockProducts->take(5) as $product)
                <div class="flex items-center justify-between p-3 rounded-xl" :class="darkMode?'bg-primary-900/20':'bg-primary-50'">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-red-200 flex items-center justify-center">
                            <i class="fa-solid fa-triangle-exclamation text-red-700"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold" :class="darkMode?'text-white':'text-primary-900'">{{ $product->name }}</p>
                            <p class="text-xs" :class="darkMode?'text-primary-400':'text-gray-500'">Qty: {{ $product->quantity }} / Reorder: {{ $product->reorder_level }}</p>
                        </div>
                    </div>
                    <a href="{{ route('inventory.products.show', $product) }}" class="text-xs text-primary-600 hover:text-primary-800">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                </div>
                @endforeach
            </div>
            @else
            <p class="text-center text-gray-500 py-8">All products are well stocked! 🎉</p>
            @endif
        </div>

        <!-- System Activity / Quick Stats -->
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">📊 Quick Stats</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 rounded-xl" :class="darkMode?'bg-primary-900/20':'bg-primary-50'">
                    <span class="text-sm" :class="darkMode?'text-white':'text-primary-900'">This Month's Sales</span>
                    <span class="font-bold" :class="darkMode?'text-white':'text-primary-900'">TZS {{ number_format($thisMonthRevenue, 2) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl" :class="darkMode?'bg-primary-900/20':'bg-primary-50'">
                    <span class="text-sm" :class="darkMode?'text-white':'text-primary-900'">Total Revenue</span>
                    <span class="font-bold" :class="darkMode?'text-white':'text-primary-900'">TZS {{ number_format($totalRevenue, 2) }}</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl" :class="darkMode?'bg-primary-900/20':'bg-primary-50'">
                    <span class="text-sm" :class="darkMode?'text-white':'text-primary-900'">Out of Stock</span>
                    <span class="font-bold text-red-600">{{ $outOfStockCount }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    // Gauge Chart Animation
    const targetPercentage = {{ $targetPercentage }};
    const gaugeFill = document.getElementById('gaugeFill');
    const rotation = (targetPercentage / 100) * 180 - 90;
    const color = targetPercentage < 30 ? '#ef4444' : targetPercentage < 70 ? '#f59e0b' : '#10b981';
    gaugeFill.style.border = '8px solid ' + color;
    gaugeFill.style.borderBottom = 'none';
    gaugeFill.style.transform = `rotate(${rotation}deg)`;

    // Sales Trend (Line Chart)
    const salesCtx = document.getElementById('salesChart').getContext('2d');
    const salesChart = new Chart(salesCtx, {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [{
                label: 'Sales (TZS)',
                data: @json($salesData),
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.4,
                fill: true,
                pointBackgroundColor: '#22c55e',
                pointBorderColor: '#ffffff',
                pointBorderWidth: 2,
                pointRadius: 5,
                pointHoverRadius: 7
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Payment Methods (Pie Chart)
    const paymentCtx = document.getElementById('paymentChart').getContext('2d');
    const paymentChart = new Chart(paymentCtx, {
        type: 'pie',
        data: {
            labels: @json($paymentMethods->pluck('payment_method')),
            datasets: [{
                data: @json($paymentMethods->pluck('count')),
                backgroundColor: [
                    '#10b981',
                    '#3b82f6',
                    '#f59e0b',
                    '#8b5cf6',
                    '#ec4899',
                    '#06b6d4'
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Top Products (Bar Chart)
    const topProductsCtx = document.getElementById('topProductsChart').getContext('2d');
    const topProductsChart = new Chart(topProductsCtx, {
        type: 'bar',
        data: {
            labels: @json($topProducts->pluck('product.name')),
            datasets: [{
                label: 'Units Sold',
                data: @json($topProducts->pluck('total_quantity')),
                backgroundColor: [
                    'rgba(34, 197, 94, 0.8)',
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(139, 92, 246, 0.8)',
                    'rgba(236, 72, 153, 0.8)'
                ],
                borderColor: [
                    '#22c55e',
                    '#3b82f6',
                    '#f59e0b',
                    '#8b5cf6',
                    '#ec4899'
                ],
                borderWidth: 2,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Stock Status (Doughnut Chart)
    const stockCtx = document.getElementById('stockChart').getContext('2d');
    const stockChart = new Chart(stockCtx, {
        type: 'doughnut',
        data: {
            labels: ['In Stock', 'Low Stock', 'Out of Stock'],
            datasets: [{
                data: [{{ $stockStatus['in_stock'] }}, {{ $stockStatus['low_stock'] }}, {{ $stockStatus['out_of_stock'] }}],
                backgroundColor: [
                    '#10b981',
                    '#f59e0b',
                    '#ef4444'
                ],
                borderWidth: 3,
                borderColor: '#ffffff',
                cutout: '60%'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Sales by Category (Bar Chart)
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    const categoryChart = new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: @json($salesByCategory->pluck('name')),
            datasets: [{
                label: 'Sales (TZS)',
                data: @json($salesByCategory->pluck('total')),
                backgroundColor: 'rgba(139, 92, 246, 0.7)',
                borderColor: '#8b5cf6',
                borderWidth: 2,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Cashier Performance (Bar Chart)
    const cashierCtx = document.getElementById('cashierChart').getContext('2d');
    const cashierChart = new Chart(cashierCtx, {
        type: 'bar',
        data: {
            labels: @json($cashierPerformance->pluck('name')),
            datasets: [{
                label: 'Sales Amount',
                data: @json($cashierPerformance->pluck('sales_sum_total')),
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderColor: '#3b82f6',
                borderWidth: 2,
                borderRadius: 5
            }, {
                label: 'Number of Sales',
                data: @json($cashierPerformance->pluck('sales_count')),
                backgroundColor: 'rgba(245, 158, 11, 0.7)',
                borderColor: '#f59e0b',
                borderWidth: 2,
                borderRadius: 5
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });

    // Combo Chart: Sales & Items
    const comboCtx = document.getElementById('comboChart').getContext('2d');
    const comboChart = new Chart(comboCtx, {
        type: 'bar',
        data: {
            labels: @json($labels),
            datasets: [{
                type: 'line',
                label: 'Sales (TZS)',
                data: @json($salesData),
                borderColor: '#22c55e',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.4,
                fill: true,
                yAxisID: 'y'
            }, {
                type: 'bar',
                label: 'Items Sold',
                data: @json($itemsSoldData),
                backgroundColor: 'rgba(59, 130, 246, 0.6)',
                borderColor: '#3b82f6',
                borderWidth: 2,
                borderRadius: 5,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            },
            scales: {
                y: {
                    type: 'linear',
                    display: true,
                    position: 'left',
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0,0,0,0.05)'
                    }
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
</script>
@endsection