<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Trip / tracking sessions - the Bolt/Uber style "Trip" entity mapped onto an online order
        Schema::create('tracking_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('online_order_id')->constrained()->onDelete('cascade');
            $table->foreignId('delivery_rider_id')->constrained()->onDelete('cascade');
            $table->foreignId('customer_id')->nullable()->constrained()->onDelete('set null');

            // Pickup = the store location the rider departs from
            $table->decimal('pickup_latitude', 10, 7);
            $table->decimal('pickup_longitude', 10, 7);
            $table->string('pickup_address')->nullable();

            // Destination = the delivery address
            $table->decimal('destination_latitude', 10, 7);
            $table->decimal('destination_longitude', 10, 7);
            $table->string('destination_address')->nullable();

            $table->enum('status', [
                'requested',
                'accepted',
                'driver_arriving',
                'driver_arrived',
                'trip_started',
                'trip_in_progress',
                'trip_completed',
                'cancelled',
            ])->default('accepted');

            $table->timestamp('started_at')->nullable();
            $table->timestamp('driver_arriving_at')->nullable();
            $table->timestamp('driver_arrived_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            // Cached route summary (distance_meters, duration_seconds, polyline) - refreshed on off-route recalculation
            $table->json('route_data')->nullable();

            $table->timestamps();

            $table->index(['online_order_id', 'status']);
            $table->index(['delivery_rider_id', 'status']);
        });

        // Extend rider_locations with rich GPS metadata + session association
        Schema::table('rider_locations', function (Blueprint $table) {
            $table->foreignId('tracking_session_id')->nullable()->after('longitude')->constrained()->onDelete('set null');
            $table->decimal('heading', 5, 1)->nullable()->after('tracking_session_id');
            $table->decimal('speed', 6, 2)->nullable()->after('heading');
            $table->decimal('accuracy', 6, 2)->nullable()->after('speed');
            $table->timestamp('recorded_at')->nullable()->after('accuracy');
        });

        // Rider online presence
        Schema::table('delivery_riders', function (Blueprint $table) {
            $table->boolean('is_online')->default(false)->after('is_active');
            $table->timestamp('last_seen_at')->nullable()->after('is_online');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_riders', function (Blueprint $table) {
            $table->dropColumn(['is_online', 'last_seen_at']);
        });

        Schema::table('rider_locations', function (Blueprint $table) {
            $table->dropForeign(['tracking_session_id']);
            $table->dropColumn(['tracking_session_id', 'heading', 'speed', 'accuracy', 'recorded_at']);
        });

        Schema::dropIfExists('tracking_sessions');
    }
};
