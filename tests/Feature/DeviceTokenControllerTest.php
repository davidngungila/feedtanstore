<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\UserDevice;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class DeviceTokenControllerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

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
    }

    public function test_registers_device_token(): void
    {
        $user = User::create([
            'name' => 'Rider',
            'email' => 'rider@example.com',
            'password' => 'secret123',
            'role' => 'rider',
        ]);

        Sanctum::actingAs($user);

        $response = $this->postJson('/api/device-token', [
            'fcm_token' => 'fcm-token-1',
            'device_type' => 'android',
            'app_version' => '1.2.3',
        ]);

        $response->assertOk()
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('user_devices', [
            'user_id' => $user->id,
            'fcm_token' => 'fcm-token-1',
            'is_active' => true,
            'app_version' => '1.2.3',
        ]);
    }

    public function test_re_registration_updates_existing_device(): void
    {
        $user = User::create([
            'name' => 'Rider',
            'email' => 'rider@example.com',
            'password' => 'secret123',
            'role' => 'rider',
        ]);

        Sanctum::actingAs($user);

        $this->postJson('/api/device-token', ['fcm_token' => 'fcm-token-1'])->assertOk();
        $this->postJson('/api/device-token', ['fcm_token' => 'fcm-token-1', 'app_version' => '2.0.0'])->assertOk();

        $this->assertSame(1, UserDevice::where('user_id', $user->id)->count());
        $this->assertSame('2.0.0', UserDevice::where('user_id', $user->id)->first()->app_version);
    }

    public function test_removes_device_token(): void
    {
        $user = User::create([
            'name' => 'Rider',
            'email' => 'rider@example.com',
            'password' => 'secret123',
            'role' => 'rider',
        ]);

        Sanctum::actingAs($user);

        $this->postJson('/api/device-token', ['fcm_token' => 'fcm-token-1'])->assertOk();

        $response = $this->deleteJson('/api/device-token', ['fcm_token' => 'fcm-token-1']);

        $response->assertOk()
            ->assertJsonPath('message', 'Device token removed');

        $this->assertSame(0, UserDevice::where('user_id', $user->id)->active()->count());
        $this->assertNull(UserDevice::where('user_id', $user->id)->first()->fcm_token);
    }

    public function test_requires_authentication(): void
    {
        $this->postJson('/api/device-token', ['fcm_token' => 'fcm-token-1'])->assertUnauthorized();
    }
}
