@extends('layouts.app')

@section('page-title', 'Add Mobile Money Account')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Add Mobile Money Account</h2>
            <a href="{{ route('finance.mobile-money') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Mobile Money
            </a>
        </div>

        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-800 rounded-lg">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('finance.mobile-money.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Provider *</label>
                    <select name="provider" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Select Provider</option>
                        <option value="M-Pesa" {{ old('provider') == 'M-Pesa' ? 'selected' : '' }}>M-Pesa</option>
                        <option value="Tigo Pesa" {{ old('provider') == 'Tigo Pesa' ? 'selected' : '' }}>Tigo Pesa</option>
                        <option value="Airtel Money" {{ old('provider') == 'Airtel Money' ? 'selected' : '' }}>Airtel Money</option>
                        <option value="Halopesa" {{ old('provider') == 'Halopesa' ? 'selected' : '' }}>Halopesa</option>
                        <option value="TTCL Mobile Money" {{ old('provider') == 'TTCL Mobile Money' ? 'selected' : '' }}>TTCL Mobile Money</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Phone Number *</label>
                    <input type="text" name="phone_number" value="{{ old('phone_number') }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Account Name</label>
                    <input type="text" name="account_name" value="{{ old('account_name') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Opening Balance</label>
                    <input type="number" step="0.01" name="balance" value="{{ old('balance', 0) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="flex items-center gap-2 mt-6">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm font-medium text-gray-700">Active</span>
                    </label>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('finance.mobile-money') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Save Mobile Money Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection