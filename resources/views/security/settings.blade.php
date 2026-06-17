@extends('layouts.app')

@section('page-title', 'Security Settings')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <h2 class="text-xl font-bold mb-6 text-primary-900">Security Settings</h2>
        
        <form method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Authentication</h3>
                </div>
                
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="twoFactor" class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500">
                    <label for="twoFactor" class="text-sm font-medium text-gray-700">Two-Factor Authentication</label>
                </div>
                
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="sessionTimeout" checked class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500">
                    <label for="sessionTimeout" class="text-sm font-medium text-gray-700">Auto Session Timeout (30 min)</label>
                </div>
                
                <div class="md:col-span-2">
                    <h3 class="text-lg font-semibold text-gray-900 mb-4">Password Policy</h3>
                </div>
                
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">Minimum Password Length</label>
                    <input type="number" value="8" min="6" max="32" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="requireUppercase" checked class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500">
                    <label for="requireUppercase" class="text-sm font-medium text-gray-700">Require Uppercase</label>
                </div>
                
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="requireNumber" checked class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500">
                    <label for="requireNumber" class="text-sm font-medium text-gray-700">Require Numbers</label>
                </div>
                
                <div class="flex items-center gap-2">
                    <input type="checkbox" id="requireSpecial" class="w-4 h-4 text-primary-600 rounded focus:ring-primary-500">
                    <label for="requireSpecial" class="text-sm font-medium text-gray-700">Require Special Characters</label>
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