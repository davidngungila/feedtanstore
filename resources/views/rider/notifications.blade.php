@extends('layouts.app')

@section('page-title', 'Notifications')

@section('content')
<div class="animate-[fadeIn_0.4s_ease] space-y-6">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="{{ route('rider.dashboard') }}" class="text-primary-600 hover:text-primary-800 mb-2 inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                </a>
                <h2 class="text-xl font-bold text-primary-900">Notifications</h2>
            </div>
        </div>

        <div class="space-y-3">
            <div class="p-4 border rounded-lg bg-blue-50">
                <div class="flex items-start gap-3">
                    <i class="fas fa-bell text-blue-600 mt-1"></i>
                    <div>
                        <div class="font-semibold text-blue-800">Welcome to your rider dashboard!</div>
                        <div class="text-sm text-blue-600">You can now manage your deliveries and track orders from here.</div>
                        <div class="text-xs text-blue-500 mt-1">{{ now()->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            </div>
            <div class="p-4 border rounded-lg">
                <div class="flex items-start gap-3">
                    <i class="fas fa-info-circle text-gray-400 mt-1"></i>
                    <div>
                        <div class="font-semibold text-gray-700">No new notifications</div>
                        <div class="text-sm text-gray-500">You're all caught up!</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
