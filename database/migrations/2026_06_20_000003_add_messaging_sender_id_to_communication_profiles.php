<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('communication_profiles', function (Blueprint $table) {
            $table->string('messaging_sender_id')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('communication_profiles', function (Blueprint $table) {
            $table->dropColumn('messaging_sender_id');
        });
    }
};
