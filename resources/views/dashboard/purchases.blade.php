@extends('layouts.app')

@section('page-title', 'Purchases Analytics')

@section('content')
<div class="animate-[fadeIn_0.4s_ease] space-y-6">
    <div class="flex items-center justify-between">
        <h1 class="text-xl font-bold" :class="darkMode?'text-white':'text-primary-900'">Purchases Analytics</h1>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="card rounded-2xl p-5">
            <div class="text-sm" :class="darkMode?'text-primary-400':'text-gray-500'">Today's PO Amount</div>
            <div class="text-2xl font-bold" :class="darkMode?'text-white':'text-primary-900'">TZS {{ number_format($todayPOAmount, 2) }}</div>
            <div class="text-xs mt-1" :class="darkMode?'text-primary-500':'text-gray-400'">{{ $todayPOCount }} PO(s)</div>
        </div>
        <div class="card rounded-2xl p-5">
            <div class="text-sm" :class="darkMode?'text-primary-400':'text-gray-500'">Today's GRNs</div>
            <div class="text-2xl font-bold" :class="darkMode?'text-white':'text-primary-900'">{{ $todayGRN }}</div>
        </div>
        <div class="card rounded-2xl p-5">
            <div class="text-sm" :class="darkMode?'text-primary-400':'text-gray-500'">Today's Payments</div>
            <div class="text-2xl font-bold" :class="darkMode?'text-white':'text-primary-900'">TZS {{ number_format($todayPayments, 2) }}</div>
        </div>
        <div class="card rounded-2xl p-5">
            <div class="text-sm" :class="darkMode?'text-primary-400':'text-gray-500'">This Month's PO Amount</div>
            <div class="text-2xl font-bold" :class="darkMode?'text-white':'text-primary-900'">TZS {{ number_format($thisMonthPOAmount, 2) }}</div>
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
