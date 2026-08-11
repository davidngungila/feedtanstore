<?php

namespace Tests\Feature;

use App\Models\DeliveryRider;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class RiderProfileImageTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Schema::create('users', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });

        Schema::create('delivery_riders', function ($table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('profile_image')->nullable();
            $table->timestamps();
        });
    }

    public function test_rider_can_upload_profile_image(): void
    {
        Storage::fake('public');

        $user = User::create([
            'name' => 'Rider One',
            'email' => 'rider1@example.com',
            'password' => 'secret123',
        ]);
        $rider = DeliveryRider::create(['user_id' => $user->id, 'name' => 'Rider One']);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/rider/profile-image', [
            'image' => UploadedFile::fake()->image('avatar.jpg', 100, 100),
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Profile image updated');

        $this->assertNotNull($rider->fresh()->profile_image);
        Storage::disk('public')->assertExists($rider->fresh()->profile_image);

        $this->assertNotNull($rider->fresh()->profile_image_url);
    }

    public function test_upload_replaces_previous_image(): void
    {
        Storage::fake('public');

        $user = User::create([
            'name' => 'Rider Two',
            'email' => 'rider2@example.com',
            'password' => 'secret123',
        ]);
        $rider = DeliveryRider::create(['user_id' => $user->id, 'name' => 'Rider Two']);
        Sanctum::actingAs($user);

        $first = $this->postJson('/api/rider/profile-image', [
            'image' => UploadedFile::fake()->image('one.jpg', 100, 100),
        ]);
        $firstPath = $rider->fresh()->profile_image;

        $second = $this->postJson('/api/rider/profile-image', [
            'image' => UploadedFile::fake()->image('two.jpg', 100, 100),
        ]);

        $second->assertOk();
        $this->assertNotSame($firstPath, $rider->fresh()->profile_image);
        Storage::disk('public')->assertMissing($firstPath);
        Storage::disk('public')->assertExists($rider->fresh()->profile_image);
    }

    public function test_rider_can_remove_profile_image(): void
    {
        Storage::fake('public');

        $user = User::create([
            'name' => 'Rider Three',
            'email' => 'rider3@example.com',
            'password' => 'secret123',
        ]);
        $rider = DeliveryRider::create(['user_id' => $user->id, 'name' => 'Rider Three']);
        Sanctum::actingAs($user);

        $this->postJson('/api/rider/profile-image', [
            'image' => UploadedFile::fake()->image('pic.jpg', 100, 100),
        ]);
        $path = $rider->fresh()->profile_image;

        $response = $this->postJson('/api/rider/profile-image', [
            'remove' => true,
        ]);

        $response->assertOk()
            ->assertJsonPath('message', 'Profile image removed');
        $this->assertNull($rider->fresh()->profile_image);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_requires_authentication(): void
    {
        $response = $this->postJson('/api/rider/profile-image', [
            'image' => UploadedFile::fake()->image('x.jpg'),
        ]);

        $response->assertUnauthorized();
    }

    public function test_rejects_non_image_uploads(): void
    {
        Storage::fake('public');

        $user = User::create([
            'name' => 'Rider Four',
            'email' => 'rider4@example.com',
            'password' => 'secret123',
        ]);
        DeliveryRider::create(['user_id' => $user->id, 'name' => 'Rider Four']);
        Sanctum::actingAs($user);

        $response = $this->postJson('/api/rider/profile-image', [
            'image' => UploadedFile::fake()->create('doc.txt', 10),
        ]);

        $response->assertStatus(422);
    }
}
