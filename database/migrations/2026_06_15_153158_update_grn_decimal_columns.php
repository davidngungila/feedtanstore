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
        // Update goods_received_notes table
        Schema::table('goods_received_notes', function (Blueprint $table) {
            $table->decimal('total', 15, 2)->default(0)->change();
        });

        // Update grn_items table
        Schema::table('grn_items', function (Blueprint $table) {
            $table->decimal('unit_price', 15, 2)->change();
            $table->decimal('total', 15, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert goods_received_notes table
        Schema::table('goods_received_notes', function (Blueprint $table) {
            $table->decimal('total', 10, 2)->default(0)->change();
        });

        // Revert grn_items table
        Schema::table('grn_items', function (Blueprint $table) {
            $table->decimal('unit_price', 10, 2)->change();
            $table->decimal('total', 10, 2)->change();
        });
    }
};
