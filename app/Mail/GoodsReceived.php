<?php

namespace App\Mail;

use App\Models\GoodsReceivedNote;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class GoodsReceived extends Mailable
{
    use Queueable, SerializesModels;

    public $grn;

    /**
     * Create a new message instance.
     */
    public function __construct(GoodsReceivedNote $grn)
    {
        $this->grn = $grn->load(['supplier', 'purchaseOrder', 'items.product']);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Goods Received Note: ' . $this->grn->grn_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.goods-received',
            with: [
                'grn' => $this->grn,
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
        $pdf->loadHtml(view('purchasing.grn-pdf', ['grn' => $this->grn])->render());
        $pdf->setPaper('A4', 'portrait');
        $pdf->render();
        
        return [
            Attachment::fromData(fn () => $pdf->output(), $this->grn->grn_number . '.pdf')
                ->withMime('application/pdf')
        ];
    }
}
