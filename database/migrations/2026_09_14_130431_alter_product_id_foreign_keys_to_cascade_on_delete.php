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
            $table->dropForeign('online_order_items_product_id_foreign');
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });

        Schema::table('marketing_request_items', function (Blueprint $table) {
            $table->dropForeign('marketing_request_items_product_id_foreign');
            $table->foreign('product_id')->references('id')->on('products')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('online_order_items', function (Blueprint $table) {
            $table->dropForeign('online_order_items_product_id_foreign');
            $table->foreign('product_id')->references('id')->on('products');
        });

        Schema::table('marketing_request_items', function (Blueprint $table) {
            $table->dropForeign('marketing_request_items_product_id_foreign');
            $table->foreign('product_id')->references('id')->on('products');
        });
    }
};