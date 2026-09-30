<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NightlyReportsMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $reportDate;
    public string $salesPdf;
    public string $stockPdf;

    /**
     * @param string $reportDate  Y-m-d the reports cover
     * @param string $salesPdf    Raw PDF bytes: daily sales report
     * @param string $stockPdf    Raw PDF bytes: full current-stock report
     */
    public function __construct(string $reportDate, string $salesPdf, string $stockPdf)
    {
        $this->reportDate = $reportDate;
        $this->salesPdf = $salesPdf;
        $this->stockPdf = $stockPdf;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Daily Sales & Inventory Report — ' . $this->reportDate,
        );
    }

    public function content(): Content
    {
        return new Content(
            text: 'emails.nightly-reports-text',
            with: ['reportDate' => $this->reportDate],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->salesPdf, 'daily-sales-' . $this->reportDate . '.pdf')
                ->as('daily-sales-' . $this->reportDate . '.pdf')
                ->withMime('application/pdf'),
            Attachment::fromData(fn () => $this->stockPdf, 'current-stock-' . $this->reportDate . '.pdf')
                ->as('current-stock-' . $this->reportDate . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
