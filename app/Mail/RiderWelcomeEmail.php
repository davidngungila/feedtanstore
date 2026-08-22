<?php

namespace App\Mail;

use App\Models\DeliveryRider;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RiderWelcomeEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $rider;
    public $password;
    public $loginUrl;

    public function __construct(DeliveryRider $rider, string $password)
    {
        $this->rider = $rider;
        $this->password = $password;
        $settings = \App\Models\StoreSetting::firstOrCreate();
        $baseUrl = $settings->store_url ?? config('app.url');
        $this->loginUrl = $baseUrl . '/rider/login';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Feedtan Delivery Team - Your Login Credentials',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.rider-welcome',
            with: [
                'rider' => $this->rider,
                'password' => $this->password,
                'loginUrl' => $this->loginUrl,
            ],
        );
    }
}