<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InventoryAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $alertDate;
    public array $sections;

    /**
     * @param string $alertDate  Y-m-d
     * @param array  $sections   Newly breached items: lowStock, outOfStock,
     *                           expired, expiring7, unusualDiscounts, cashDiscrepancies
     */
    public function __construct(string $alertDate, array $sections = [])
    {
        $this->alertDate = $alertDate;
        $this->sections = $sections;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🚨 Feedtan Stock Alert — ' . $this->alertDate,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.inventory-alert',
            with: [
                'alertDate' => $this->alertDate,
                'sections' => $this->sections,
            ],
        );
    }
}
