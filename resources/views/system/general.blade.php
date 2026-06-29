@extends('layouts.app')

@section('page-title', 'General Settings')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">General Settings</h2>
        </div>

        <form action="{{ route('system.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Store Name</label>
                    <input type="text" name="store_name" value="{{ old('store_name', $settings->store_name) }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                           required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Currency</label>
                    <input type="text" name="currency" value="{{ old('currency', $settings->currency ?? 'TZS') }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" 
                           required>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                    <input type="email" name="store_email" value="{{ old('store_email', $settings->store_email) }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                    <input type="text" name="store_phone" value="{{ old('store_phone', $settings->store_phone) }}" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Address</label>
                    <textarea name="store_address" rows="3" 
                              class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('store_address', $settings->store_address) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Store URL (for links in SMS/Emails)</label>
                    <input type="url" name="store_url" value="{{ old('store_url', $settings->store_url) }}" 
                           placeholder="https://yourstore.com"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Store Logo</label>
                    @if($settings->store_logo)
                        <img src="{{ asset('storage/' . $settings->store_logo) }}" alt="Logo" class="h-16 mb-2">
                    @endif
                    <input type="file" name="store_logo" accept="image/*" 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>

                <div class="flex items-center gap-2">
                    <input type="checkbox" name="enable_loyalty" id="enable_loyalty" 
                           {{ old('enable_loyalty', $settings->enable_loyalty) ? 'checked' : '' }} 
                           class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500">
                    <label for="enable_loyalty" class="text-sm font-medium text-gray-700">Enable Loyalty Program</label>
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