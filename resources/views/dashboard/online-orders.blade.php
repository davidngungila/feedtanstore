@extends('layouts.app')

@section('page-title', 'Online Orders Analytics')

@section('content')
<div class="animate-[fadeIn_0.4s_ease] space-y-6">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <h1 class="text-xl font-bold" :class="darkMode?'text-white':'text-primary-900'">Online Orders Analytics</h1>
        
        <!-- Date Filter -->
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium" :class="darkMode?'text-gray-300':'text-gray-600'">Filter:</label>
            <select id="date-filter" onchange="window.location.href = this.value" class="px-3 py-2 rounded-lg text-sm border focus:outline-none focus:ring-2 focus:ring-primary-500" :class="darkMode?'bg-primary-900 border-primary-800 text-white':'bg-white border-gray-200 text-gray-900'">
                <option value="{{ route('dashboard.online-orders', ['filter' => 'day']) }}" {{ $filter === 'day' ? 'selected' : '' }}>Day</option>
                <option value="{{ route('dashboard.online-orders', ['filter' => 'week']) }}" {{ $filter === 'week' ? 'selected' : '' }}>Week</option>
                <option value="{{ route('dashboard.online-orders', ['filter' => 'month']) }}" {{ $filter === 'month' ? 'selected' : '' }}>Month</option>
                <option value="{{ route('dashboard.online-orders', ['filter' => '3months']) }}" {{ $filter === '3months' ? 'selected' : '' }}>3 Months</option>
                <option value="{{ route('dashboard.online-orders', ['filter' => '6months']) }}" {{ $filter === '6months' ? 'selected' : '' }}>6 Months</option>
                <option value="{{ route('dashboard.online-orders', ['filter' => 'year']) }}" {{ $filter === 'year' ? 'selected' : '' }}>Year</option>
            </select>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
        <div class="card rounded-2xl p-5">
            <div class="text-sm" :class="darkMode?'text-primary-400':'text-gray-500'">Online Revenue</div>
            <div class="text-2xl font-bold" :class="darkMode?'text-white':'text-primary-900'">TZS {{ number_format($filteredOnlineRevenue, 2) }}</div>
            <div class="text-xs mt-1" :class="darkMode?'text-primary-500':'text-gray-400'">{{ $filteredOnlineOrdersCount }} orders</div>
        </div>
        <div class="card rounded-2xl p-5">
            <div class="text-sm" :class="darkMode?'text-primary-400':'text-gray-500'">Items Sold</div>
            <div class="text-2xl font-bold" :class="darkMode?'text-white':'text-primary-900'">{{ $filteredOnlineItems }}</div>
        </div>
    </div>

    <!-- Charts Section 1 -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Online Sales Trend -->
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">Online Sales Trend (Last 30 Days)</h3>
            <canvas id="onlineSalesTrendChart" height="200"></canvas>
        </div>

        <!-- Order Status Breakdown -->
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">Order Status Breakdown</h3>
            <canvas id="orderStatusChart" height="200"></canvas>
        </div>
    </div>

    <!-- Charts Section 2 -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Top Online Products -->
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">Top Online Products</h3>
            <canvas id="topOnlineProductsChart" height="200"></canvas>
        </div>

        <!-- Rider Performance -->
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">Rider Performance</h3>
            <canvas id="riderPerformanceChart" height="200"></canvas>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="card rounded-2xl p-5">
        <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">Recent Online Orders</h3>
        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Status</th>
                        <th>Total</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($recentOnlineOrders as $order)
                    <tr class="table-row">
                        <td class="font-mono text-xs">
                            <a href="{{ route('online.orders.show', $order) }}" class="text-primary-600 hover:text-primary-800">{{ $order->id }}</a>
                        </td>
                        <td>{{ $order->customer_name ?? 'Guest' }}</td>
                        <td>
                            <span class="badge badge-{{ $order->status === 'delivered' ? 'green' : ($order->status === 'cancelled' ? 'red' : 'yellow') }}">
                                {{ ucfirst($order->status) }}
                            </span>
                        </td>
                        <td class="font-mono">TZS {{ number_format($order->total, 2) }}</td>
                        <td class="text-xs">{{ $order->created_at->format('M d, H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    // Online Sales Trend Chart
    new Chart(document.getElementById('onlineSalesTrendChart'), {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [{
                label: 'Online Sales',
                data: @json($onlineSalesData),
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                fill: true,
                tension: 0.3
            }]
        }
    });

    // Order Status Chart
    new Chart(document.getElementById('orderStatusChart'), {
        type: 'pie',
        data: {
            labels: @json($statusBreakdown->pluck('status')),
            datasets: [{
                data: @json($statusBreakdown->pluck('count')),
                backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#ef4444']
            }]
        }
    });

    // Top Online Products Chart
    new Chart(document.getElementById('topOnlineProductsChart'), {
        type: 'bar',
        data: {
            labels: @json($topOnlineProducts->pluck('product.name')),
            datasets: [{
                label: 'Units Sold',
                data: @json($topOnlineProducts->pluck('total_quantity')),
                backgroundColor: 'rgba(139, 92, 246, 0.7)',
                borderRadius: 5
            }]
        }
    });

    // Rider Performance Chart
    new Chart(document.getElementById('riderPerformanceChart'), {
        type: 'bar',
        data: {
            labels: @json($riderPerformance->pluck('name')),
            datasets: [{
                label: 'Orders Delivered',
                data: @json($riderPerformance->pluck('online_orders_count')),
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderRadius: 5
            }]
        }
    });
</script>
@endsection
