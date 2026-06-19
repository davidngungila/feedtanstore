@extends('layouts.app')

@section('page-title', 'Edit Account')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Edit Account</h2>
            <a href="{{ route('finance.chart-of-accounts') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Chart of Accounts
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

        <form action="{{ route('finance.chart-of-accounts.update', $account) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Account Code *</label>
                    <input type="text" name="account_code" value="{{ old('account_code', $account->account_code) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input type="text" name="name" value="{{ old('name', $account->name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Account Type *</label>
                    <select name="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Select type</option>
                        <option value="Asset" {{ old('type', $account->type) === 'Asset' ? 'selected' : '' }}>Asset</option>
                        <option value="Liability" {{ old('type', $account->type) === 'Liability' ? 'selected' : '' }}>Liability</option>
                        <option value="Equity" {{ old('type', $account->type) === 'Equity' ? 'selected' : '' }}>Equity</option>
                        <option value="Revenue" {{ old('type', $account->type) === 'Revenue' ? 'selected' : '' }}>Revenue</option>
                        <option value="Expense" {{ old('type', $account->type) === 'Expense' ? 'selected' : '' }}>Expense</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Parent Account</label>
                    <select name="parent_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">None (Top-level account)</option>
                        @foreach($parentAccounts as $parentAccount)
                            <option value="{{ $parentAccount->id }}" {{ old('parent_id', $account->parent_id) == $parentAccount->id ? 'selected' : '' }}>{{ $parentAccount->account_code }} - {{ $parentAccount->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('description', $account->description) }}</textarea>
                </div>
                <div>
                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1" {{ old('is_active', $account->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        <span class="text-sm font-medium text-gray-700">Active</span>
                    </label>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('finance.chart-of-accounts') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Update Account
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
