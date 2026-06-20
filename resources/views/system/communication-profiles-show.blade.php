@extends('layouts.app')

@section('page-title', $communicationProfile->name)

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">{{ $communicationProfile->name }}</h2>
            <div class="flex gap-3">
                <a href="{{ route('system.communication-profiles.edit', $communicationProfile) }}" class="px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Edit
                </a>
                <a href="{{ route('system.communication-profiles') }}" class="px-4 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Back to Profiles
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div>
                <p class="text-sm text-gray-500 mb-1">Type</p>
                <span class="badge {{ $communicationProfile->type === 'email' ? 'badge-blue' : 'badge-purple' }}">
                    {{ ucfirst($communicationProfile->type) }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Status</p>
                <span class="badge {{ $communicationProfile->is_active ? 'badge-green' : 'badge-gray' }}">
                    {{ $communicationProfile->is_active ? 'Active' : 'Inactive' }}
                </span>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Created At</p>
                <p class="font-medium">{{ $communicationProfile->created_at->format('M d, Y h:i A') }}</p>
            </div>
            <div>
                <p class="text-sm text-gray-500 mb-1">Last Updated</p>
                <p class="font-medium">{{ $communicationProfile->updated_at->format('M d, Y h:i A') }}</p>
            </div>
        </div>

        @if($communicationProfile->type === 'email')
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-semibold text-primary-800 mb-4">Email Configuration</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">SMTP Host</p>
                        <p class="font-medium">{{ $communicationProfile->smtp_host ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">SMTP Port</p>
                        <p class="font-medium">{{ $communicationProfile->smtp_port ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">SMTP Username</p>
                        <p class="font-medium">{{ $communicationProfile->smtp_username ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">Encryption</p>
                        <p class="font-medium">{{ $communicationProfile->smtp_encryption ? ucfirst($communicationProfile->smtp_encryption) : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">From Email Address</p>
                        <p class="font-medium">{{ $communicationProfile->email_from_address ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">From Name</p>
                        <p class="font-medium">{{ $communicationProfile->email_from_name ?? '-' }}</p>
                    </div>
                </div>
            </div>
        @else
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-semibold text-primary-800 mb-4">SMS Configuration</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-gray-500 mb-1">SMS Provider</p>
                        <p class="font-medium">{{ $communicationProfile->sms_provider ? ucfirst($communicationProfile->sms_provider) : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">API Key</p>
                        <p class="font-medium">{{ $communicationProfile->sms_api_key ? '••••••••' : '-' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500 mb-1">From Number</p>
                        <p class="font-medium">{{ $communicationProfile->sms_from_number ?? '-' }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
