<?php

namespace App\Mail;

use App\Models\PurchaseOrder;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PurchaseOrderCreated extends Mailable
{
    use Queueable, SerializesModels;

    public $purchaseOrder;

    /**
     * Create a new message instance.
     */
    public function __construct(PurchaseOrder $purchaseOrder)
    {
        $this->purchaseOrder = $purchaseOrder->load(['supplier', 'items.product']);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Purchase Order: ' . $this->purchaseOrder->po_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.purchase-order-created',
            with: [
                'purchaseOrder' => $this->purchaseOrder,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        // Generate and attach PDF
        $pdf = new \Dompdf\Dompdf();
        $pdf->loadHtml(view('purchasing.orders-pdf', ['purchaseOrder' => $this->purchaseOrder])->render());
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();
        
        return [
            Attachment::fromData(fn () => $pdf->output(), $this->purchaseOrder->po_number . '.pdf')
                ->withMime('application/pdf')
        ];
    }
}
