@extends('layouts.app')

@section('page-title', 'Supplier Credit Report')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class='text-xl font-bold text-primary-900'>Supplier Credit Report</h2>
            <a href="{{ route('reports.advanced.supplier-credit.pdf') }}" class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">
                Export PDF
            </a>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-primary-200 flex items-center justify-center">
                        <i class="fas fa-truck text-primary-700"></i>
                    </div>
                </div>
                <p class="text-sm text-primary-700 mb-1">Total Suppliers</p>
                <h3 class="text-2xl font-bold text-primary-900">{{ $suppliers->count() }}</h3>
            </div>
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-200 flex items-center justify-center">
                        <i class="fas fa-wallet text-blue-700"></i>
                    </div>
                </div>
                <p class="text-sm text-blue-700 mb-1">Total Credit</p>
                <h3 class="text-2xl font-bold text-blue-900">TZS {{ number_format($totalCredit, 2) }}</h3>
            </div>
            @php
                $suppliersWithCredit = $suppliers->filter(function($s) { return $s->purchaseOrders->sum('total') > 0; });
            @endphp
            <div class="bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-orange-200 flex items-center justify-center">
                        <i class="fas fa-clock text-orange-700"></i>
                    </div>
                </div>
                <p class="text-sm text-orange-700 mb-1">Suppliers with Credit</p>
                <h3 class="text-2xl font-bold text-orange-900">{{ $suppliersWithCredit->count() }}</h3>
            </div>
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-green-200 flex items-center justify-center">
                        <i class="fas fa-percentage text-green-700"></i>
                    </div>
                </div>
                <p class="text-sm text-green-700 mb-1">Avg. Credit/Supplier</p>
                <h3 class="text-2xl font-bold text-green-900">TZS {{ number_format($suppliers->count() > 0 ? $totalCredit / $suppliers->count() : 0, 2) }}</h3>
            </div>
        </div>

        <!-- Suppliers Table -->
        <div class="border border-gray-100 rounded-xl p-5">
            <h4 class="font-semibold text-primary-900 mb-4">Supplier Credit Details</h4>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-700 font-medium">Supplier</th>
                            <th class="px-4 py-3 text-left text-gray-700 font-medium">Email</th>
                            <th class="px-4 py-3 text-left text-gray-700 font-medium">Phone</th>
                            <th class="px-4 py-3 text-right text-gray-700 font-medium">Outstanding Credit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($suppliers as $supplier)
                        <tr>
                            <td class="px-4 py-3 font-medium">{{ $supplier->name }}</td>
                            <td class="px-4 py-3">{{ $supplier->email }}</td>
                            <td class="px-4 py-3">{{ $supplier->phone }}</td>
                            @php
                                $credit = $supplier->purchaseOrders->sum('total');
                            @endphp
                            <td class="px-4 py-3 text-right {{ $credit > 0 ? 'text-orange-700' : 'text-gray-600' }} font-semibold">TZS {{ number_format($credit, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection