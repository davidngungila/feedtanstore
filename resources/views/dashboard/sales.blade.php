@extends('layouts.app')

@section('page-title', 'Sales Analytics')

@section('content')
<div class="animate-[fadeIn_0.4s_ease] space-y-6">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <h1 class="text-xl font-bold" :class="darkMode?'text-white':'text-primary-900'">Sales Analytics</h1>
        
        <!-- Custom Date Range Filter -->
        <form id="date-range-form" class="flex flex-wrap items-center gap-3" method="GET">
            <label class="text-sm font-medium" :class="darkMode?'text-gray-300':'text-gray-600'">From:</label>
            <input type="date" name="start_date" value="{{ $startDate }}" class="px-3 py-2 rounded-lg text-sm border focus:outline-none focus:ring-2 focus:ring-primary-500" :class="darkMode?'bg-primary-900 border-primary-800 text-white':'bg-white border-gray-200 text-gray-900'">
            
            <label class="text-sm font-medium" :class="darkMode?'text-gray-300':'text-gray-600'">To:</label>
            <input type="date" name="end_date" value="{{ $endDate }}" class="px-3 py-2 rounded-lg text-sm border focus:outline-none focus:ring-2 focus:ring-primary-500" :class="darkMode?'bg-primary-900 border-primary-800 text-white':'bg-white border-gray-200 text-gray-900'">
            
            <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">
                Apply
            </button>
        </form>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
        <div class="card rounded-2xl p-5 overflow-hidden">
            <div class="text-sm" :class="darkMode?'text-primary-400':'text-gray-500'">Revenue</div>
            <div class="text-lg sm:text-xl md:text-2xl font-bold break-all" :class="darkMode?'text-white':'text-primary-900'">TZS {{ number_format($filteredRevenue, 2) }}</div>
            <div class="text-xs mt-1" :class="darkMode?'text-primary-500':'text-gray-400'">{{ $filteredTransactions }} transactions</div>
        </div>
        <div class="card rounded-2xl p-5 overflow-hidden">
            <div class="text-sm" :class="darkMode?'text-primary-400':'text-gray-500'">Items Sold</div>
            <div class="text-lg sm:text-xl md:text-2xl font-bold break-all" :class="darkMode?'text-white':'text-primary-900'">{{ $filteredItems }}</div>
        </div>
        <div class="card rounded-2xl p-5 overflow-hidden">
            <div class="text-sm" :class="darkMode?'text-primary-400':'text-gray-500'">Returns</div>
            <div class="text-lg sm:text-xl md:text-2xl font-bold break-all" :class="darkMode?'text-white':'text-red-600'">TZS {{ number_format($returnsAmount, 2) }}</div>
            <div class="text-xs mt-1" :class="darkMode?'text-primary-500':'text-gray-400'">{{ $returnsCount }} returns</div>
        </div>
    </div>

    <!-- Charts Section 1 -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Sales Trend -->
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">Sales Trend (Last 30 Days)</h3>
            <canvas id="salesTrendChart" height="200"></canvas>
        </div>

        <!-- Payment Methods -->
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">Payment Methods</h3>
            <canvas id="paymentMethodsChart" height="200"></canvas>
        </div>
    </div>

    <!-- Charts Section 2 -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Top Products -->
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">Top Products (This Month)</h3>
            <canvas id="topProductsChart" height="200"></canvas>
        </div>

        <!-- Top Customers -->
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">Top Customers (This Month)</h3>
            <canvas id="topCustomersChart" height="200"></canvas>
        </div>
    </div>

    <!-- Tables Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Top Products List -->
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">Top Products</h3>
            <div class="space-y-3">
                @foreach($topProducts as $product)
                <div class="flex items-center justify-between p-3 rounded-xl" :class="darkMode?'bg-primary-900/20':'bg-primary-50'">
                    <div>
                        <p class="font-semibold text-sm" :class="darkMode?'text-white':'text-primary-900'">{{ $product->product->name ?? 'N/A' }}</p>
                        <p class="text-xs" :class="darkMode?'text-primary-400':'text-gray-500'">Sold: {{ $product->total_quantity }}</p>
                    </div>
                    <div class="font-mono" :class="darkMode?'text-white':'text-primary-900'">TZS {{ number_format($product->total_amount, 2) }}</div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Top Customers List -->
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">Top Customers</h3>
            <div class="space-y-3">
                @foreach($topCustomers as $customer)
                <div class="flex items-center justify-between p-3 rounded-xl" :class="darkMode?'bg-primary-900/20':'bg-primary-50'">
                    <div>
                        <p class="font-semibold text-sm" :class="darkMode?'text-white':'text-primary-900'">{{ $customer->name }}</p>
                        <p class="text-xs" :class="darkMode?'text-primary-400':'text-gray-500'">{{ $customer->transactions }} transactions</p>
                    </div>
                    <div class="font-mono" :class="darkMode?'text-white':'text-primary-900'">TZS {{ number_format($customer->total_spent, 2) }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Sales By Hour -->
    <div class="card rounded-2xl p-5">
        <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">Sales By Hour (Today)</h3>
        <canvas id="salesByHourChart" height="100"></canvas>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    // Sales Trend Chart
    new Chart(document.getElementById('salesTrendChart'), {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [{
                label: 'Sales',
                data: @json($salesData),
                borderColor: '#10b981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                fill: true,
                tension: 0.3
            }]
        }
    });

    // Payment Methods Chart
    new Chart(document.getElementById('paymentMethodsChart'), {
        type: 'pie',
        data: {
            labels: @json($paymentMethods->pluck('payment_method')),
            datasets: [{
                data: @json($paymentMethods->pluck('count')),
                backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#ef4444']
            }]
        }
    });

    // Top Products Chart
    new Chart(document.getElementById('topProductsChart'), {
        type: 'bar',
        data: {
            labels: @json($topProducts->pluck('product.name')),
            datasets: [{
                label: 'Units Sold',
                data: @json($topProducts->pluck('total_quantity')),
                backgroundColor: 'rgba(139, 92, 246, 0.7)',
                borderRadius: 5
            }]
        }
    });

    // Top Customers Chart
    new Chart(document.getElementById('topCustomersChart'), {
        type: 'bar',
        data: {
            labels: @json($topCustomers->pluck('name')),
            datasets: [{
                label: 'Total Spent',
                data: @json($topCustomers->pluck('total_spent')),
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderRadius: 5
            }]
        }
    });

    // Sales By Hour Chart
    new Chart(document.getElementById('salesByHourChart'), {
        type: 'bar',
        data: {
            labels: Array.from({length: 24}, (_, i) => `${i}:00`),
            datasets: [{
                label: 'Sales',
                data: @json($salesByHour),
                backgroundColor: 'rgba(16, 185, 129, 0.7)'
            }]
        }
    });
</script>
@endsection
