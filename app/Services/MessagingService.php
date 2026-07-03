<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\SentMessage;
use App\Models\CommunicationProfile;

class MessagingService
{
    protected $baseUrl = 'https://messaging-service.co.tz';
    protected $apiKey;
    protected $senderId;
    protected $testMode;

    public function __construct($apiKey = null, $senderId = null, $testMode = false)
    {
        $profile = CommunicationProfile::where('type', 'sms')->where('is_active', true)->first();
        $this->apiKey = $apiKey ?? ($profile->sms_api_key ?? '');
        $this->senderId = $senderId ?? ($profile->messaging_sender_id ?? 'TANZANIATIP');
        $this->testMode = $testMode;
    }

    public function sendSms($to, $text, $from = null, $testMode = null)
    {
        try {
            $from = $from ?? $this->senderId;
            $testMode = $testMode ?? $this->testMode;

            // Clean and format the phone number
            $to = preg_replace('/[^0-9]/', '', $to);
            if (substr($to, 0, 1) === '0') {
                $to = '255' . substr($to, 1);
            } elseif (substr($to, 0, 3) !== '255') {
                // If not starting with 255, assume it's a Tanzania number and prepend 255
                $to = '255' . $to;
            }

            $url = $testMode 
                ? $this->baseUrl . '/api/sms/v2/test/text/single'
                : $this->baseUrl . '/api/sms/v2/text/single';

            Log::info('Attempting to send SMS', [
                'to' => $to,
                'from' => $from,
                'text' => $text,
                'url' => $url,
                'test_mode' => $testMode,
                'api_key_present' => !empty($this->apiKey),
            ]);

            if (empty($this->apiKey)) {
                Log::error('SMS API key not configured');
                $sentMessage = SentMessage::create([
                    'type' => 'sms',
                    'to' => $to,
                    'from' => $from,
                    'message' => $text,
                    'api_response' => ['error' => 'SMS API key not configured'],
                    'status' => 'failed',
                    'message_id' => null,
                ]);
                return [
                    'success' => false,
                    'response' => ['error' => 'SMS API key not configured'],
                    'sentMessage' => $sentMessage,
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ])->post($url, [
                'from' => $from,
                'to' => $to,
                'text' => $text,
            ]);

            Log::info('SMS API Response', [
                'status' => $response->status(),
                'successful' => $response->successful(),
                'body' => $response->body(),
            ]);

            $messageId = null;
            $responseJson = $response->json();
            if (isset($responseJson['messages']) && is_array($responseJson['messages']) && count($responseJson['messages']) > 0) {
                $messageId = $responseJson['messages'][0]['messageId'] ?? null;
            }

            // Store in database
            $sentMessage = SentMessage::create([
                'type' => 'sms',
                'to' => $to,
                'from' => $from,
                'message' => $text,
                'api_response' => $responseJson,
                'status' => $response->successful() ? 'sent' : 'failed',
                'message_id' => $messageId,
            ]);

            return [
                'success' => $response->successful(),
                'response' => $responseJson,
                'sentMessage' => $sentMessage,
            ];
        } catch (\Exception $e) {
            Log::error('Failed to send SMS', ['exception' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            $sentMessage = SentMessage::create([
                'type' => 'sms',
                'to' => $to,
                'from' => $from ?? $this->senderId,
                'message' => $text,
                'api_response' => ['error' => $e->getMessage()],
                'status' => 'failed',
                'message_id' => null,
            ]);
            return [
                'success' => false,
                'response' => ['error' => $e->getMessage()],
                'sentMessage' => $sentMessage,
            ];
        }
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
