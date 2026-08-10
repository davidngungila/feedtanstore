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
        Schema::table('online_order_items', function (Blueprint $table) {
            $table->integer('packaged_quantity')->default(0)->after('is_packaged');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('online_order_items', function (Blueprint $table) {
            $table->dropColumn('packaged_quantity');
        });
    }
};
