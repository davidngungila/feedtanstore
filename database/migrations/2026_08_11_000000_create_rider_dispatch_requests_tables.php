<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rider_dispatch_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('online_order_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['pending', 'accepted', 'cancelled'])->default('pending');
            $table->foreignId('accepted_rider_id')->nullable()->constrained('delivery_riders')->onDelete('set null');
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('rider_dispatch_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rider_dispatch_request_id')->constrained()->onDelete('cascade');
            $table->foreignId('delivery_rider_id')->constrained()->onDelete('cascade');
            $table->enum('response', ['accepted', 'declined'])->default('declined');
            $table->unique(['rider_dispatch_request_id', 'delivery_rider_id'], 'rider_dispatch_responses_unique');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rider_dispatch_responses');
        Schema::dropIfExists('rider_dispatch_requests');
    }
};
