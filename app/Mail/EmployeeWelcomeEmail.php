<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmployeeWelcomeEmail extends Mailable
{
    use SerializesModels;

    public $employee;
    public $password;
    public $loginUrl;

    public function __construct(User $employee, string $password)
    {
        $this->employee = $employee;
        $this->password = $password;
        $settings = \App\Models\StoreSetting::firstOrCreate();
        $baseUrl = $settings->store_url ?? config('app.url');
        $this->loginUrl = rtrim($baseUrl, '/') . '/login';
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Feedtan Store - Your Account Credentials',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.employee-welcome',
            with: [
                'employee' => $this->employee,
                'password' => $this->password,
                'loginUrl' => $this->loginUrl,
            ],
        );
    }
}
