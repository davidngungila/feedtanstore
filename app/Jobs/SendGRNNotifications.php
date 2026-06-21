<?php

namespace App\Jobs;

use App\Models\GoodsReceivedNote;
use App\Models\CommunicationProfile;
use App\Mail\GoodsReceived;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Mail;
use App\Services\MessagingService;

class SendGRNNotifications implements ShouldQueue
{
    use Queueable;

    protected $grn;

    /**
     * Create a new job instance.
     */
    public function __construct(GoodsReceivedNote $grn)
    {
        $this->grn = $grn->load(['supplier', 'purchaseOrder', 'items.product']);
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $grn = $this->grn;
        $supplier = $grn->supplier;
        
        // 1. Send email to supplier (if supplier has email)
        if ($supplier && $supplier->email) {
            $emailProfile = CommunicationProfile::where('type', 'email')->where('is_active', true)->first();
            if ($emailProfile) {
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
                    Mail::mailer('test_smtp')->to($supplier->email)->send(new GoodsReceived($grn));
                } catch (\Exception $e) {
                    // Log email send failure
                    \Log::error('Failed to send GRN email to supplier: ' . $e->getMessage());
                }
            }
        }
        
        // 2. Send SMS to supplier (if supplier has phone)
        if ($supplier && $supplier->phone) {
            $smsProfile = CommunicationProfile::where('type', 'sms')->where('is_active', true)->first();
            if ($smsProfile) {
                $smsText = "Dear $supplier->name, we have received goods (GRN: $grn->grn_number). Total amount: TZS $grn->total. Thank you! Regards, Feedtan Store";
                
                try {
                    $messagingService = new MessagingService($smsProfile->sms_api_key, $smsProfile->messaging_sender_id, false);
                    $messagingService->sendSms($supplier->phone, $smsText);
                } catch (\Exception $e) {
                    // Log SMS send failure
                    \Log::error('Failed to send GRN SMS to supplier: ' . $e->getMessage());
                }
            }
        }
        
        // 3. Send email/SMS to store admin/manager
        $adminUsers = \App\Models\User::whereIn('role', ['admin', 'manager'])->get();
        foreach ($adminUsers as $admin) {
            // Send email to admin
            if ($admin->email) {
                $emailProfile = CommunicationProfile::where('type', 'email')->where('is_active', true)->first();
                if ($emailProfile) {
                    try {
                        Mail::mailer('test_smtp')->to($admin->email)->send(new GoodsReceived($grn));
                    } catch (\Exception $e) {
                        \Log::error('Failed to send GRN email to admin: ' . $e->getMessage());
                    }
                }
            }
            
            // Send SMS to admin
            if ($admin->phone) {
                $smsProfile = CommunicationProfile::where('type', 'sms')->where('is_active', true)->first();
                if ($smsProfile) {
                    $adminSmsText = "Goods received (GRN: $grn->grn_number) from supplier $supplier->name. Total: TZS $grn->total.";
                    try {
                        $messagingService = new MessagingService($smsProfile->sms_api_key, $smsProfile->messaging_sender_id, false);
                        $messagingService->sendSms($admin->phone, $adminSmsText);
                    } catch (\Exception $e) {
                        \Log::error('Failed to send GRN SMS to admin: ' . $e->getMessage());
                    }
                }
            }
        }
    }
}
