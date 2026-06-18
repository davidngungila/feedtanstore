@extends('layouts.app')

@section('page-title', 'Edit Income')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Edit Income</h2>
            <a href="{{ route('finance.income.show', $income) }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Income Details
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

        <form action="{{ route('finance.income.update', $income) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                    <input type="date" name="date" value="{{ old('date', $income->date->format('Y-m-d')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Category *</label>
                    <select name="category" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Select Category</option>
                        <option value="Sales" {{ old('category', $income->category) == 'Sales' ? 'selected' : '' }}>Sales</option>
                        <option value="Services" {{ old('category', $income->category) == 'Services' ? 'selected' : '' }}>Services</option>
                        <option value="Interest" {{ old('category', $income->category) == 'Interest' ? 'selected' : '' }}>Interest</option>
                        <option value="Other" {{ old('category', $income->category) == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('description', $income->description) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Amount *</label>
                    <input type="number" step="0.01" name="amount" value="{{ old('amount', $income->amount) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method *</label>
                    <select name="payment_method" id="payment_method" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Select Method</option>
                        <option value="Cash" {{ old('payment_method', $income->payment_method) == 'Cash' ? 'selected' : '' }}>Cash</option>
                        <option value="Bank Transfer" {{ old('payment_method', $income->payment_method) == 'Bank Transfer' ? 'selected' : '' }}>Bank Transfer</option>
                        <option value="Mobile Money" {{ old('payment_method', $income->payment_method) == 'Mobile Money' ? 'selected' : '' }}>Mobile Money</option>
                    </select>
                </div>
                <div id="bank_account_field" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bank Account</label>
                    <select name="bank_account_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Select Bank Account</option>
                        @foreach($bankAccounts as $account)
                            @if($account->is_active)
                                <option value="{{ $account->id }}" {{ old('bank_account_id', $income->bank_account_id) == $account->id ? 'selected' : '' }}>{{ $account->name }} - {{ $account->bank_name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div id="mobile_money_field" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Mobile Money Account</label>
                    <select name="mobile_money_account_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Select Mobile Money Account</option>
                        @foreach($mobileMoneyAccounts as $account)
                            @if($account->is_active)
                                <option value="{{ $account->id }}" {{ old('mobile_money_account_id', $income->mobile_money_account_id) == $account->id ? 'selected' : '' }}>{{ $account->provider }} - {{ $account->phone_number }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('finance.income.show', $income) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Update Income
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethodSelect = document.getElementById('payment_method');
        const bankField = document.getElementById('bank_account_field');
        const mobileField = document.getElementById('mobile_money_field');
        
        paymentMethodSelect.addEventListener('change', function() {
            bankField.style.display = 'none';
            mobileField.style.display = 'none';
            
            if (this.value === 'Bank Transfer') {
                bankField.style.display = 'block';
            } else if (this.value === 'Mobile Money') {
                mobileField.style.display = 'block';
            }
        });
        
        if (paymentMethodSelect.value) {
            paymentMethodSelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection
