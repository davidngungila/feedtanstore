@extends('layouts.app')

@section('page-title', 'Create Budget')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Create Budget</h2>
            <a href="{{ route('finance.budgets') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>

        <form action="{{ route('finance.budgets.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Budget Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="form-input w-full">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="form-input w-full">{{ old('description') }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Budget Amount (TZS) *</label>
                    <input type="number" step="0.01" name="amount" value="{{ old('amount') }}" required min="0" class="form-input w-full">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Start Date *</label>
                    <input type="date" name="start_date" value="{{ old('start_date', date('Y-m-d')) }}" required class="form-input w-full">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">End Date *</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}" required class="form-input w-full">
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('finance.budgets') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Create Budget
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
