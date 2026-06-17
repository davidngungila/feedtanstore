@extends('layouts.app')

@section('page-title', 'Receipt Settings')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Receipt Settings</h2>
        </div>

        <form action="{{ route('system.update') }}" method="POST">
            @csrf
            <div class="space-y-6 mb-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="receipt_show_logo" id="receipt_show_logo" 
                               {{ old('receipt_show_logo', $settings->receipt_show_logo) ? 'checked' : '' }} 
                               class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500">
                        <label for="receipt_show_logo" class="text-sm font-medium text-gray-700">Show Logo on Receipt</label>
                    </div>

                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="receipt_show_tax" id="receipt_show_tax" 
                               {{ old('receipt_show_tax', $settings->receipt_show_tax) ? 'checked' : '' }} 
                               class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500">
                        <label for="receipt_show_tax" class="text-sm font-medium text-gray-700">Show Tax on Receipt</label>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Receipt Header</label>
                    <textarea name="receipt_header" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('receipt_header', $settings->receipt_header) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Receipt Footer</label>
                    <textarea name="receipt_footer" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('receipt_footer', $settings->receipt_footer) }}</textarea>
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