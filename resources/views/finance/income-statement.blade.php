@extends('layouts.app')

@section('page-title', 'Income Statement (Profit & Loss)')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-primary-900">Income Statement (Profit & Loss)</h1>
        <div class="text-sm text-gray-500">
            As of {{ now()->format('d/m/Y') }}
        </div>
    </div>

    <!-- Income Statement -->
    <div class="card rounded-2xl p-6 mb-6">
        <h2 class="text-xl font-bold text-primary-900 mb-4 pb-3 border-b border-gray-200">Revenue</h2>
        <div class="space-y-3 mb-6">
            <div class="flex justify-between items-center py-2 border-b border-gray-100 gap-4">
                <span class="text-gray-600 flex-shrink-0">Total Sales</span>
                <span class="font-semibold break-all text-right">TZS {{ number_format($totalSales, 2) }}</span>
            </div>
        </div>

        <h2 class="text-xl font-bold text-primary-900 mb-4 pb-3 border-b border-gray-200">Cost of Goods Sold</h2>
        <div class="space-y-3 mb-6">
            <div class="flex justify-between items-center py-2 border-b border-gray-100 gap-4">
                <span class="text-gray-600 flex-shrink-0">Total Purchases</span>
                <span class="font-semibold break-all text-right">TZS {{ number_format($totalPurchases, 2) }}</span>
            </div>
        </div>

        <div class="flex justify-between items-center py-3 border-t-2 border-primary-200 bg-primary-50 px-3 rounded-lg mb-6 gap-4">
            <span class="font-bold text-primary-900 text-lg flex-shrink-0">Gross Profit</span>
            <span class="font-bold {{ $grossProfit >= 0 ? 'text-green-700' : 'text-red-700' }} text-lg break-all text-right">TZS {{ number_format($grossProfit, 2) }}</span>
        </div>

        <h2 class="text-xl font-bold text-primary-900 mb-4 pb-3 border-b border-gray-200">Operating Expenses</h2>
        <div class="space-y-3 mb-6">
            <div class="flex justify-between items-center py-2 border-b border-gray-100 gap-4">
                <span class="text-gray-600 flex-shrink-0">Total Operating Expenses</span>
                <span class="font-semibold break-all text-right">TZS {{ number_format($totalOperatingExpenses, 2) }}</span>
            </div>
        </div>

        <div class="flex justify-between items-center py-3 border-t-2 border-primary-200 bg-primary-50 px-3 rounded-lg mb-6 gap-4">
            <span class="font-bold text-primary-900 text-lg flex-shrink-0">Operating Profit</span>
            <span class="font-bold {{ $operatingProfit >= 0 ? 'text-green-700' : 'text-red-700' }} text-lg break-all text-right">TZS {{ number_format($operatingProfit, 2) }}</span>
        </div>

        <h2 class="text-xl font-bold text-primary-900 mb-4 pb-3 border-b border-gray-200">Other Income</h2>
        <div class="space-y-3 mb-6">
            <div class="flex justify-between items-center py-2 border-b border-gray-100 gap-4">
                <span class="text-gray-600 flex-shrink-0">Total Other Income</span>
                <span class="font-semibold break-all text-right">TZS {{ number_format($totalOtherIncome, 2) }}</span>
            </div>
        </div>

        <div class="flex justify-between items-center py-4 border-t-4 border-green-500 bg-green-50 px-4 rounded-lg gap-4">
            <span class="font-bold text-green-900 text-xl flex-shrink-0">Net Profit</span>
            <span class="font-bold {{ $netProfit >= 0 ? 'text-green-700' : 'text-red-700' }} text-xl break-all text-right">TZS {{ number_format($netProfit, 2) }}</span>
        </div>
    </div>
</div>
@endsection
