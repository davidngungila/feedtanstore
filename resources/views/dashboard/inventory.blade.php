@extends('layouts.app')

@section('page-title', 'Inventory Analytics')

@section('content')
<div class="animate-[fadeIn_0.4s_ease] space-y-6">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <h1 class="text-xl font-bold" :class="darkMode?'text-white':'text-primary-900'">Inventory Analytics</h1>
        
        <!-- Date Filter -->
        <div class="flex items-center gap-2">
            <label class="text-sm font-medium" :class="darkMode?'text-gray-300':'text-gray-600'">Filter:</label>
            <select id="date-filter" onchange="window.location.href = this.value" class="px-3 py-2 rounded-lg text-sm border focus:outline-none focus:ring-2 focus:ring-primary-500" :class="darkMode?'bg-primary-900 border-primary-800 text-white':'bg-white border-gray-200 text-gray-900'">
                <option value="{{ route('dashboard.inventory', ['filter' => 'day']) }}" {{ $filter === 'day' ? 'selected' : '' }}>Day</option>
                <option value="{{ route('dashboard.inventory', ['filter' => 'week']) }}" {{ $filter === 'week' ? 'selected' : '' }}>Week</option>
                <option value="{{ route('dashboard.inventory', ['filter' => 'month']) }}" {{ $filter === 'month' ? 'selected' : '' }}>Month</option>
                <option value="{{ route('dashboard.inventory', ['filter' => '3months']) }}" {{ $filter === '3months' ? 'selected' : '' }}>3 Months</option>
                <option value="{{ route('dashboard.inventory', ['filter' => '6months']) }}" {{ $filter === '6months' ? 'selected' : '' }}>6 Months</option>
                <option value="{{ route('dashboard.inventory', ['filter' => 'year']) }}" {{ $filter === 'year' ? 'selected' : '' }}>Year</option>
            </select>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="card rounded-2xl p-5">
            <div class="text-sm" :class="darkMode?'text-primary-400':'text-gray-500'">Total Products</div>
            <div class="text-2xl font-bold" :class="darkMode?'text-white':'text-primary-900'">{{ $totalProducts }}</div>
        </div>
        <div class="card rounded-2xl p-5">
            <div class="text-sm" :class="darkMode?'text-primary-400':'text-gray-500'">Inventory Value</div>
            <div class="text-2xl font-bold" :class="darkMode?'text-white':'text-primary-900'">TZS {{ number_format($totalValue, 2) }}</div>
        </div>
        <div class="card rounded-2xl p-5">
            <div class="text-sm" :class="darkMode?'text-primary-400':'text-gray-500'">Retail Value</div>
            <div class="text-2xl font-bold" :class="darkMode?'text-white':'text-primary-900'">TZS {{ number_format($totalRetailValue, 2) }}</div>
        </div>
        <div class="card rounded-2xl p-5">
            <div class="text-sm" :class="darkMode?'text-primary-400':'text-gray-500'">In Stock</div>
            <div class="text-2xl font-bold" :class="darkMode?'text-white':'text-green-600'">{{ $inStock }}</div>
        </div>
        <div class="card rounded-2xl p-5">
            <div class="text-sm" :class="darkMode?'text-primary-400':'text-gray-500'">Low Stock</div>
            <div class="text-2xl font-bold" :class="darkMode?'text-white':'text-yellow-600'">{{ $lowStock }}</div>
        </div>
    </div>

    <!-- Stock Status Chart -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">Stock Status</h3>
            <canvas id="stockStatusChart" height="200"></canvas>
        </div>
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">Inventory Value Trend</h3>
            <canvas id="inventoryValueChart" height="200"></canvas>
        </div>
    </div>

    <!-- Products by Category & Brand -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">Products by Category</h3>
            <canvas id="productsByCategoryChart" height="200"></canvas>
        </div>
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">Products by Brand</h3>
            <canvas id="productsByBrandChart" height="200"></canvas>
        </div>
    </div>

    <!-- Low Stock Products -->
    <div class="card rounded-2xl p-5">
        <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">Low Stock Products</h3>
        <div class="overflow-x-auto">
            <table class="data-table w-full">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>SKU</th>
                        <th>Quantity</th>
                        <th>Reorder Level</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($lowStockProducts as $product)
                    <tr class="table-row">
                        <td class="font-semibold">{{ $product->name }}</td>
                        <td class="font-mono text-xs">{{ $product->sku ?? '-' }}</td>
                        <td class="text-yellow-600 font-semibold">{{ $product->quantity }}</td>
                        <td>{{ $product->reorder_level }}</td>
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
    // Stock Status Chart
    new Chart(document.getElementById('stockStatusChart'), {
        type: 'doughnut',
        data: {
            labels: ['In Stock', 'Low Stock', 'Out of Stock', 'Expiring Soon', 'Expired'],
            datasets: [{
                data: [{{ $inStock }}, {{ $lowStock }}, {{ $outOfStock }}, {{ $expiringSoon }}, {{ $expired }}],
                backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#f97316', '#dc2626'],
                borderWidth: 3,
                borderColor: '#ffffff',
                cutout: '60%'
            }]
        }
    });

    // Inventory Value Chart
    new Chart(document.getElementById('inventoryValueChart'), {
        type: 'line',
        data: {
            labels: @json($labels),
            datasets: [{
                label: 'Inventory Value',
                data: @json($inventoryValueData),
                borderColor: '#8b5cf6',
                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                fill: true,
                tension: 0.3
            }]
        }
    });

    // Products by Category Chart
    new Chart(document.getElementById('productsByCategoryChart'), {
        type: 'bar',
        data: {
            labels: @json($productsByCategory->pluck('name')),
            datasets: [{
                label: 'Products',
                data: @json($productsByCategory->pluck('products_count')),
                backgroundColor: 'rgba(16, 185, 129, 0.7)',
                borderRadius: 5
            }]
        }
    });

    // Products by Brand Chart
    new Chart(document.getElementById('productsByBrandChart'), {
        type: 'bar',
        data: {
            labels: @json($productsByBrand->pluck('name')),
            datasets: [{
                label: 'Products',
                data: @json($productsByBrand->pluck('products_count')),
                backgroundColor: 'rgba(59, 130, 246, 0.7)',
                borderRadius: 5
            }]
        }
    });
</script>
@endsection
