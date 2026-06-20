<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\SentMessage;
use App\Models\StoreSetting;

class MessagingService
{
    protected $baseUrl = 'https://messaging-service.co.tz';
    protected $apiKey;
    protected $senderId;
    protected $testMode;

    public function __construct($apiKey = null, $senderId = null, $testMode = true)
    {
        $settings = StoreSetting::first();
        $this->apiKey = $apiKey ?? ($settings->sms_api_key ?? '');
        $this->senderId = $senderId ?? ($settings->messaging_sender_id ?? 'TANZANIATIP');
        $this->testMode = $testMode;
    }

    public function sendSms($to, $text, $from = null, $testMode = null)
    {
        $from = $from ?? $this->senderId;
        $testMode = $testMode ?? $this->testMode;

        $url = $testMode 
            ? $this->baseUrl . '/api/sms/v2/test/text/single'
            : $this->baseUrl . '/api/sms/v2/text/single';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($url, [
            'from' => $from,
            'to' => $to,
            'text' => $text,
        ]);

        // Store in database
        $sentMessage = SentMessage::create([
            'type' => 'sms',
            'to' => $to,
            'from' => $from,
            'message' => $text,
            'api_response' => $response->json(),
            'status' => $response->successful() ? 'sent' : 'failed',
            'message_id' => $response->json('messages.0.messageId'),
        ]);

        return [
            'success' => $response->successful(),
            'response' => $response->json(),
            'sentMessage' => $sentMessage,
        ];
    }

    public function sendWhatsApp($to, $text, $from = null, $testMode = null)
    {
        $from = $from ?? $this->senderId;
        $testMode = $testMode ?? $this->testMode;

        $url = $testMode 
            ? $this->baseUrl . '/api/whatsapp/v2/test/text/single'
            : $this->baseUrl . '/api/whatsapp/v2/text/single';

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ])->post($url, [
            'from' => $from,
            'to' => $to,
            'text' => $text,
        ]);

        // Store in database
        $sentMessage = SentMessage::create([
            'type' => 'whatsapp',
            'to' => $to,
            'from' => $from,
            'message' => $text,
            'api_response' => $response->json(),
            'status' => $response->successful() ? 'sent' : 'failed',
            'message_id' => $response->json('messages.0.messageId'),
        ]);

        return [
            'success' => $response->successful(),
            'response' => $response->json(),
            'sentMessage' => $sentMessage,
        ];
    }
}
