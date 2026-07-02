@extends('layouts.app')

@section('page-title', 'Edit Asset')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Edit Asset</h2>
            <a href="{{ route('finance.assets') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Assets
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

        <form action="{{ route('finance.assets.update', $asset) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Asset Name *</label>
                    <input type="text" name="name" value="{{ old('name', $asset->name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="e.g., Office Laptop, Delivery Truck">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Asset Type *</label>
                    <select name="type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Select Type</option>
                        <option value="Equipment" {{ old('type', $asset->type) == 'Equipment' ? 'selected' : '' }}>Equipment</option>
                        <option value="Vehicle" {{ old('type', $asset->type) == 'Vehicle' ? 'selected' : '' }}>Vehicle</option>
                        <option value="Furniture" {{ old('type', $asset->type) == 'Furniture' ? 'selected' : '' }}>Furniture</option>
                        <option value="Machinery" {{ old('type', $asset->type) == 'Machinery' ? 'selected' : '' }}>Machinery</option>
                        <option value="Electronics" {{ old('type', $asset->type) == 'Electronics' ? 'selected' : '' }}>Electronics</option>
                        <option value="Building" {{ old('type', $asset->type) == 'Building' ? 'selected' : '' }}>Building</option>
                        <option value="Land" {{ old('type', $asset->type) == 'Land' ? 'selected' : '' }}>Land</option>
                        <option value="Software" {{ old('type', $asset->type) == 'Software' ? 'selected' : '' }}>Software</option>
                        <option value="Other" {{ old('type', $asset->type) == 'Other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Serial Number</label>
                    <input type="text" name="serial_number" value="{{ old('serial_number', $asset->serial_number) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="e.g., SN123456789">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <textarea name="description" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Detailed description of the asset">{{ old('description', $asset->description) }}</textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Date *</label>
                    <input type="date" name="purchase_date" value="{{ old('purchase_date', $asset->purchase_date->format('Y-m-d')) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Depreciation Start Date</label>
                    <input type="date" name="depreciation_start_date" value="{{ old('depreciation_start_date', $asset->depreciation_start_date ? $asset->depreciation_start_date->format('Y-m-d') : '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    <p class="text-xs text-gray-500 mt-1">Defaults to purchase date if not specified</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Purchase Cost (TZS) *</label>
                    <input type="number" step="0.01" name="purchase_cost" value="{{ old('purchase_cost', $asset->purchase_cost) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="0.00">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Salvage Value (TZS)</label>
                    <input type="number" step="0.01" name="salvage_value" value="{{ old('salvage_value', $asset->salvage_value) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="0.00">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Useful Life (Years) *</label>
                    <input type="number" name="useful_life_years" value="{{ old('useful_life_years', $asset->useful_life_years) }}" required min="1" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="e.g., 5">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Depreciation Method *</label>
                    <select name="depreciation_method" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Select Method</option>
                        <option value="straight_line" {{ old('depreciation_method', $asset->depreciation_method) == 'straight_line' ? 'selected' : '' }}>Straight Line</option>
                        <option value="declining_balance" {{ old('depreciation_method', $asset->depreciation_method) == 'declining_balance' ? 'selected' : '' }}>Declining Balance</option>
                        <option value="double_declining_balance" {{ old('depreciation_method', $asset->depreciation_method) == 'double_declining_balance' ? 'selected' : '' }}>Double Declining Balance</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status *</label>
                    <select name="status" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                        <option value="">Select Status</option>
                        <option value="active" {{ old('status', $asset->status) == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $asset->status) == 'inactive' ? 'selected' : '' }}>Inactive</option>
                        <option value="maintenance" {{ old('status', $asset->status) == 'maintenance' ? 'selected' : '' }}>Under Maintenance</option>
                        <option value="retired" {{ old('status', $asset->status) == 'retired' ? 'selected' : '' }}>Retired</option>
                        <option value="disposed" {{ old('status', $asset->status) == 'disposed' ? 'selected' : '' }}>Disposed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Location</label>
                    <input type="text" name="location" value="{{ old('location', $asset->location) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="e.g., Main Office, Warehouse">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Manufacturer</label>
                    <input type="text" name="manufacturer" value="{{ old('manufacturer', $asset->manufacturer) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="e.g., Dell, Toyota">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                    <input type="text" name="model" value="{{ old('model', $asset->model) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="e.g., Latitude 7420, Hilux">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Warranty Expiry</label>
                    <input type="date" name="warranty_expiry" value="{{ old('warranty_expiry', $asset->warranty_expiry ? $asset->warranty_expiry->format('Y-m-d') : '') }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Assigned To</label>
                    <input type="text" name="assigned_to" value="{{ old('assigned_to', $asset->assigned_to) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="e.g., John Doe, IT Department">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Maintenance Notes</label>
                    <textarea name="maintenance_notes" rows="3" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Any maintenance history or notes">{{ old('maintenance_notes', $asset->maintenance_notes) }}</textarea>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('finance.assets') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Update Asset
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
