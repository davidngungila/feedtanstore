<?php

namespace App\Jobs;

use App\Models\OnlineOrder;
use App\Models\CommunicationProfile;
use App\Mail\OnlineOrderPlaced;
use Illuminate\Support\Facades\Mail;
use App\Services\MessagingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendOnlineOrderNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    /**
     * Create a new job instance.
     */
    public function __construct(OnlineOrder $order)
    {
        try {
            $this->order = $order->load(['items.product']);
        } catch (\Exception $e) {
            \Log::error('Failed to load order items: ' . $e->getMessage());
            $this->order = $order;
        }
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            $order = $this->order;
            
            // Get store settings to use store_url if available
            $settings = \App\Models\StoreSetting::firstOrCreate();
            $baseUrl = $settings->store_url ?? config('app.url');
            // Use tracking token for URLs
            $trackingIdentifier = $order->tracking_token ?? $order->order_number;
            $trackingUrl = $baseUrl . '/shop/tracking/' . $trackingIdentifier;
            $pdfUrl = $baseUrl . '/shop/tracking/' . $trackingIdentifier . '/pdf';
            $trackingPageUrl = $trackingUrl;

            // 1. Send SMS
            try {
                $smsProfile = CommunicationProfile::where('type', 'sms')->where('is_active', true)->first();
                if ($smsProfile && $order->customer_phone) {
                    $smsText = "Thanks $order->customer_name. Your order $order->order_number has been received. Delivery Code: $order->delivery_code. Please keep this code safe and provide it upon delivery. Track: $trackingUrl";
                    $messagingService = new MessagingService($smsProfile->sms_api_key, $smsProfile->messaging_sender_id, false);
                    $messagingService->sendSms($order->customer_phone, $smsText);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send online order SMS: ' . $e->getMessage(), ['exception' => $e]);
            }

            // 2. Send Email
            try {
                if ($order->customer_email) {
                    $emailProfile = CommunicationProfile::where('type', 'email')->where('is_active', true)->first();
                    $mailer = 'smtp'; // Default to smtp
                    if ($emailProfile) {
                        try {
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
                            $mailer = 'test_smtp';
                        } catch (\Exception $configError) {
                            \Log::error('Failed to configure custom email profile: ' . $configError->getMessage(), ['exception' => $configError]);
                            $mailer = 'smtp';
                        }
                    }
                    Mail::mailer($mailer)->to($order->customer_email)->send(new OnlineOrderPlaced($order, $trackingPageUrl, $payUrl, $pdfUrl));
                }
            } catch (\Exception $e) {
                \Log::error('Failed to send online order email: ' . $e->getMessage(), ['exception' => $e]);
            }
        } catch (\Exception $e) {
            \Log::error('SendOnlineOrderNotifications job failed: ' . $e->getMessage(), ['exception' => $e]);
        }
    }
}
