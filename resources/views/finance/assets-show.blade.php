@extends('layouts.app')

@section('page-title', 'Asset Details')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Asset Details</h2>
            <div class="flex gap-2">
                <a href="{{ route('finance.assets.edit', $asset) }}" class="px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white rounded-lg transition-colors">
                    <i class="fas fa-edit mr-2"></i>Edit
                </a>
                <a href="{{ route('finance.assets') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                    <i class="fas fa-arrow-left mr-2"></i>Back to Assets
                </a>
            </div>
        </div>

        <!-- Asset Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
            <div class="bg-blue-50 rounded-lg p-4">
                <p class="text-sm text-blue-600 font-medium">Purchase Cost</p>
                <p class="text-2xl font-bold text-blue-900">TZS {{ number_format($asset->purchase_cost, 2) }}</p>
            </div>
            <div class="bg-green-50 rounded-lg p-4">
                <p class="text-sm text-green-600 font-medium">Current Value</p>
                <p class="text-2xl font-bold text-green-900">TZS {{ number_format($asset->current_value, 2) }}</p>
            </div>
            <div class="bg-orange-50 rounded-lg p-4">
                <p class="text-sm text-orange-600 font-medium">Accumulated Depreciation</p>
                <p class="text-2xl font-bold text-orange-900">TZS {{ number_format($asset->accumulated_depreciation, 2) }}</p>
            </div>
            <div class="bg-purple-50 rounded-lg p-4">
                <p class="text-sm text-purple-600 font-medium">Depreciation</p>
                <p class="text-2xl font-bold text-purple-900">{{ number_format($asset->depreciation_percentage, 1) }}%</p>
            </div>
        </div>

        <!-- Asset Information -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="font-bold text-gray-900 mb-4">Basic Information</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Asset Name</p>
                        <p class="font-semibold">{{ $asset->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Type</p>
                        <p class="font-semibold">{{ $asset->type }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Serial Number</p>
                        <p class="font-semibold">{{ $asset->serial_number ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Status</p>
                        <span class="px-2 py-1 rounded-full text-xs font-bold {{ $asset->status === 'active' ? 'bg-green-100 text-green-800' : ($asset->status === 'maintenance' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800') }}">
                            {{ ucfirst($asset->status) }}
                        </span>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Location</p>
                        <p class="font-semibold">{{ $asset->location ?? 'N/A' }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-gray-50 rounded-lg p-4">
                <h3 class="font-bold text-gray-900 mb-4">Purchase & Depreciation Details</h3>
                <div class="space-y-3">
                    <div>
                        <p class="text-sm text-gray-600">Purchase Date</p>
                        <p class="font-semibold">{{ $asset->purchase_date->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Depreciation Start Date</p>
                        <p class="font-semibold">{{ $asset->depreciation_start_date ? $asset->depreciation_start_date->format('d/m/Y') : $asset->purchase_date->format('d/m/Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Salvage Value</p>
                        <p class="font-semibold">TZS {{ number_format($asset->salvage_value, 2) }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Useful Life</p>
                        <p class="font-semibold">{{ $asset->useful_life_years }} years</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Remaining Life</p>
                        <p class="font-semibold">{{ $asset->remaining_life }} years</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Depreciation Method</p>
                        <p class="font-semibold">{{ ucfirst(str_replace('_', ' ', $asset->depreciation_method)) }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Details -->
        @if($asset->manufacturer || $asset->model || $asset->warranty_expiry || $asset->assigned_to || $asset->maintenance_notes)
        <div class="bg-gray-50 rounded-lg p-4 mb-8">
            <h3 class="font-bold text-gray-900 mb-4">Additional Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($asset->manufacturer)
                <div>
                    <p class="text-sm text-gray-600">Manufacturer</p>
                    <p class="font-semibold">{{ $asset->manufacturer }}</p>
                </div>
                @endif
                @if($asset->model)
                <div>
                    <p class="text-sm text-gray-600">Model</p>
                    <p class="font-semibold">{{ $asset->model }}</p>
                </div>
                @endif
                @if($asset->warranty_expiry)
                <div>
                    <p class="text-sm text-gray-600">Warranty Expiry</p>
                    <p class="font-semibold">{{ $asset->warranty_expiry->format('d/m/Y') }}</p>
                </div>
                @endif
                @if($asset->assigned_to)
                <div>
                    <p class="text-sm text-gray-600">Assigned To</p>
                    <p class="font-semibold">{{ $asset->assigned_to }}</p>
                </div>
                @endif
            </div>
            @if($asset->maintenance_notes)
            <div class="mt-4">
                <p class="text-sm text-gray-600">Maintenance Notes</p>
                <p class="font-semibold">{{ $asset->maintenance_notes }}</p>
            </div>
            @endif
        </div>
        @endif

        @if($asset->description)
        <div class="bg-gray-50 rounded-lg p-4 mb-8">
            <h3 class="font-bold text-gray-900 mb-2">Description</h3>
            <p class="text-gray-700">{{ $asset->description }}</p>
        </div>
        @endif

        <!-- Depreciation Schedule -->
        <div class="bg-gray-50 rounded-lg p-4">
            <h3 class="font-bold text-gray-900 mb-4">Depreciation Schedule</h3>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-200">
                        <tr>
                            <th class="px-4 py-3 text-left text-gray-600">Year</th>
                            <th class="px-4 py-3 text-left text-gray-600">Date</th>
                            <th class="px-4 py-3 text-left text-gray-600">Depreciation</th>
                            <th class="px-4 py-3 text-left text-gray-600">Accumulated Depreciation</th>
                            <th class="px-4 py-3 text-left text-gray-600">Book Value</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($asset->depreciation_schedule as $schedule)
                        <tr class="{{ $schedule['date'] <= now()->format('Y-m-d') ? 'bg-blue-50' : '' }}">
                            <td class="px-4 py-3 font-semibold">Year {{ $schedule['year'] }}</td>
                            <td class="px-4 py-3">{{ \Carbon\Carbon::parse($schedule['date'])->format('d/m/Y') }}</td>
                            <td class="px-4 py-3 text-orange-600">TZS {{ number_format($schedule['depreciation'], 2) }}</td>
                            <td class="px-4 py-3">TZS {{ number_format($schedule['accumulated_depreciation'], 2) }}</td>
                            <td class="px-4 py-3 font-bold text-green-700">TZS {{ number_format($schedule['book_value'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if($asset->is_fully_depreciated)
            <div class="mt-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i>This asset is fully depreciated.
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
