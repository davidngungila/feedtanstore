@extends('layouts.app')

@section('content')
<div class="animate-[fadeIn_0.4s_ease]">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-[#064e3b]">Messaging Test</h1>
            <p class="text-sm text-gray-600 mt-1">Send test SMS/WhatsApp messages using Messaging Service API</p>
        </div>
    </div>

    @if(session('result'))
        <div class="mb-6 p-4 rounded-xl border {{ session('result')['success'] ? 'bg-[#d1fae5] border-[#6ee7b7]' : 'bg-[#fee2e2] border-[#fca5a5]' }}">
            <h3 class="font-semibold {{ session('result')['success'] ? 'text-[#065f46]' : 'text-[#991b1b]' }} mb-2">
                {{ session('result')['success'] ? 'Message Sent!' : 'Error Sending Message' }}
            </h3>
            <pre class="text-xs bg-white/60 p-3 rounded-lg overflow-x-auto">{{ json_encode(session('result')['response'], JSON_PRETTY_PRINT) }}</pre>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Send Message Form -->
        <div class="card rounded-2xl p-6">
            <h2 class="text-lg font-semibold text-[#064e3b] mb-4">Send Test Message</h2>
            <form method="POST" action="{{ route('messaging.test.send') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="form-label">Message Type</label>
                        <select name="type" class="form-input input-field" required>
                            <option value="sms">SMS</option>
                            <option value="whatsapp">WhatsApp</option>
                        </select>
                    </div>
                    <div>
                        <label class="form-label">API Key (Optional - uses settings if empty)</label>
                        <input type="text" name="api_key" class="form-input input-field" value="{{ old('api_key', $profile->sms_api_key ?? '') }}" placeholder="Enter API Key">
                    </div>
                    <div>
                        <label class="form-label">Sender ID (Optional)</label>
                        <input type="text" name="sender_id" class="form-input input-field" value="{{ old('sender_id', $profile->messaging_sender_id ?? '') }}" placeholder="Enter Sender ID (e.g. TANZANIATIP)">
                    </div>
                    <div>
                        <label class="form-label">Recipient Phone Number</label>
                        <input type="text" name="to" class="form-input input-field" placeholder="e.g. 255655000000" required>
                    </div>
                    <div>
                        <label class="form-label">Message</label>
                        <textarea name="message" class="form-input input-field" rows="4" placeholder="Enter your message here..." required></textarea>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" id="test_mode" name="test_mode" checked class="w-4 h-4 text-[#10b981]">
                        <label for="test_mode" class="text-sm text-gray-700">Test Mode (Free, no credits used)</label>
                    </div>
                    <button type="submit" class="w-full bg-[#10b981] hover:bg-[#059669] text-white font-semibold py-3 rounded-xl transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i> Send Message
                    </button>
                </div>
            </form>
        </div>

        <!-- Message History -->
        <div class="card rounded-2xl p-6">
            <h2 class="text-lg font-semibold text-[#064e3b] mb-4">Message History</h2>
            <div class="overflow-x-auto max-h-[500px] overflow-y-auto">
                @if($messages->count() > 0)
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Type</th>
                                <th>To</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($messages as $msg)
                            <tr>
                                <td>
                                    <span class="badge {{ $msg->type === 'sms' ? 'badge-blue' : 'badge-green' }}">
                                        {{ strtoupper($msg->type) }}
                                    </span>
                                </td>
                                <td>{{ $msg->to }}</td>
                                <td>
                                    <span class="badge {{ $msg->status === 'sent' ? 'badge-green' : 'badge-red' }}">
                                        {{ ucfirst($msg->status) }}
                                    </span>
                                </td>
                                <td>{{ $msg->created_at->format('M d, H:i') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="text-center py-12 text-gray-500">
                        <i class="fas fa-comment-dots text-4xl mb-3"></i>
                        <p>No messages sent yet</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
