<?php

namespace App\Mail;

use App\Models\OnlineOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OnlineOrderPlaced extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $trackingUrl;
    public $payUrl;
    public $pdfUrl;

    public function __construct(OnlineOrder $order, $trackingUrl = null, $payUrl = null, $pdfUrl = null)
    {
        $this->order = $order->load(['items.product', 'rider', 'user']);
        $settings = \App\Models\StoreSetting::firstOrCreate();
        $baseUrl = $settings->store_url ?? config('app.url');
        // Use short customer reference without the # for tracking URL
        $trackingIdentifier = ltrim($this->order->short_customer_reference, '#');
        $this->trackingUrl = $trackingUrl ?? ($baseUrl . '/shop/tracking/' . $trackingIdentifier);
        $this->payUrl = $payUrl ?? ($baseUrl . '/shop/payment/' . $order->payment_token);
        $this->pdfUrl = $pdfUrl ?? ($baseUrl . '/shop/receipt/' . $order->receipt_token);
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Feedtan Order ' . $this->order->order_number,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.online-order-placed',
            with: [
                'order' => $this->order,
                'trackingUrl' => $this->trackingUrl,
                'payUrl' => $this->payUrl,
                'pdfUrl' => $this->pdfUrl,
            ],
        );
    }

    public function attachments(): array
    {
        // Removed PDF attachment per user request
        return [];
    }
}
