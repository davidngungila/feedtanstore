<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sent_messages', function (Blueprint $table) {
            $table->id();
            $table->string('type'); // email, sms, whatsapp
            $table->string('to');
            $table->string('from')->nullable();
            $table->string('subject')->nullable();
            $table->text('message');
            $table->json('api_response')->nullable();
            $table->string('status')->default('pending');
            $table->string('message_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sent_messages');
    }
};
