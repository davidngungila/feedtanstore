<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rider_dispatch_batches', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['pending', 'accepted', 'cancelled'])->default('pending');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('target_rider_id')->nullable()->constrained('delivery_riders')->nullOnDelete();
            $table->foreignId('accepted_rider_id')->nullable()->constrained('delivery_riders')->nullOnDelete();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->string('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('rider_dispatch_batch_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rider_dispatch_batch_id')->constrained()->onDelete('cascade');
            $table->foreignId('delivery_rider_id')->constrained()->onDelete('cascade');
            $table->enum('response', ['accepted', 'declined'])->default('declined');
            $table->unique(['rider_dispatch_batch_id', 'delivery_rider_id'], 'rider_dispatch_batch_responses_unique');
            $table->timestamps();
        });

        Schema::table('rider_dispatch_requests', function (Blueprint $table) {
            $table->foreignId('dispatch_batch_id')->nullable()->after('online_order_id')
                ->constrained('rider_dispatch_batches')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('rider_dispatch_requests', function (Blueprint $table) {
            $table->dropConstrainedForeignId('dispatch_batch_id');
        });

        Schema::dropIfExists('rider_dispatch_batch_responses');
        Schema::dropIfExists('rider_dispatch_batches');
    }
};
