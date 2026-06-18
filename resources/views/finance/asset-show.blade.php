@extends('layouts.app')

@section('page-title', $asset->name)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">{{ $asset->name }}</h2>
            <div class="flex gap-2">
                <a href="{{ route('finance.assets.edit', $asset) }}" class="px-3 py-1 bg-primary-100 text-primary-700 rounded-lg hover:bg-primary-200 transition-colors">
                    <i class="fas fa-edit mr-1"></i>Edit
                </a>
                <a href="{{ route('finance.assets') }}" class="px-3 py-1 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i>Back
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <p class="text-sm text-gray-600 mb-1">Asset Type</p>
                <p class="font-medium">{{ $asset->type }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Status</p>
                <p class="font-medium">
                    <span class="px-2 py-1 rounded-full text-xs font-bold {{ $asset->status === 'Active' ? 'bg-green-100 text-green-800' : ($asset->status === 'Sold' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                        {{ $asset->status }}
                    </span>
                </p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Purchase Date</p>
                <p class="font-medium">{{ $asset->purchase_date->format('d/m/Y') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Added By</p>
                <p class="font-medium">{{ $asset->user->name ?? 'N/A' }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Cost</p>
                <p class="font-bold text-primary-700 text-2xl">TZS {{ number_format($asset->cost, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-600 mb-1">Current Value</p>
                <p class="font-bold text-green-700 text-2xl">TZS {{ number_format($asset->current_value ?? $asset->cost, 2) }}</p>
            </div>
            @if($asset->description)
                <div class="md:col-span-2">
                    <p class="text-sm text-gray-600 mb-1">Description</p>
                    <p class="font-medium">{{ $asset->description }}</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
