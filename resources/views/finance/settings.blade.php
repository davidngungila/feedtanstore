@extends('layouts.app')

@section('page-title', 'Finance Settings')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <h2 class="text-xl font-bold text-primary-900 mb-4">Finance Settings</h2>
        
        <div class="space-y-6">
            <div>
                <h3 class="text-lg font-semibold text-primary-900 mb-4">Tax Management</h3>
                <p class="text-gray-600 mb-4">Manage your tax rates and settings here.</p>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-4 border border-gray-200 rounded-lg hover:shadow-md transition-shadow">
                        <h4 class="font-semibold mb-2">Tax Rates</h4>
                        <p class="text-sm text-gray-600 mb-4">Configure VAT and other tax rates</p>
                        <a href="{{ route('finance.tax-management') }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors inline-block">
                            Manage Rates
                        </a>
                    </div>
                    
                    <div class="p-4 border border-gray-200 rounded-lg hover:shadow-md transition-shadow">
                        <h4 class="font-semibold mb-2">Tax Reports</h4>
                        <p class="text-sm text-gray-600 mb-4">Generate tax reports for filing</p>
                        <button class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                            Generate Reports
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
