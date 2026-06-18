@extends('layouts.app')

@section('page-title', 'Budgets')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Budgets</h2>
            <button class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                Add Budget
            </button>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="p-4 border border-gray-200 rounded-lg">
                <h3 class="font-semibold mb-2">Marketing Budget</h3>
                <p class="text-3xl font-bold text-primary-900 mb-2">TZS 500,000.00</p>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: 65%"></div>
                </div>
                <p class="text-sm text-gray-600 mt-2">65% spent</p>
            </div>
            
            <div class="p-4 border border-gray-200 rounded-lg">
                <h3 class="font-semibold mb-2">Operations Budget</h3>
                <p class="text-3xl font-bold text-primary-900 mb-2">TZS 1,000,000.00</p>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-green-600 h-2 rounded-full" style="width: 40%"></div>
                </div>
                <p class="text-sm text-gray-600 mt-2">40% spent</p>
            </div>
            
            <div class="p-4 border border-gray-200 rounded-lg">
                <h3 class="font-semibold mb-2">Inventory Budget</h3>
                <p class="text-3xl font-bold text-primary-900 mb-2">TZS 2,000,000.00</p>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-yellow-600 h-2 rounded-full" style="width: 80%"></div>
                </div>
                <p class="text-sm text-gray-600 mt-2">80% spent</p>
            </div>
        </div>
    </div>
</div>
@endsection