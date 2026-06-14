@extends('layouts.app')

@section('page-title', 'Purchases Analytics')

@section('content')
<div class="animate-[fadeIn_0.4s_ease] space-y-6">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <h1 class="text-xl font-bold" :class="darkMode?'text-white':'text-primary-900'">Purchases Analytics</h1>
        
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
            <div class="text-sm" :class="darkMode?'text-primary-400':'text-gray-500'">PO Amount</div>
            <div class="text-lg sm:text-xl md:text-2xl font-bold break-all" :class="darkMode?'text-white':'text-primary-900'">TZS {{ number_format($filteredPOAmount, 2) }}</div>
            <div class="text-xs mt-1" :class="darkMode?'text-primary-500':'text-gray-400'">{{ $filteredPOCount }} PO(s)</div>
        </div>
        <div class="card rounded-2xl p-5 overflow-hidden">
            <div class="text-sm" :class="darkMode?'text-primary-400':'text-gray-500'">GRNs</div>
            <div class="text-lg sm:text-xl md:text-2xl font-bold break-all" :class="darkMode?'text-white':'text-primary-900'">{{ $filteredGRN }}</div>
        </div>
        <div class="card rounded-2xl p-5 overflow-hidden">
            <div class="text-sm" :class="darkMode?'text-primary-400':'text-gray-500'">Payments</div>
            <div class="text-lg sm:text-xl md:text-2xl font-bold break-all" :class="darkMode?'text-white':'text-primary-900'">TZS {{ number_format($filteredPayments, 2) }}</div>
        </div>
    </div>

    <!-- Charts Section 1 -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Purchases Trend -->
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">Purchases Trend (Last 30 Days)</h3>
            <canvas id="purchasesTrendChart" height="200"></canvas>
        </div>

        <!-- PO Status Breakdown -->
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">PO Status Breakdown</h3>
            <canvas id="poStatusChart" height="200"></canvas>
        </div>
    </div>

    <!-- Top Suppliers -->
    <div class="card rounded-2xl p-5">
        <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">Top Suppliers (This Month)</h3>
        <canvas id="topSuppliersChart" height="200"></canvas>
    </div>

    <!-- Recent POs & Payments -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Recent POs -->
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">Recent Purchase Orders</h3>
            <div class="space-y-3">
                @foreach($recentPOs as $po)
                <div class="flex items-center justify-between p-3 rounded-xl" :class="darkMode?'bg-primary-900/20':'bg-primary-50'">
                    <div>
                        <p class="font-semibold text-sm" :class="darkMode?'text-white':'text-primary-900'">PO #{{ $po->id }}</p>
                        <p class="text-xs" :class="darkMode?'text-primary-400':'text-gray-500'">{{ $po->supplier->name ?? 'N/A' }}</p>
                    </div>
                    <div class="font-mono" :class="darkMode?'text-white':'text-primary-900'">TZS {{ number_format($po->total, 2) }}</div>
                </div>
                @endforeach
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">Recent Payments</h3>
            <div class="space-y-3">
                @foreach($recentPayments as $payment)
                <div class="flex items-center justify-between p-3 rounded-xl" :class="darkMode?'bg-primary-900/20':'bg-primary-50'">
                    <div>
                        <p class="font-semibold text-sm" :class="darkMode?'text-white':'text-primary-900'">{{ $payment->supplier->name ?? 'N/A' }}</p>
                        <p class="text-xs" :class="darkMode?'text-primary-400':'text-gray-500'">{{ $payment->created_at->format('M d, Y') }}</p>
                    </div>
                    <div class="font-mono" :class="darkMode?'text-white':'text-primary-900'">TZS {{ number_format($payment->amount, 2) }}</div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
    // Purchases Trend Chart
    new Chart(document.getElementById('purchasesTrendChart'), {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [{
                label: 'Purchases',
                data: @json($purchaseData),
                borderColor: '#f59e0b',
                backgroundColor: 'rgba(245, 158, 11, 0.1)',
                fill: true,
                tension: 0.3
            }]
        }
    });

    // PO Status Chart
    new Chart(document.getElementById('poStatusChart'), {
        type: 'pie',
        data: {
            labels: @json($poStatusBreakdown->pluck('status')),
            datasets: [{
                data: @json($poStatusBreakdown->pluck('count')),
                backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#ef4444']
            }]
        }
    });

    // Top Suppliers Chart
    new Chart(document.getElementById('topSuppliersChart'), {
        type: 'bar',
        data: {
            labels: @json($topSuppliers->pluck('name')),
            datasets: [{
                label: 'Total Spent',
                data: @json($topSuppliers->pluck('total_spent')),
                backgroundColor: 'rgba(245, 158, 11, 0.7)',
                borderRadius: 5
            }]
        }
    });
</script>
@endsection
