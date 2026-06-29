<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('online_orders', function (Blueprint $table) {
            $table->dropUnique(['tracking_token']);
            $table->string('tracking_token', 500)->nullable()->change();
            $table->unique('tracking_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('online_orders', function (Blueprint $table) {
            $table->dropUnique(['tracking_token']);
            $table->string('tracking_token', 64)->nullable()->change();
            $table->unique('tracking_token');
        });
    }
};
