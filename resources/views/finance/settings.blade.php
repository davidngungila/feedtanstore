@extends('layouts.app')

@section('page-title', 'Finance Settings')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <h2 class="text-xl font-bold text-primary-900 mb-4">Finance Settings</h2>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('store.settings.update') }}" method="POST">
            @csrf
            @method('PUT')

            <h3 class="text-lg font-semibold text-primary-800 mb-4">Tax Management</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="tax_enabled" {{ $settings->tax_enabled ? 'checked' : '' }} class="w-5 h-5 text-primary-600 rounded border-gray-300 focus:ring-primary-500">
                        <span class="text-sm font-semibold text-gray-700">Enable Tax Calculation</span>
                    </label>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tax Name (e.g., VAT, GST)</label>
                    <input type="text" name="tax_name" value="{{ $settings->tax_name }}" class="form-input w-full">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Tax Rate (%)</label>
                    <input type="number" step="0.01" name="tax_rate" value="{{ $settings->tax_rate }}" min="0" max="100" class="form-input w-full">
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
