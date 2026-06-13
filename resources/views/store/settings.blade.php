@extends('layouts.app')

@section('page-title', 'Store Settings')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Store Settings</h2>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('store.settings.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <!-- Tax Settings Section -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-primary-900 mb-4 border-b border-gray-200 pb-2">
                    <i class="fas fa-percentage mr-2"></i>
                    Tax Settings
                </h3>
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tax Rate (%)</label>
                            <input type="number" step="0.01" name="tax_rate" value="{{ $settings->tax_rate }}" class="form-input w-full">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Receipt Settings Section -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-primary-900 mb-4 border-b border-gray-200 pb-2">
                    <i class="fas fa-receipt mr-2"></i>
                    Receipt Settings
                </h3>
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Receipt Footer</label>
                        <textarea name="receipt_footer" rows="4" class="form-input w-full" placeholder="Thank you for your business!">{{ $settings->receipt_footer }}</textarea>
                        <p class="text-xs text-gray-500 mt-2">This text will appear at the bottom of all customer receipts</p>
                    </div>
                </div>
            </div>

            <!-- Program Settings Section -->
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-primary-900 mb-4 border-b border-gray-200 pb-2">
                    <i class="fas fa-gift mr-2"></i>
                    Program Settings
                </h3>
                <div class="p-4 bg-gray-50 rounded-xl border border-gray-200">
                    <label class="flex items-center gap-3">
                        <input type="checkbox" name="enable_loyalty" value="1" {{ $settings->enable_loyalty ? 'checked' : '' }} class="w-5 h-5 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <div>
                            <span class="text-base font-semibold text-gray-700">Enable Loyalty Program</span>
                            <p class="text-xs text-gray-500 mt-1">Allows customers to earn and redeem loyalty points</p>
                        </div>
                    </label>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors flex items-center gap-2">
                    <i class="fas fa-cog"></i>
                    Save Settings
                </button>
            </div>
        </form>
    </div>
</div>
@endsection