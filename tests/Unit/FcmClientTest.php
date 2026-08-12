<?php

namespace Tests\Unit;

use App\Services\Fcm\Exceptions\FcmInvalidTokenException;
use App\Services\Fcm\Exceptions\FcmNotConfiguredException;
use App\Services\Fcm\Exceptions\FcmUnauthorizedException;
use App\Services\Fcm\FcmClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class FcmClientTest extends TestCase
{
    private const TEST_PRIVATE_KEY = <<<'PEM'
-----BEGIN PRIVATE KEY-----
MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQC3PYvwbLdX2lrv
SkOzt6BjTzlLO9/n0K5Y3RYHuKa1q6LRrL2K02DYkGZxKzp5qMgySKiDyMqrhrFo
TDgEqDoobanHRQ8MJDte2YoJEGTQ29w1ygvn3k13YKj6fGeXA6B0518XeoousT3E
boo03ZnuSqrTp82pxE/nWcUZ1rzRK3iN0N7EIx6zlkRoQL4dbMGeFRSFU5jaa55I
lyPIWfvCy/cmJYTo3u/w+r07p+70xfQJn9Z1csFYIMFn/tS35ZSkK3xcoHcLmVCJ
fdx6popCaSLr8D0SSPwtsUrDLqflA6l/r3MEIRkdBiyNqiQX3u7G4KW0Hdmjeyvj
rNF/kZ0bAgMBAAECggEAOUhgwFrEIA8vQUH2ky6S0ajZENdZYice8cG/ms9TMlTD
FAgLwuPckSbnF3a7k3+7gdir8XKqRN/ZAvFcy7vpXm0V16kTkKic9MRNvhKlaZNp
rlkIysX4cprBiHiui4uDNDiGRhk1LG6VEBy8UNV7wv4NlBgPl4Q6tGigULkMEtkc
FBwy0cSMM4iqZLrx7IjQOZcRx5ZyNPrUYnWAuR0zfHS/hpZaeFOI68kirTbuhx6f
nNRvVW2OqDsmAbI9LDdi1uiTvDcDprFL/SCxE/vns5x7bZDM7kVUvkgCT/6ehO+j
yDEIkrQv+QwbKAyPpjaTYRAj2rLtUm/qlnzuIdGFKQKBgQDhP5xTbUA+pcVzTbIC
IaoW4txtbeQ1e+rGRXhJ2DTaj/eerYAvlPhSVHS3niqmXNsFOCW1n3CbRR8CE2Qi
lczOYyYqxUPZHiaua3H/iUnspmj+G8b2QONfmH8yxvhtNikXZrbJ81jyJKRqlnsx
v48Fhzx7BKjVfPwi+wcsJnPWmQKBgQDQQcM5Y3oexhMrHab2BMkPs6oE4Vbv6ynw
l+R21EMvqPiFI2KDaJsPGnU8HA94g0LJCffhUs0yxBgq3jg1YrwgXG43TBes0cox
VA/SUDItyzsgwf0S+TqjxCmu36eGasbYDI2ddE+Wgfln5YfnxTnH0+40AkxZfu37
OjxW1hTF0wKBgQDW7kK1nI7r+HQzNNUBkaviULCyvmQ+4LJCZPGFzQeJ8kv+nmGt
hYF51drVhtf9jKb1EQFyj+P8VPVknqozEiuuWA+ISlkWaN3SGvZZNmBSruuKZWjx
ezM6+aGOCyvr0f1dtgX/J/QcgfhdOJ/u9XF8ffGpFOYhaDSTEGNkroBkKQKBgGAR
dhVLJlJ73OvOye5DVty/bHbD3G7gdIBgESwfzr51m+8O26ry3lShR+NqrlhRdMV4
q7htkesROnTL/fHikhX7jXxExccbH8KRnJrQE9W8IpKB6lSOU9an7vKUiZsgNooD
gHBZ7zzmyD59S6xG9tiPkxq61K2UOAPkYWFNcFexAoGAM3o2gJ9eIB6UDoMoI6NF
qNDG6Tj8uN0n/kSNxKgk8ls/C/Q9NSHz0t9/2RdXTHEjLJZ8dBMCaeMnIA8Uzrg1
DaXm6Fo8hMdNbvBf5xPW6/bcaCjD8abn67mcUcvYhgkzbNseeI2BVSj0IRhxUL86
N4whGSW/kRrSAxss5owDMV4=
-----END PRIVATE KEY-----
PEM;

    private string $credentialsJson;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        $this->credentialsJson = base64_encode(json_encode([
            'type' => 'service_account',
            'project_id' => 'test-project',
            'client_email' => 'firebase@test-project.iam.gserviceaccount.com',
            'private_key' => self::TEST_PRIVATE_KEY,
            'token_uri' => 'https://oauth2.googleapis.com/token',
        ]));

        $this->enableFcm();
    }

    public function test_fetches_access_token_and_sends_message(): void
    {
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response(['access_token' => 'access-123', 'expires_in' => 3599]),
            'https://fcm.googleapis.com/*' => Http::response(['name' => 'projects/test-project/messages/1']),
        ]);

        $client = new FcmClient;

        $result = $client->send('device-token-abc', ['title' => 'Hi', 'body' => 'Hello'], [
            'type' => 'trip.completed',
            'order_id' => '42',
        ], ['badge' => 1]);

        $this->assertSame(['name' => 'projects/test-project/messages/1'], $result);
        $this->assertSame('access-123', $client->accessToken());

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'fcm.googleapis.com')
                && $request['message']['token'] === 'device-token-abc'
                && $request['message']['notification']['title'] === 'Hi'
                && $request['message']['data']['type'] === 'trip.completed'
                && $request['message']['data']['order_id'] === '42';
        });

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'oauth2.googleapis.com')
                && str_contains($request->body(), 'grant_type=urn%3Aietf%3Aparams%3Aoauth%3Agrant-type%3Ajwt-bearer');
        });
    }

    public function test_throws_when_token_is_unregistered(): void
    {
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response(['access_token' => 'access-123', 'expires_in' => 3599]),
            'https://fcm.googleapis.com/*' => Http::response([
                'error' => ['code' => 404, 'status' => 'NOT_FOUND', 'message' => 'Requested entity was not found.'],
            ], 404),
        ]);

        $this->expectException(FcmInvalidTokenException::class);

        (new FcmClient)->send('stale-token', ['title' => 't', 'body' => 'b'], []);
    }

    public function test_retries_transient_failure_then_succeeds(): void
    {
        config()->set('services.fcm.max_attempts', 2);
        config()->set('services.fcm.retry_delay_seconds', 0);

        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response(['access_token' => 'access-123', 'expires_in' => 3599]),
            'https://fcm.googleapis.com/*' => Http::sequence()
                ->push(['error' => ['code' => 429, 'status' => 'UNAVAILABLE']], 429)
                ->push(['name' => 'projects/test-project/messages/2']),
        ]);

        $result = (new FcmClient)->send('device-token-abc', ['title' => 't', 'body' => 'b'], []);

        $this->assertSame('projects/test-project/messages/2', $result['name']);

        Http::assertSentCount(3);
    }

    public function test_throws_unauthorized_when_oauth_fails(): void
    {
        Http::fake([
            'https://oauth2.googleapis.com/token' => Http::response(['error' => 'invalid_grant'], 400),
        ]);

        $this->expectException(FcmUnauthorizedException::class);

        (new FcmClient)->send('device-token-abc', ['title' => 't', 'body' => 'b'], []);
    }

    public function test_throws_when_fcm_not_configured(): void
    {
        config()->set('services.fcm.enabled', false);

        $this->expectException(FcmNotConfiguredException::class);

        (new FcmClient)->send('device-token-abc', ['title' => 't', 'body' => 'b'], []);
    }

    private function enableFcm(): void
    {
        config()->set('services.fcm', [
            'enabled' => true,
            'project_id' => 'test-project',
            'credentials_path' => null,
            'credentials_json' => $this->credentialsJson,
            'oauth_scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'token_uri' => 'https://oauth2.googleapis.com/token',
            'api_uri' => 'https://fcm.googleapis.com/v1/projects/{project_id}/messages:send',
            'max_attempts' => 1,
            'retry_delay_seconds' => 0,
            'default_channel' => 'general',
            'default_icon' => 'ic_notification',
            'default_sound' => 'default',
        ]);
    }
}
