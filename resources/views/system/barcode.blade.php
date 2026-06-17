@extends('layouts.app')

@section('page-title', 'Barcode Settings')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Barcode Settings</h2>
        </div>

        <form action="{{ route('system.update') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Barcode Type</label>
                    <select name="barcode_type" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="CODE128" {{ old('barcode_type', $settings->barcode_type ?? 'CODE128') == 'CODE128' ? 'selected' : '' }}>Code 128</option>
                        <option value="EAN13" {{ old('barcode_type', $settings->barcode_type ?? 'CODE128') == 'EAN13' ? 'selected' : '' }}>EAN-13</option>
                        <option value="UPCA" {{ old('barcode_type', $settings->barcode_type ?? 'CODE128') == 'UPCA' ? 'selected' : '' }}>UPC-A</option>
                        <option value="CODE39" {{ old('barcode_type', $settings->barcode_type ?? 'CODE128') == 'CODE39' ? 'selected' : '' }}>Code 39</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Barcode Width (px)</label>
                    <input type="number" name="barcode_width" value="{{ old('barcode_width', $settings->barcode_width ?? 300) }}" 
                           min="100" max="1000"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Barcode Height (px)</label>
                    <input type="number" name="barcode_height" value="{{ old('barcode_height', $settings->barcode_height ?? 100) }}" 
                           min="50" max="500"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="barcode_show_text" id="barcode_show_text" 
                           {{ old('barcode_show_text', $settings->barcode_show_text) ? 'checked' : '' }} 
                           class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500">
                    <label for="barcode_show_text" class="text-sm font-medium text-gray-700">Show Text Below Barcode</label>
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