@extends('layouts.app')

@section('page-title', 'Test Communication Profile')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="card rounded-2xl p-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-xl font-bold text-primary-900">Test Profile: {{ $communicationProfile->name }}</h2>
            <a href="{{ route('system.communication-profiles') }}" class="text-primary-600 hover:text-primary-800">
                <i class="fas fa-arrow-left mr-2"></i>Back
            </a>
        </div>

        @if(session('success'))
            <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-800 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-800 rounded-lg">
                {{ session('error') }}
            </div>
        @endif

        <form method="POST" action="{{ route('system.communication-profiles.send-test', $communicationProfile) }}">
            @csrf

            @if($communicationProfile->type === 'email')
                <div class="space-y-4">
                    <div>
                        <label class="form-label">Recipient Email</label>
                        <input type="email" name="recipient" class="form-input input-field" required placeholder="test@example.com">
                    </div>
                    <div>
                        <label class="form-label">Subject</label>
                        <input type="text" name="subject" class="form-input input-field" required value="Test Email from {{ config('app.name') }}">
                    </div>
                    <div>
                        <label class="form-label">Message</label>
                        <textarea name="message" class="form-input input-field" rows="5" required>This is a test email to verify your email configuration is working correctly!</textarea>
                    </div>
                </div>
            @else
                <div class="space-y-4">
                    <div>
                        <label class="form-label">Recipient Phone Number</label>
                        <input type="text" name="recipient" class="form-input input-field" required placeholder="e.g. 255655000000">
                    </div>
                    <div>
                        <label class="form-label">Message</label>
                        <textarea name="message" class="form-input input-field" rows="4" required>This is a test SMS to verify your SMS configuration is working correctly!</textarea>
                    </div>
                </div>
            @endif

            <div class="mt-6">
                <button type="submit" class="bg-primary-600 hover:bg-primary-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                    <i class="fas fa-paper-plane mr-2"></i>Send Test
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
