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
            $table->decimal('delivery_base_fee', 15, 2)->default(2000);
            $table->decimal('delivery_per_km_rate', 15, 2)->default(400);
            $table->decimal('delivery_free_threshold', 15, 2)->default(50000);
            $table->boolean('delivery_use_zone_pricing')->default(false);
            $table->text('delivery_zone_config')->nullable(); // JSON for zone pricing: [ {name: "Zone A", min_km: 0, max_km:3, fee:2500}, ... ]
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'delivery_base_fee',
                'delivery_per_km_rate',
                'delivery_free_threshold',
                'delivery_use_zone_pricing',
                'delivery_zone_config',
            ]);
        });
    }
};
