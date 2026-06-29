@extends('layouts.app')

@section('page-title', 'Live Location')

@section('content')
<div class="animate-[fadeIn_0.4s_ease] space-y-6">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="{{ route('rider.dashboard') }}" class="text-primary-600 hover:text-primary-800 mb-2 inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
                <h2 class="text-xl font-bold text-primary-900">Live Location</h2>
            </div>
        </div>

        <div class="h-96 bg-gray-100 rounded-lg mb-6 flex items-center justify-center">
            <div class="text-center text-gray-500">
                <i class="fas fa-location-arrow text-5xl mb-4"></i>
                <p>Live location tracking goes here</p>
            </div>
        </div>

        <div class="p-4 border rounded-lg bg-yellow-50">
            <p class="text-sm text-yellow-800">
                <i class="fas fa-info-circle mr-2"></i>
                Location permissions are required to use this feature.
            </p>
        </div>
    </div>
</div>
@endsection
