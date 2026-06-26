<?php

use App\Models\AdminAccessToken;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:entry-link {--minutes=10 : Minutes before the link expires} {--host= : Override the base host, e.g. https://store.feedtancmg.org}', function () {
    $minutes = max(1, (int) $this->option('minutes'));
    $host = trim((string) $this->option('host'));
    $token = Str::random(40);
    $encryptedToken = Crypt::encryptString($token);
    $entryToken = rtrim(strtr(base64_encode($encryptedToken), '+/', '-_'), '=');

    AdminAccessToken::create([
        'token_hash' => hash('sha256', $token),
        'encrypted_token' => $encryptedToken,
        'expires_at' => now()->addMinutes($minutes),
    ]);

    $baseUrl = $host !== '' ? rtrim($host, '/') : rtrim(config('app.url') ?: url('/'), '/');
    $url = $baseUrl . '/' . $entryToken;

    $this->info('Encrypted admin entry URL:');
    $this->line($url);
    $this->newLine();
    $this->comment("Expires in {$minutes} minute(s).");
})->purpose('Generate a one-time encrypted admin entry URL');
