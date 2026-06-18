@extends('layouts.app')

@section('page-title', 'Add Shares to ' . $shareholder->name)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Add Shares to {{ $shareholder->name }}</h2>
            <a href="{{ route('finance.shareholders.show', $shareholder) }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>

        <form action="{{ route('finance.shareholders.store-share', $shareholder) }}" method="POST" id="shareForm">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date *</label>
                    <input type="date" name="date" value="{{ old('date', date('Y-m-d')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Number of Shares *</label>
                    <input type="number" name="number_of_shares" id="numberOfShares" value="{{ old('number_of_shares', 1) }}" required min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Share Price (TZS) *</label>
                    <input type="number" step="0.01" name="share_price" id="sharePrice" value="{{ old('share_price', $storeSettings->share_price ?? 0) }}" required min="0" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Total Amount (TZS)</label>
                    <input type="text" id="totalAmount" readonly class="w-full px-4 py-2 border border-gray-200 rounded-lg bg-gray-50 font-bold text-gray-800">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('finance.shareholders.show', $shareholder) }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Add Shares
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function calculateTotal() {
        const numShares = parseFloat(document.getElementById('numberOfShares').value) || 0;
        const sharePrice = parseFloat(document.getElementById('sharePrice').value) || 0;
        const total = numShares * sharePrice;
        document.getElementById('totalAmount').value = total.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    document.getElementById('numberOfShares').addEventListener('input', calculateTotal);
    document.getElementById('sharePrice').addEventListener('input', calculateTotal);
    calculateTotal();
</script>
@endsection
