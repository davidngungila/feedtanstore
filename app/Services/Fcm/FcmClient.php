<?php

namespace App\Services\Fcm;

use App\Services\Fcm\Exceptions\FcmApiException;
use App\Services\Fcm\Exceptions\FcmException;
use App\Services\Fcm\Exceptions\FcmInvalidTokenException;
use App\Services\Fcm\Exceptions\FcmNotConfiguredException;
use App\Services\Fcm\Exceptions\FcmUnauthorizedException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Minimal FCM HTTP v1 client.
 *
 * Handles service-account OAuth2 (JWT assertion -> access token), sends
 * messages to a single device token via the FCM v1 API, retries transient
 * failures, and classifies errors (unregistered tokens, auth failures, ...).
 */
class FcmClient
{
    public function isConfigured(): bool
    {
        return (bool) config('services.fcm.enabled')
            && config('services.fcm.project_id') !== null
            && $this->credentials() !== null;
    }

    /**
     * Send a notification+data message to a single device token.
     *
     * @param  array<string, mixed>  $notification  title/body/... (optional)
     * @param  array<string, string>  $data  string-keyed data payload
     * @param  array<string, mixed>  $platformOptions  android/apns overrides
     * @return array<string, mixed> FCM response body
     *
     * @throws FcmException
     */
    public function send(string $token, ?array $notification, array $data, array $platformOptions = []): array
    {
        $this->ensureConfigured();

        $message = [
            'token' => $token,
            'data' => $this->stringifyData($data),
        ];

        if ($notification) {
            $message['notification'] = $notification;
        }

        $message = array_merge($message, $this->platformPayload($platformOptions));

        return $this->sendMessage($message);
    }

    /**
     * Build and send a full raw FCM v1 message. Used directly for custom payloads.
     *
     * @param  array<string, mixed>  $message  a complete FCM v1 "message" object
     * @return array<string, mixed>
     *
     * @throws FcmException
     */
    public function sendMessage(array $message): array
    {
        $this->ensureConfigured();

        $attempts = max(1, (int) config('services.fcm.max_attempts', 1));
        $delay = (int) config('services.fcm.retry_delay_seconds', 2);
        $url = str_replace('{project_id}', config('services.fcm.project_id'), config('services.fcm.api_uri'));

        for ($attempt = 1; $attempt <= $attempts; $attempt++) {
            try {
                $response = Http::withToken($this->accessToken())
                    ->acceptJson()
                    ->post($url, ['message' => $message]);

                if ($response->successful()) {
                    return $response->json() ?? [];
                }

                $this->handleFailureResponse($response->status(), $response->body(), $attempt, $attempts, $delay);
            } catch (FcmException $e) {
                throw $e;
            } catch (Throwable $e) {
                if ($attempt >= $attempts) {
                    Log::error('FCM send failed after retries', [
                        'error' => $e->getMessage(),
                        'token' => $this->maskToken($message['token'] ?? ''),
                    ]);

                    throw new FcmApiException('FCM request failed: '.$e->getMessage(), 0, $e);
                }

                sleep($delay);
            }
        }

        throw new FcmApiException('FCM request failed after '.$attempts.' attempts.');
    }

    /**
     * OAuth2 access token for the service account, cached until ~5 min before expiry.
     */
    public function accessToken(): string
    {
        return Cache::remember('fcm_oauth_access_token', now()->addMinutes(55), function () {
            return $this->fetchAccessToken();
        });
    }

    /**
     * @return array<string, mixed>|null
     */
    public function credentials(): ?array
    {
        $json = config('services.fcm.credentials_json');

        if (is_string($json) && $json !== '') {
            $decoded = json_decode(base64_decode($json), true);

            return is_array($decoded) ? $decoded : null;
        }

        $path = config('services.fcm.credentials_path');

        if (! is_string($path) || $path === '' || ! is_file($path)) {
            return null;
        }

        $contents = @file_get_contents($path);

        if ($contents === false) {
            return null;
        }

        $decoded = json_decode($contents, true);

        return is_array($decoded) ? $decoded : null;
    }

    private function ensureConfigured(): void
    {
        if (! $this->isConfigured()) {
            throw new FcmNotConfiguredException('FCM is not configured. Set FCM_ENABLED, FCM_PROJECT_ID and FCM_CREDENTIALS_PATH/JSON.');
        }
    }

