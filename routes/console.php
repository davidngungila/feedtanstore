<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('admin:entry-link {--minutes=10 : Minutes before the link expires} {--host= : Override the base host, e.g. https://store.feedtancmg.org}', function () {
    $minutes = max(1, (int) $this->option('minutes'));
    $host = trim((string) $this->option('host'));
    $token = Str::random(64);

    $originalRoot = config('app.url');
    $originalScheme = $originalRoot ? parse_url($originalRoot, PHP_URL_SCHEME) : null;

    if ($host !== '') {
        $forcedRoot = rtrim($host, '/');
        $forcedScheme = parse_url($forcedRoot, PHP_URL_SCHEME) ?: 'https';
        URL::forceRootUrl($forcedRoot);
        URL::forceScheme($forcedScheme);
        config(['app.url' => $forcedRoot]);
    }

    try {
        $url = URL::temporarySignedRoute(
            'admin.entry',
            now()->addMinutes($minutes),
            ['token' => $token]
        );
    } finally {
        URL::forceRootUrl($originalRoot);
        if ($originalScheme) {
            URL::forceScheme($originalScheme);
        }
        config(['app.url' => $originalRoot]);
    }

    $this->info('Signed admin entry URL:');
    $this->line($url);
    $this->newLine();
    $this->comment("Expires in {$minutes} minute(s).");
})->purpose('Generate a one-time temporary signed admin entry URL');
