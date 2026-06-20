<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MessagingService;
use App\Models\SentMessage;
use App\Models\CommunicationProfile;

class MessagingTestController extends Controller
{
    public function index()
    {
        $profile = CommunicationProfile::where('type', 'sms')->where('is_active', true)->first();
        $messages = SentMessage::latest()->paginate(20);
        return view('system.messaging-test', compact('messages', 'profile'));
    }

    public function sendTestMessage(Request $request)
    {
        $request->validate([
            'type' => 'required|in:sms,whatsapp',
            'to' => 'required|string',
            'message' => 'required|string',
            'api_key' => 'nullable|string',
            'sender_id' => 'nullable|string',
            'test_mode' => 'boolean',
        ]);

        $profile = CommunicationProfile::where('type', 'sms')->where('is_active', true)->first();
        $apiKey = $request->input('api_key') ?? ($profile->sms_api_key ?? '');
        $senderId = $request->input('sender_id') ?? ($profile->messaging_sender_id ?? 'TANZANIATIP');
        $testMode = $request->has('test_mode');

        $messagingService = new MessagingService($apiKey, $senderId, $testMode);

        if ($request->type === 'sms') {
            $result = $messagingService->sendSms($request->to, $request->message);
        } else {
            $result = $messagingService->sendWhatsApp($request->to, $request->message);
        }

        return back()->with([
            'result' => $result,
        ]);
    }
}
