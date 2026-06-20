<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\MessagingService;
use App\Models\SentMessage;
use App\Models\StoreSetting;

class MessagingTestController extends Controller
{
    public function index()
    {
        $settings = StoreSetting::firstOrCreate();
        $messages = SentMessage::latest()->paginate(20);
        return view('system.messaging-test', compact('messages', 'settings'));
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

        $apiKey = $request->input('api_key') ?? config('services.messaging.api_key');
        $senderId = $request->input('sender_id') ?? config('services.messaging.sender_id');
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
