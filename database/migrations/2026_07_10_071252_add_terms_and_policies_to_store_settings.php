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
        Schema::table('store_settings', function (Blueprint $table) {
            $table->text('terms_of_service')->nullable();
            $table->text('privacy_policy')->nullable();
            $table->text('rider_terms')->nullable();
            $table->text('rider_privacy_policy')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'terms_of_service', 'privacy_policy', 'rider_terms', 'rider_privacy_policy'
            ]);
        });
    }
};
