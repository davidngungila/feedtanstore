<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\DeliveryRider;
use App\Models\OnlineOrder;
use App\Models\RiderDispatchBatch;
use App\Models\RiderDispatchRequest;
use App\Models\StoreSetting;
use App\Models\TrackingSession;
use App\Models\User;
use App\Services\Notifications\NotificationService;
use Illuminate\Support\Facades\Schema;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class BulkDispatchBatchTest extends TestCase
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
            $table->string('fcm_token', 512)->nullable()->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('delivery_riders', function ($table) {
            $table->id();
            $table->foreignId('user_id')->nullable();
            $table->string('name')->nullable();
            $table->string('phone')->nullable();
            $table->string('vehicle_type')->nullable();
            $table->string('vehicle_plate')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_online')->default(false);
            $table->timestamp('last_seen_at')->nullable();
            $table->timestamps();
        });

        Schema::create('customers', function ($table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->timestamps();
        });

        Schema::create('online_orders', function ($table) {
            $table->id();
            $table->string('order_number');
            $table->foreignId('customer_id')->nullable();
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('delivery_address')->nullable();
            $table->decimal('delivery_latitude', 10, 7)->nullable();
            $table->decimal('delivery_longitude', 10, 7)->nullable();
            $table->string('status')->default('pending');
            $table->string('packaging_status')->default('pending');
            $table->string('reconciliation_status')->default('pending');
            $table->string('payment_status')->default('pending');
            $table->string('payment_method')->nullable();
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('discount', 12, 2)->default(0);
            $table->decimal('delivery_fee', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->foreignId('delivery_rider_id')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->string('delivery_code')->nullable();
            $table->string('rider_acceptance_status')->nullable();
            $table->timestamp('rider_accepted_at')->nullable();
            $table->timestamps();
        });

        Schema::create('products', function ($table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('online_order_items', function ($table) {
            $table->id();
            $table->foreignId('online_order_id');
            $table->foreignId('product_id')->nullable();
            $table->integer('quantity')->default(1);
            $table->decimal('price', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('rider_dispatch_batches', function ($table) {
            $table->id();
            $table->string('status')->default('pending');
            $table->foreignId('created_by')->nullable();
            $table->foreignId('target_rider_id')->nullable();
            $table->foreignId('accepted_rider_id')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('rider_dispatch_requests', function ($table) {
            $table->id();
            $table->foreignId('online_order_id');
            $table->foreignId('dispatch_batch_id')->nullable();
            $table->string('status')->default('pending');
            $table->foreignId('accepted_rider_id')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('rider_dispatch_responses', function ($table) {
            $table->id();
            $table->foreignId('rider_dispatch_request_id');
            $table->foreignId('delivery_rider_id');
            $table->string('response')->default('declined');
            $table->timestamps();
        });

        Schema::create('rider_dispatch_batch_responses', function ($table) {
            $table->id();
            $table->foreignId('rider_dispatch_batch_id');
            $table->foreignId('delivery_rider_id');
            $table->string('response')->default('declined');
            $table->timestamps();
        });

        Schema::create('online_order_status_histories', function ($table) {
            $table->id();
            $table->foreignId('online_order_id');
            $table->string('status');
            $table->string('payment_status')->nullable();
            $table->string('notes')->nullable();
            $table->foreignId('user_id')->nullable();
            $table->timestamps();
        });

        Schema::create('tracking_sessions', function ($table) {
            $table->id();
            $table->foreignId('online_order_id');
            $table->foreignId('delivery_rider_id')->nullable();
            $table->foreignId('customer_id')->nullable();
            $table->decimal('pickup_latitude', 10, 7)->nullable();
            $table->decimal('pickup_longitude', 10, 7)->nullable();
            $table->string('pickup_address')->nullable();
            $table->decimal('destination_latitude', 10, 7)->nullable();
            $table->decimal('destination_longitude', 10, 7)->nullable();
            $table->string('destination_address')->nullable();
            $table->string('status');
            $table->json('route_data')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->timestamps();
        });

        Schema::create('rider_locations', function ($table) {
            $table->id();
            $table->foreignId('delivery_rider_id');
            $table->foreignId('tracking_session_id')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->timestamp('recorded_at')->nullable();
            $table->timestamps();
        });

        Schema::create('store_settings', function ($table) {
            $table->id();
            $table->string('store_name')->nullable();
            $table->decimal('store_latitude', 10, 7)->nullable();
            $table->decimal('store_longitude', 10, 7)->nullable();
            $table->timestamps();
        });
    }

    public function test_batch_index_lists_pending_batches_for_rider(): void
    {
        [$rider] = $this->makeRider();
        $orders = $this->makeEligibleOrders(2);
        $this->makeBatch($orders);

        Sanctum::actingAs($rider->user);

        $response = $this->getJson('/api/rider/dispatch-batches');

        $response->assertOk();
        $this->assertCount(1, $response->json());
        $this->assertSame(2, $response->json()[0]['order_count']);
        $this->assertCount(2, $response->json()[0]['orders']);
    }

    public function test_batch_disappears_after_rider_declines(): void
    {
        [$rider] = $this->makeRider();
        $orders = $this->makeEligibleOrders(2);
        $batch = $this->makeBatch($orders);

        Sanctum::actingAs($rider->user);

        $response = $this->postJson("/api/rider/dispatch-batches/{$batch->id}/decline");
        $response->assertOk();

        $this->getJson('/api/rider/dispatch-batches')->assertOk()->assertJson([]);
    }

    public function test_accept_batch_assigns_all_orders_and_starts_sessions(): void
    {
        [$rider] = $this->makeRider();
        $orders = $this->makeEligibleOrders(2);
        $batch = $this->makeBatch($orders);

        Sanctum::actingAs($rider->user);

        $response = $this->postJson("/api/rider/dispatch-batches/{$batch->id}/accept");

        $response->assertOk()->assertJson(['order_count' => 2]);

        $this->assertSame($rider->id, $orders[0]->fresh()->delivery_rider_id);
        $this->assertSame('out_for_delivery', $orders[0]->fresh()->status);
        $this->assertSame($rider->id, $orders[1]->fresh()->delivery_rider_id);

        $this->assertSame('accepted', $batch->fresh()->status);
        $this->assertSame($rider->id, $batch->fresh()->accepted_rider_id);

        $this->assertSame(2, TrackingSession::where('delivery_rider_id', $rider->id)->count());
    }

    public function test_batch_not_visible_once_accepted_by_another_rider(): void
    {
        [$riderA] = $this->makeRider();
        [$riderB] = $this->makeRider();
        $orders = $this->makeEligibleOrders(1);
        $batch = $this->makeBatch($orders);

        Sanctum::actingAs($riderA->user);
        $this->postJson("/api/rider/dispatch-batches/{$batch->id}/accept")->assertOk();

        Sanctum::actingAs($riderB->user);
        $this->getJson('/api/rider/dispatch-batches')->assertOk()->assertJson([]);
    }

    private function makeRider(): array
    {
        $user = User::create([
            'name' => 'Rider One',
            'email' => 'rider'.uniqid().'@example.com',
            'password' => 'secret123',
            'role' => 'rider',
        ]);
        $rider = DeliveryRider::create([
            'user_id' => $user->id,
            'name' => 'Rider One',
            'phone' => '255712345678',
            'is_active' => true,
        ]);

        return [$rider, $user];
    }

    private function makeEligibleOrders(int $count): array
    {
        $orders = [];

        for ($i = 0; $i < $count; $i++) {
            $orders[] = OnlineOrder::create([
                'order_number' => 'ORD-BULK-'.$i.'-'.uniqid(),
                'customer_name' => 'Customer '.$i,
                'delivery_latitude' => -3.35 + $i * 0.001,
                'delivery_longitude' => 36.68 + $i * 0.001,
                'status' => 'confirmed',
                'packaging_status' => 'completed',
                'reconciliation_status' => 'completed',
                'payment_status' => 'paid',
                'total' => 10000 + $i,
            ]);
        }

        return $orders;
    }

    private function makeBatch(array $orders): RiderDispatchBatch
    {
        $batch = RiderDispatchBatch::create([
            'status' => 'pending',
            'expires_at' => now()->addMinutes(45),
        ]);

        foreach ($orders as $order) {
            RiderDispatchRequest::create([
                'online_order_id' => $order->id,
                'dispatch_batch_id' => $batch->id,
                'status' => 'pending',
                'expires_at' => $batch->expires_at,
            ]);
        }

        return $batch;
    }
}
