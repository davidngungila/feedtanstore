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

    public function __construct(OnlineOrder $order)
    {
        $this->order = $order->load(['items.product', 'rider', 'user']);
        $settings = \App\Models\StoreSetting::firstOrCreate();
        $baseUrl = $settings->store_url ?? config('app.url');
        // Use short customer reference without the # for URL safety
        $trackingIdentifier = substr($this->order->short_customer_reference, 1);
        $this->trackingUrl = $baseUrl . '/shop/tracking/' . $trackingIdentifier;
        $this->payUrl = $this->trackingUrl . '?pay=1';
        $this->pdfUrl = $baseUrl . '/shop/tracking/' . $trackingIdentifier . '/pdf';
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
        $pdf = new \Dompdf\Dompdf();
        $pdf->loadHtml(view('online.orders-pdf', ['order' => $this->order])->render());
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();

        return [
            Attachment::fromData(fn () => $pdf->output(), $this->order->order_number . '.pdf')
                ->withMime('application/pdf')
        ];
    }
}
