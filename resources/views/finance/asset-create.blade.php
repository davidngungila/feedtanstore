@extends('layouts.app')

@section('page-title', 'Add Asset')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Add Asset</h2>
            <a href="{{ route('finance.assets') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>

        <form action="{{ route('finance.assets.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Asset Name *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="form-input w-full">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Asset Type *</label>
                    <select name="type" required class="form-input w-full">
                        <option value="">Select Type</option>
                        <option value="Equipment" {{ old('type') == 'Equipment' ? 'selected' : '' }}>Equipment</option>
                        <option value="Vehicle" {{ old('type') == 'Vehicle' ? 'selected' : '' }}>Vehicle</option>
                        <option value="Furniture" {{ old('type') == 'Furniture' ? 'selected' : '' }}>Furniture</option>
                        <option value="Electronics" {{ old('type') == 'Electronics' ? 'selected' : '' }}>Electronics</option>
                        <option value="Other" {{ old('type') == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Purchase Date *</label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date', date('Y-m-d')) }}" required class="form-input w-full">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Cost (TZS) *</label>
                    <input type="number" step="0.01" name="cost" value="{{ old('cost') }}" required min="0" class="form-input w-full">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Current Value (TZS)</label>
                    <input type="number" step="0.01" name="current_value" value="{{ old('current_value') }}" min="0" class="form-input w-full">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Status *</label>
                    <select name="status" required class="form-input w-full">
                        <option value="Active" {{ old('status') == 'Active' ? 'selected' : '' }}>Active</option>
                        <option value="Sold" {{ old('status') == 'Sold' ? 'selected' : '' }}>Sold</option>
                        <option value="Disposed" {{ old('status') == 'Disposed' ? 'selected' : '' }}>Disposed</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <textarea name="description" rows="3" class="form-input w-full">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('finance.assets') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Save Asset
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
