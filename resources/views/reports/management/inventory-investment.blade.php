@extends('layouts.app')

@section('page-title', 'Inventory Investment')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex flex-wrap items-center justify-between mb-6 gap-4">
            <h2 class="text-xl font-bold text-primary-900">Inventory Investment</h2>
            <div class="flex items-center gap-3">
                <button class="px-4 py-2 border border-primary-200 rounded-lg text-primary-700 hover:bg-primary-50 font-medium transition-colors">
                    Export PDF
                </button>
            </div>
        </div>

        <!-- Stats Card -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-gradient-to-br from-primary-50 to-primary-100 rounded-xl p-5">
                <div class="flex items-center justify-between mb-3">
                    <div class="w-10 h-10 rounded-lg bg-primary-200 flex items-center justify-center">
                        <i class="fas fa-coins text-primary-700"></i>
                    </div>
                </div>
                <p class="text-sm text-primary-700 mb-1">Total Investment</p>
                <h3 class="text-2xl font-bold text-primary-900">TZS {{ number_format($totalInvestment, 2) }}</h3>
            </div>
        </div>

        <!-- Categories Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-gray-700 font-medium">Category</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Investment Value</th>
                        <th class="px-4 py-3 text-right text-gray-700 font-medium">Percentage</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($categories as $category)
                    <tr>
                        <td class="px-4 py-3 font-medium">{{ $category->name }}</td>
                        <td class="px-4 py-3 text-right">TZS {{ number_format($category->investment_value, 2) }}</td>
                        <td class="px-4 py-3 text-right">
                            {{ $totalInvestment > 0 ? number_format(($category->investment_value / $totalInvestment) * 100, 2) : 0 }}%
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection