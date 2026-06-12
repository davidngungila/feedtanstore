@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content')
<div class="animate-[fadeIn_0.4s_ease] space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold" :class="darkMode?'text-white':'text-primary-900'">Good morning, Admin 👋</h2>
            <p class="text-sm mt-0.5" :class="darkMode?'text-primary-400':'text-primary-600'">Here's what's happening today.</p>
        </div>
        <div class="flex items-center gap-2">
            <span class="text-xs font-mono" :class="darkMode?'text-primary-400':'text-primary-500'">{{ date('l, F j, Y') }}</span>
        </div>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="stat-card card" style="animation-delay:0s">
            <div class="bg-blob rounded-full" style="background:#10b981"></div>
            <div class="flex items-start justify-between mb-4">
                <div class="icon-wrap" style="background:#10b98122">
                    <i class="fa-solid fa-dollar-sign" style="color:#10b981"></i>
                </div>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full" style="background:#d1fae5;color:#065f46">
                    <i class="fa-solid fa-arrow-up text-[10px]"></i> 12.5%
                </span>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" :class="darkMode?'text-primary-400':'text-primary-500'">Total Sales</p>
                <p class="text-xl font-bold font-mono" :class="darkMode?'text-white':'text-primary-900'">TZS 1,234,500</p>
                <p class="text-[11px] mt-1" :class="darkMode?'text-primary-500':'text-gray-500'">vs last month</p>
            </div>
        </div>
        <div class="stat-card card" style="animation-delay:80ms">
            <div class="bg-blob rounded-full" style="background:#3b82f6"></div>
            <div class="flex items-start justify-between mb-4">
                <div class="icon-wrap" style="background:#3b82f622">
                    <i class="fa-solid fa-shopping-cart" style="color:#3b82f6"></i>
                </div>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full" style="background:#d1fae5;color:#065f46">
                    <i class="fa-solid fa-arrow-up text-[10px]"></i> 8.2%
                </span>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" :class="darkMode?'text-primary-400':'text-primary-500'">Orders</p>
                <p class="text-xl font-bold font-mono" :class="darkMode?'text-white':'text-primary-900'">145</p>
                <p class="text-[11px] mt-1" :class="darkMode?'text-primary-500':'text-gray-500'">Today</p>
            </div>
        </div>
        <div class="stat-card card" style="animation-delay:160ms">
            <div class="bg-blob rounded-full" style="background:#f59e0b"></div>
            <div class="flex items-start justify-between mb-4">
                <div class="icon-wrap" style="background:#f59e0b22">
                    <i class="fa-solid fa-users" style="color:#f59e0b"></i>
                </div>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full" style="background:#d1fae5;color:#065f46">
                    <i class="fa-solid fa-arrow-up text-[10px]"></i> 5.3%
                </span>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" :class="darkMode?'text-primary-400':'text-primary-500'">Customers</p>
                <p class="text-xl font-bold font-mono" :class="darkMode?'text-white':'text-primary-900'">1,234</p>
                <p class="text-[11px] mt-1" :class="darkMode?'text-primary-500':'text-gray-500'">Active</p>
            </div>
        </div>
        <div class="stat-card card" style="animation-delay:240ms">
            <div class="bg-blob rounded-full" style="background:#ef4444"></div>
            <div class="flex items-start justify-between mb-4">
                <div class="icon-wrap" style="background:#ef444422">
                    <i class="fa-solid fa-box" style="color:#ef4444"></i>
                </div>
                <span class="text-xs font-semibold px-2 py-0.5 rounded-full" style="background:#fee2e2;color:#991b1b">
                    <i class="fa-solid fa-arrow-down text-[10px]"></i> 2.1%
                </span>
            </div>
            <div>
                <p class="text-[11px] font-semibold uppercase tracking-wider mb-1" :class="darkMode?'text-primary-400':'text-primary-500'">Low Stock</p>
                <p class="text-xl font-bold font-mono" :class="darkMode?'text-white':'text-primary-900'">23</p>
                <p class="text-[11px] mt-1" :class="darkMode?'text-primary-500':'text-gray-500'">Products</p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">Recent Sales</h3>
            <div class="overflow-x-auto">
                <table class="data-table w-full">
                    <thead>
                        <tr>
                            <th>Invoice</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-row">
                            <td class="font-mono text-xs">#INV-001</td>
                            <td>John Doe</td>
                            <td class="font-mono">TZS 45,000</td>
                            <td><span class="badge badge-green">Paid</span></td>
                        </tr>
                        <tr class="table-row">
                            <td class="font-mono text-xs">#INV-002</td>
                            <td>Jane Smith</td>
                            <td class="font-mono">TZS 78,000</td>
                            <td><span class="badge badge-yellow">Pending</span></td>
                        </tr>
                        <tr class="table-row">
                            <td class="font-mono text-xs">#INV-003</td>
                            <td>Mike Johnson</td>
                            <td class="font-mono">TZS 32,000</td>
                            <td><span class="badge badge-green">Paid</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card rounded-2xl p-5">
            <h3 class="font-bold text-sm mb-4" :class="darkMode?'text-white':'text-primary-900'">Top Products</h3>
            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 rounded-xl" :class="darkMode?'bg-primary-900/20':'bg-primary-50'">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-primary-200 flex items-center justify-center">
                            <i class="fa-solid fa-box text-primary-700"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold" :class="darkMode?'text-white':'text-primary-900'">Product A</p>
                            <p class="text-xs" :class="darkMode?'text-primary-400':'text-gray-500'">120 units sold</p>
                        </div>
                    </div>
                    <span class="font-mono font-bold" :class="darkMode?'text-white':'text-primary-900'">TZS 240,000</span>
                </div>
                <div class="flex items-center justify-between p-3 rounded-xl" :class="darkMode?'bg-primary-900/20':'bg-primary-50'">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-lg bg-primary-200 flex items-center justify-center">
                            <i class="fa-solid fa-box text-primary-700"></i>
                        </div>
                        <div>
                            <p class="text-sm font-semibold" :class="darkMode?'text-white':'text-primary-900'">Product B</p>
                            <p class="text-xs" :class="darkMode?'text-primary-400':'text-gray-500'">95 units sold</p>
                        </div>
                    </div>
                    <span class="font-mono font-bold" :class="darkMode?'text-white':'text-primary-900'">TZS 190,000</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection