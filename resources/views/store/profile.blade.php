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

        <form action="{{ route('store.profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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
                    <input type="text" name="currency" value="{{ $settings->currency }}" class="form-input w-full">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Address</label>
                    <textarea name="store_address" rows="3" class="form-input w-full">{{ $settings->store_address }}</textarea>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</div>
@endsection