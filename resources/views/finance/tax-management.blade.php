@extends('layouts.app')

@section('page-title', 'Tax Management')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <h2 class="text-xl font-bold text-primary-900 mb-4">Tax Management</h2>
        <p class="text-gray-600 mb-6">Manage your tax rates and settings here.</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="p-4 border border-gray-200 rounded-lg">
                <h3 class="font-semibold mb-2">Tax Rates</h3>
                <p class="text-sm text-gray-600 mb-4">Configure VAT and other tax rates</p>
                <button class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Manage Rates
                </button>
            </div>
            
            <div class="p-4 border border-gray-200 rounded-lg">
                <h3 class="font-semibold mb-2">Tax Reports</h3>
                <p class="text-sm text-gray-600 mb-4">Generate tax reports for filing</p>
                <button class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Generate Reports
                </button>
            </div>
        </div>
    </div>
</div>
@endsection