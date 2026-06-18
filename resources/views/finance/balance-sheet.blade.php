@extends('layouts.app')

@section('page-title', 'Balance Sheet')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-primary-900">Balance Sheet</h1>
        <div class="text-sm text-gray-500">
            As of {{ now()->format('d/m/Y') }}
        </div>
    </div>

    <!-- Balance Sheet -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Assets Section -->
        <div class="card rounded-2xl p-6 min-w-[300px]">
            <h2 class="text-xl font-bold text-primary-900 mb-4 pb-3 border-b border-gray-200">Assets</h2>
            
            <!-- Current Assets -->
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Current Assets</h3>
            <div class="space-y-3 mb-6">
                <div class="flex justify-between items-center py-2 border-b border-gray-100 gap-4">
                    <span class="text-gray-600 flex-shrink-0">Cash on Hand</span>
                    <span class="font-semibold whitespace-nowrap text-right" style="font-size: clamp(0.625rem, 1vw + 0.5rem, 1rem);">TZS {{ number_format($cashBalance, 2) }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100 gap-4">
                    <span class="text-gray-600 flex-shrink-0">Bank Balance</span>
                    <span class="font-semibold whitespace-nowrap text-right" style="font-size: clamp(0.625rem, 1vw + 0.5rem, 1rem);">TZS {{ number_format($bankBalance, 2) }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100 gap-4">
                    <span class="text-gray-600 flex-shrink-0">Mobile Money Balance</span>
                    <span class="font-semibold whitespace-nowrap text-right" style="font-size: clamp(0.625rem, 1vw + 0.5rem, 1rem);">TZS {{ number_format($mobileMoneyBalance, 2) }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100 gap-4">
                    <span class="text-gray-600 flex-shrink-0">Inventory Value</span>
                    <span class="font-semibold whitespace-nowrap text-right" style="font-size: clamp(0.625rem, 1vw + 0.5rem, 1rem);">TZS {{ number_format($inventoryValue, 2) }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100 gap-4">
                    <span class="text-gray-600 flex-shrink-0">Accounts Receivable</span>
                    <span class="font-semibold whitespace-nowrap text-right" style="font-size: clamp(0.625rem, 1vw + 0.5rem, 1rem);">TZS {{ number_format($accountsReceivable, 2) }}</span>
                </div>
            </div>

            <!-- Total Assets -->
            @php
                $totalAssets = $cashBalance + $bankBalance + $mobileMoneyBalance + $inventoryValue + $accountsReceivable;
            @endphp
            <div class="flex justify-between items-center py-3 border-t-2 border-primary-200 bg-primary-50 px-3 rounded-lg gap-4">
                <span class="font-bold text-primary-900 flex-shrink-0">Total Assets</span>
                <span class="font-bold text-primary-900 whitespace-nowrap text-right" style="font-size: clamp(0.75rem, 1.1vw + 0.5rem, 1.125rem);">TZS {{ number_format($totalAssets, 2) }}</span>
            </div>
        </div>

        <!-- Liabilities & Equity Section -->
        <div class="card rounded-2xl p-6 min-w-[300px]">
            <h2 class="text-xl font-bold text-primary-900 mb-4 pb-3 border-b border-gray-200">Liabilities & Equity</h2>
            
            <!-- Liabilities -->
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Liabilities</h3>
            <div class="space-y-3 mb-6">
                <div class="flex justify-between items-center py-2 border-b border-gray-100 gap-4">
                    <span class="text-gray-600 flex-shrink-0">Accounts Payable</span>
                    <span class="font-semibold whitespace-nowrap text-right" style="font-size: clamp(0.625rem, 1vw + 0.5rem, 1rem);">TZS {{ number_format($accountsPayable, 2) }}</span>
                </div>
            </div>

            <!-- Equity -->
            <h3 class="text-lg font-semibold text-gray-800 mb-3">Equity</h3>
            <div class="space-y-3 mb-6">
                <div class="flex justify-between items-center py-2 border-b border-gray-100 gap-4">
                    <span class="text-gray-600 flex-shrink-0">Capital</span>
                    <span class="font-semibold whitespace-nowrap text-right" style="font-size: clamp(0.625rem, 1vw + 0.5rem, 1rem);">TZS {{ number_format($totalCapital, 2) }}</span>
                </div>
                <div class="flex justify-between items-center py-2 border-b border-gray-100 gap-4">
                    <span class="text-gray-600 flex-shrink-0">Retained Earnings</span>
                    <span class="font-semibold whitespace-nowrap text-right" style="font-size: clamp(0.625rem, 1vw + 0.5rem, 1rem);">TZS {{ number_format($retainedEarnings, 2) }}</span>
                </div>
            </div>

            <!-- Total Liabilities & Equity -->
            @php
                $totalLiabilitiesAndEquity = $accountsPayable + $totalCapital + $retainedEarnings;
            @endphp
            <div class="flex justify-between items-center py-3 border-t-2 border-primary-200 bg-primary-50 px-3 rounded-lg gap-4">
                <span class="font-bold text-primary-900 flex-shrink-0">Total Liabilities & Equity</span>
                <span class="font-bold text-primary-900 whitespace-nowrap text-right" style="font-size: clamp(0.75rem, 1.1vw + 0.5rem, 1.125rem);">TZS {{ number_format($totalLiabilitiesAndEquity, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Check Balance -->
    <div class="mt-6">
        @if($totalAssets == $totalLiabilitiesAndEquity)
            <div class="card rounded-2xl p-6 bg-green-50 border border-green-200">
                <div class="flex items-center gap-3">
                    <i class="fas fa-check-circle text-green-600 text-2xl"></i>
                    <div>
                        <h3 class="font-bold text-green-800">Balance Sheet is Balanced</h3>
                        <p class="text-sm text-green-700">Total Assets equal Total Liabilities and Equity</p>
                    </div>
                </div>
            </div>
        @else
            <div class="card rounded-2xl p-6 bg-red-50 border border-red-200">
                <div class="flex items-center gap-3">
                    <i class="fas fa-exclamation-circle text-red-600 text-2xl"></i>
                    <div>
                        <h3 class="font-bold text-red-800">Balance Sheet is Not Balanced</h3>
                        <p class="text-sm text-red-700">Total Assets do not equal Total Liabilities and Equity</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
