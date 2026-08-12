<?php

namespace Tests\Feature;

use App\Models\DeliveryRider;
use App\Models\OnlineOrder;
use App\Models\TrackingSession;
use App\Models\User;
use App\Models\UserDevice;
use App\Services\Notifications\NotificationService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class NotificationServiceTest extends TestCase
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

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        config()->set('services.fcm', [
            'enabled' => true,
            'project_id' => 'test-project',
            'credentials_path' => null,
            'credentials_json' => base64_encode(json_encode([
                'client_email' => 'firebase@test-project.iam.gserviceaccount.com',
                'private_key' => self::TEST_PRIVATE_KEY,
            ])),
            'oauth_scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'token_uri' => 'https://oauth2.googleapis.com/token',
            'api_uri' => 'https://fcm.googleapis.com/v1/projects/{project_id}/messages:send',
            'max_attempts' => 1,
            'retry_delay_seconds' => 0,
            'default_channel' => 'general',
            'default_icon' => 'ic_notification',
            'default_sound' => 'default',
        ]);

        Http::fake(function ($request) {
            if (str_contains($request->url(), 'oauth2.googleapis.com')) {
                return Http::response(['access_token' => 'access-123', 'expires_in' => 3599]);
            }

            if (str_contains($request->url(), 'fcm.googleapis.com')) {
                $token = $request['message']['token'] ?? '';

                if ($token === 'stale-token') {
                    return Http::response([
                        'error' => ['code' => 404, 'status' => 'NOT_FOUND', 'message' => 'Requested entity was not found.'],
                    ], 404);
                }

                return Http::response(['name' => 'projects/test-project/messages/1']);
            }

            return Http::response();
        });

        Schema::create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('admin');
            $table->timestamps();
        });

        Schema::create('user_devices', function ($table) {
            $table->id();
            $table->foreignId('user_id');
            $table->string('device_name')->nullable();
            $table->string('device_type')->nullable();
            $table->string('browser')->nullable();
            $table->string('fcm_token', 512)->nullable()->unique();
            $table->string('app_version')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamp('last_active_at')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamps();
        });

        Schema::create('delivery_riders', function ($table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_online')->default(false);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });

        Schema::create('online_orders', function ($table) {
            $table->id();
            $table->string('order_number');
            $table->foreignId('user_id')->nullable();
            $table->foreignId('delivery_rider_id')->nullable();
            $table->string('status')->default('pending');
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('tracking_sessions', function ($table) {
            $table->id();
            $table->foreignId('online_order_id');
            $table->foreignId('delivery_rider_id')->nullable();
            $table->foreignId('customer_id')->nullable();
            $table->string('status');
            $table->timestamps();
        });
    }

    public function test_sends_to_all_active_devices_of_user(): void
    {
        $user = $this->makeUser('admin', 'admin@example.com', ['token-a', 'token-b']);
        $this->makeUser('cashier', 'cashier@example.com', []);

        $service = app(NotificationService::class);
        $result = $service->sendToUser($user, 'test.event', 'test', 'Title', 'Body');

        $this->assertSame(2, $result['sent']);

        Http::assertSent(function ($request) {
            return str_contains($request->url(), 'fcm.googleapis.com')
                && in_array($request['message']['token'], ['token-a', 'token-b']);
        });
    }

    public function test_deactivates_device_with_expired_token(): void
    {
        $user = $this->makeUser('admin', 'admin@example.com', ['stale-token']);

        $service = app(NotificationService::class);
        $result = $service->sendToUser($user, 'test.event', 'test', 'Title', 'Body');

        $this->assertSame(1, $result['deactivated']);
        $this->assertSame(0, UserDevice::where('user_id', $user->id)->active()->count());
        $this->assertNull(UserDevice::where('user_id', $user->id)->first()->fcm_token);
    }

    public function test_new_order_notifies_staff_with_devices(): void
    {
        $this->makeUser('admin', 'admin@example.com', ['token-admin']);
        $this->makeUser('marketing_officer', 'mo@example.com', []);
        $customer = $this->makeUser('customer', 'customer@example.com', ['token-customer']);

        $order = OnlineOrder::create([
            'order_number' => 'ORD-TEST-1',
            'user_id' => $customer->id,
            'status' => 'pending',
            'total' => 15000,
        ]);

        $result = app(NotificationService::class)->sendOrderNotification($order, 'new');

        $this->assertSame(2, $result['sent']);

        $sentTokens = collect(Http::recorded())
            ->map(fn ($pair) => $pair[0])
            ->filter(fn ($request) => str_contains($request->url(), 'fcm.googleapis.com'))
            ->map(fn ($request) => $request['message']['data']['type'].':'.$request['message']['token'])
            ->sort()
            ->values()
            ->all();

        $this->assertSame(['order.new:token-admin', 'order.new:token-customer'], $sentTokens);
    }

    public function test_trip_completed_notifies_staff_and_customer(): void
    {
        $this->makeUser('admin', 'admin@example.com', ['token-admin']);
        $customer = $this->makeUser('customer', 'customer@example.com', ['token-customer']);
        $riderUser = $this->makeUser('rider', 'rider@example.com', []);
        $rider = DeliveryRider::create(['user_id' => $riderUser->id, 'name' => 'Juma']);

        $order = OnlineOrder::create([
            'order_number' => 'ORD-TRIP-1',
            'user_id' => $customer->id,
            'delivery_rider_id' => $rider->id,
            'status' => 'delivered',
            'total' => 5000,
        ]);

        $session = TrackingSession::create([
            'online_order_id' => $order->id,
            'delivery_rider_id' => $rider->id,
            'status' => TrackingSession::STATUS_TRIP_COMPLETED,
        ]);

        $result = app(NotificationService::class)->sendTripNotification($session, TrackingSession::STATUS_TRIP_COMPLETED);

        $this->assertSame(2, $result['sent']);

        Http::assertSent(function ($request) {
            if (! str_contains($request->url(), 'fcm.googleapis.com')) {
                return false;
            }

            return $request['message']['data']['type'] === 'trip.completed'
                && $request['message']['notification']['title'] === 'Order Delivered';
        });
    }

    public function test_returns_skipped_when_fcm_disabled(): void
    {
        config()->set('services.fcm.enabled', false);

        $user = $this->makeUser('admin', 'admin@example.com', ['token-a']);

        $result = app(NotificationService::class)->sendToUser($user, 'test.event', 'test', 'Title', 'Body');

        $this->assertSame(1, $result['skipped']);
        Http::assertNothingSent();
    }

    private function makeUser(string $role, string $email, array $tokens): User
    {
        $user = User::create([
            'name' => ucfirst($role),
            'email' => $email,
            'password' => 'secret123',
            'role' => $role,
        ]);

        foreach ($tokens as $token) {
            UserDevice::create([
                'user_id' => $user->id,
                'fcm_token' => $token,
                'device_type' => 'android',
                'is_active' => true,
            ]);
        }

        return $user;
    }
}
