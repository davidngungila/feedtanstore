<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_requests', function (Blueprint $table) {
            $table->id();
            $table->string('request_number')->unique();
            $table->foreignId('requested_by')->constrained('users');
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'accepted', 'processed', 'rejected'])->default('pending');
            $table->foreignId('processed_by')->nullable()->constrained('users');
            $table->text('storekeeper_notes')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_requests');
    }
};
