@extends('layouts.app')

@section('page-title', 'Store Profile')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Store Profile</h2>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('store.profile.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <!-- Logo Upload Section -->
            <div class="mb-8 p-6 bg-gray-50 rounded-xl border border-gray-200">
                <h3 class="text-lg font-semibold text-primary-900 mb-4">Store Logo</h3>
                <div class="flex items-center gap-6">
                    <div class="flex-shrink-0">
                        @if($settings->store_logo)
                            <img src="{{ asset('storage/' . $settings->store_logo) }}" alt="Store Logo" class="w-24 h-24 object-cover rounded-xl border-2 border-primary-300">
                        @else
                            <div class="w-24 h-24 bg-primary-100 rounded-xl border-2 border-dashed border-primary-300 flex items-center justify-center">
                                <i class="fas fa-store text-3xl text-primary-500"></i>
                            </div>
                        @endif
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Upload New Logo</label>
                        <input type="file" name="store_logo" class="w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-100 file:text-primary-700 hover:file:bg-primary-200 transition-colors">
                        <p class="text-xs text-gray-500 mt-2">Max 2MB, JPG/PNG/GIF (Recommended: 200x200 px)</p>
                    </div>
                </div>
            </div>

            <!-- Basic Info Section -->
            <h3 class="text-lg font-semibold text-primary-900 mb-4 border-b border-gray-200 pb-2">Basic Information</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Store Name</label>
                    <input type="text" name="store_name" value="{{ $settings->store_name }}" class="form-input w-full">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Email</label>
                    <input type="email" name="store_email" value="{{ $settings->store_email }}" class="form-input w-full">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Phone</label>
                    <input type="text" name="store_phone" value="{{ $settings->store_phone }}" class="form-input w-full">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Currency</label>
                    <select name="currency" class="form-input w-full">
                        <option value="TZS" {{ $settings->currency === 'TZS' ? 'selected' : '' }}>Tanzanian Shilling (TZS)</option>
                        <option value="USD" {{ $settings->currency === 'USD' ? 'selected' : '' }}>US Dollar (USD)</option>
                        <option value="KES" {{ $settings->currency === 'KES' ? 'selected' : '' }}>Kenyan Shilling (KES)</option>
                        <option value="UGX" {{ $settings->currency === 'UGX' ? 'selected' : '' }}>Ugandan Shilling (UGX)</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
                    <textarea name="store_address" rows="4" class="form-input w-full">{{ $settings->store_address }}</textarea>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-8 py-3 rounded-lg font-semibold transition-colors flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection