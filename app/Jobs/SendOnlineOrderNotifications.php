<?php

namespace App\Jobs;

use App\Models\OnlineOrder;
use App\Models\CommunicationProfile;
use App\Mail\OnlineOrderPlaced;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use App\Services\MessagingService;

class SendOnlineOrderNotifications implements ShouldQueue
{
    use Queueable;

    protected $order;

    /**
     * Create a new job instance.
     */
    public function __construct(OnlineOrder $order)
    {
        $this->order = $order->load(['items.product']);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $order = $this->order;
        
        // Get store settings to use store_url if available
        $settings = \App\Models\StoreSetting::firstOrCreate();
        $baseUrl = $settings->store_url ?? config('app.url');
        $trackingIdentifier = $order->tracking_token ?? $order->order_number;
        $trackingUrl = $baseUrl . '/shop/tracking/' . $trackingIdentifier;

        // 1. Send SMS
        $smsProfile = CommunicationProfile::where('type', 'sms')->where('is_active', true)->first();
        if ($smsProfile && $order->customer_phone) {
            $phoneNumber = $order->customer_phone;
            if (substr($phoneNumber, 0, 1) === '0') {
                $phoneNumber = '255' . substr($phoneNumber, 1);
            }
            try {
                $smsText = "Thanks $order->customer_name. Your order $order->short_customer_reference has been received. Delivery Code: $order->delivery_code. Please keep this code safe and provide it upon delivery. Track: $trackingUrl";
                $messagingService = new MessagingService($smsProfile->sms_api_key, $smsProfile->messaging_sender_id, false);
                $messagingService->sendSms($phoneNumber, $smsText);
            } catch (\Exception $e) {
                \Log::error('Failed to send online order SMS: ' . $e->getMessage());
            }
        }

        // 2. Send Email
        $emailProfile = CommunicationProfile::where('type', 'email')->where('is_active', true)->first();
        if ($emailProfile && $order->customer_email) {
            config([
                'mail.mailers.test_smtp' => [
                    'transport' => 'smtp',
                    'host' => $emailProfile->smtp_host,
                    'port' => $emailProfile->smtp_port,
                    'encryption' => $emailProfile->smtp_encryption,
                    'username' => $emailProfile->smtp_username,
                    'password' => $emailProfile->smtp_password,
                    'timeout' => 30,
                    'local_domain' => null,
                ],
                'mail.from' => [
                    'address' => $emailProfile->email_from_address,
                    'name' => $emailProfile->email_from_name,
                ],
            ]);

            try {
                Mail::mailer('test_smtp')->to($order->customer_email)->send(new OnlineOrderPlaced($order));
            } catch (\Exception $e) {
                \Log::error('Failed to send online order email: ' . $e->getMessage());
            }
        }
    }
}
