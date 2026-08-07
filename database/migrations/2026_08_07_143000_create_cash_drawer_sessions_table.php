<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_drawer_sessions', function (Blueprint $table) {
            $table->id();
            $table->string('session_number')->unique();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('opening_balance', 15, 2)->default(0);
            $table->timestamp('opened_at')->nullable();
            $table->decimal('closing_balance', 15, 2)->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->decimal('expected_balance', 15, 2)->nullable();
            $table->decimal('difference', 15, 2)->nullable();
            $table->enum('status', ['opened', 'closed', 'reconciled'])->default('opened');
            $table->text('notes')->nullable();
            $table->foreignId('reconciled_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('reconciled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_drawer_sessions');
    }
};
