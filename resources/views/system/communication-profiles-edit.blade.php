@extends('layouts.app')

@section('page-title', 'Edit Communication Profile')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Edit Communication Profile</h2>
            <a href="{{ route('system.communication-profiles') }}" class="text-primary-600 hover:text-primary-800 font-medium">
                <i class="fas fa-arrow-left mr-2"></i>Back to Profiles
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

        <form action="{{ route('system.communication-profiles.update', $communicationProfile) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="mb-8">
                <h3 class="text-lg font-semibold text-primary-800 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Profile Name *</label>
                        <input type="text" name="name" value="{{ old('name', $communicationProfile->name) }}" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Type *</label>
                        <select name="type" id="profile-type" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="email" {{ old('type', $communicationProfile->type) === 'email' ? 'selected' : '' }}>Email (SMTP)</option>
                            <option value="sms" {{ old('type', $communicationProfile->type) === 'sms' ? 'selected' : '' }}>SMS</option>
                        </select>
                    </div>
                    <div>
                        <label class="flex items-center gap-2">
                            <input type="checkbox" name="is_active" value="1" {{ old('is_active', $communicationProfile->is_active) ? 'checked' : '' }} class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                            <span class="text-sm font-medium text-gray-700">Set as Active Profile</span>
                        </label>
                        <p class="text-xs text-gray-500 mt-1">If active, all other profiles of the same type will be deactivated.</p>
                    </div>
                </div>
            </div>

            <div id="email-config" class="mb-8">
                <h3 class="text-lg font-semibold text-primary-800 mb-4">Email Configuration (SMTP)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Host</label>
                        <input type="text" name="smtp_host" value="{{ old('smtp_host', $communicationProfile->smtp_host) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="smtp.example.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Port</label>
                        <input type="number" name="smtp_port" value="{{ old('smtp_port', $communicationProfile->smtp_port) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="587">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Username</label>
                        <input type="text" name="smtp_username" value="{{ old('smtp_username', $communicationProfile->smtp_username) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="your-email@example.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMTP Password</label>
                        <input type="password" name="smtp_password" value="{{ old('smtp_password', $communicationProfile->smtp_password) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Your SMTP Password">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Encryption</label>
                        <select name="smtp_encryption" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="tls" {{ old('smtp_encryption', $communicationProfile->smtp_encryption) === 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ old('smtp_encryption', $communicationProfile->smtp_encryption) === 'ssl' ? 'selected' : '' }}>SSL</option>
                            <option value="none" {{ old('smtp_encryption', $communicationProfile->smtp_encryption) === 'none' ? 'selected' : '' }}>None</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Email Address</label>
                        <input type="email" name="email_from_address" value="{{ old('email_from_address', $communicationProfile->email_from_address) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="no-reply@example.com">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Name</label>
                        <input type="text" name="email_from_name" value="{{ old('email_from_name', $communicationProfile->email_from_name) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="Your Store">
                    </div>
                </div>
            </div>

            <div id="sms-config">
                <h3 class="text-lg font-semibold text-primary-800 mb-4">SMS Configuration</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">SMS Provider</label>
                        <select name="sms_provider" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                            <option value="">Select Provider</option>
                            <option value="twilio" {{ old('sms_provider', $communicationProfile->sms_provider) === 'twilio' ? 'selected' : '' }}>Twilio</option>
                            <option value="nexmo" {{ old('sms_provider', $communicationProfile->sms_provider) === 'nexmo' ? 'selected' : '' }}>Nexmo/Vonage</option>
                            <option value="plivo" {{ old('sms_provider', $communicationProfile->sms_provider) === 'plivo' ? 'selected' : '' }}>Plivo</option>
                            <option value="africastalking" {{ old('sms_provider', $communicationProfile->sms_provider) === 'africastalking' ? 'selected' : '' }}>Africa's Talking</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">API Key</label>
                        <input type="text" name="sms_api_key" value="{{ old('sms_api_key', $communicationProfile->sms_api_key) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">API Secret / Auth Token</label>
                        <input type="password" name="sms_api_secret" value="{{ old('sms_api_secret', $communicationProfile->sms_api_secret) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">From Number</label>
                        <input type="text" name="sms_from_number" value="{{ old('sms_from_number', $communicationProfile->sms_from_number) }}" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-primary-500" placeholder="+1234567890">
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-end gap-3">
                <a href="{{ route('system.communication-profiles') }}" class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors">
                    Update Profile
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const profileTypeSelect = document.getElementById('profile-type');
    const emailConfig = document.getElementById('email-config');
    const smsConfig = document.getElementById('sms-config');
    
    function updateConfigVisibility() {
        const type = profileTypeSelect.value;
        if (type === 'email') {
            emailConfig.style.display = 'block';
            smsConfig.style.display = 'none';
        } else {
            emailConfig.style.display = 'none';
            smsConfig.style.display = 'block';
        }
    }
    
    profileTypeSelect.addEventListener('change', updateConfigVisibility);
    updateConfigVisibility(); // Call on load
});
</script>
@endsection