    private function fetchAccessToken(): string
    {
        $credentials = $this->credentials();

        if (! $credentials || empty($credentials['client_email']) || empty($credentials['private_key'])) {
            throw new FcmNotConfiguredException('FCM service-account credentials are missing client_email or private_key.');
        }

        $now = time();
        $header = $this->urlSafe(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $claims = $this->urlSafe(json_encode([
            'iss' => $credentials['client_email'],
            'scope' => config('services.fcm.oauth_scope'),
            'aud' => config('services.fcm.token_uri'),
            'iat' => $now,
            'exp' => $now + 3600,
        ]));

        $signingInput = $header.'.'.$claims;
        $privateKey = openssl_pkey_get_private($credentials['private_key']);

        if ($privateKey === false || ! openssl_sign($signingInput, $signature, $privateKey, OPENSSL_ALGO_SHA256)) {
            throw new FcmNotConfiguredException('FCM: unable to sign the JWT assertion with the service-account private key.');
        }

        $jwt = $signingInput.'.'.$this->urlSafe($signature);

        $response = Http::asForm()
            ->post(config('services.fcm.token_uri'), [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt,
            ]);

        if (! $response->successful()) {
            Log::error('FCM OAuth token request failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);

            throw new FcmUnauthorizedException('FCM OAuth token request failed with status '.$response->status());
        }

        $token = $response->json('access_token');

        if (! is_string($token) || $token === '') {
            throw new FcmUnauthorizedException('FCM OAuth response did not include an access_token.');
        }

        return $token;
    }

    private function handleFailureResponse(int $status, string $body, int $attempt, int $attempts, int $delay): void
    {
        // Unregistered/invalid tokens are permanent — never retry.
        if ($status === 404 || str_contains($body, 'UNREGISTERED')) {
            throw new FcmInvalidTokenException('FCM device token is unregistered or invalid: '.substr($body, 0, 300));
        }

        if ($status === 401) {
            throw new FcmUnauthorizedException('FCM access token rejected (401).');
        }

        // Transient failures — retry with backoff.
        if ($status === 429 || $status >= 500) {
            if ($attempt < $attempts) {
                sleep($delay);

                return;
            }

            throw new FcmApiException('FCM request failed with status '.$status.' after '.$attempts.' attempts.');
        }

        throw new FcmApiException('FCM request failed with status '.$status.': '.substr($body, 0, 500));
    }

    /**
     * @param  array<string, mixed>  $options
     * @return array<string, mixed>
     */
    private function platformPayload(array $options): array
    {
        $channel = $options['channel'] ?? config('services.fcm.default_channel');
        $sound = $options['sound'] ?? config('services.fcm.default_sound');
        $icon = $options['icon'] ?? config('services.fcm.default_icon');
        $badge = $options['badge'] ?? null;
        $priority = $options['priority'] ?? 'high';
        $clickAction = $options['click_action'] ?? 'OPEN_APP';

        $androidNotification = [
            'channel_id' => $channel,
            'icon' => $icon,
            'sound' => $sound,
            'click_action' => $clickAction,
        ];

        if (isset($options['android'])) {
            $androidNotification = array_merge($androidNotification, $options['android']);
        }

        $payload = [
            'android' => [
                'priority' => $priority,
                'ttl' => $options['ttl'] ?? '86400s',
                'notification' => $androidNotification,
            ],
        ];

        $aps = ['sound' => $sound];

        if ($badge !== null) {
            $aps['badge'] = (int) $badge;
        }

        $payload['apns'] = [
            'headers' => ['apns-priority' => '10', 'apns-push-type' => 'alert'],
            'payload' => ['aps' => $aps],
        ];

        return $payload;
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, string>
     */
    private function stringifyData(array $data): array
    {
        $stringified = [];

        foreach ($data as $key => $value) {
            if ($value === null) {
                continue;
            }

            $stringified[(string) $key] = is_scalar($value) ? (string) $value : json_encode($value);
        }

        return $stringified;
    }

    private function maskToken(string $token): string
    {
        if (strlen($token) <= 8) {
            return '***';
        }

        return substr($token, 0, 4).'...'.substr($token, -4);
    }

    private function urlSafe(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }
}
