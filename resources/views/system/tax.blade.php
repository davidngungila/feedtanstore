@extends('layouts.app')

@section('page-title', 'Tax Settings')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Tax Settings</h2>
        </div>

        <form action="{{ route('system.update') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="flex items-center gap-2">
                    <input type="checkbox" name="tax_enabled" id="tax_enabled" 
                           {{ old('tax_enabled', $settings->tax_enabled) ? 'checked' : '' }} 
                           class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500">
                    <label for="tax_enabled" class="text-sm font-medium text-gray-700">Enable Tax</label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tax Name</label>
                    <input type="text" name="tax_name" value="{{ old('tax_name', $settings->tax_name ?? 'VAT') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Tax Rate (%)</label>
                    <input type="number" name="tax_rate" value="{{ old('tax_rate', $settings->tax_rate ?? 0) }}" 
                           step="0.01" min="0" max="100"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg font-medium transition-colors">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection