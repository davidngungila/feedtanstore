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
        // Update sales table
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->default(0)->change();
            $table->decimal('tax', 15, 2)->default(0)->change();
            $table->decimal('discount', 15, 2)->default(0)->change();
            $table->decimal('total', 15, 2)->default(0)->change();
            $table->decimal('paid', 15, 2)->default(0)->change();
            $table->decimal('change', 15, 2)->default(0)->change();
        });

        // Update sale_items table
        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('unit_price', 15, 2)->change();
            $table->decimal('discount', 15, 2)->default(0)->change();
            $table->decimal('total', 15, 2)->change();
        });

        // Update online_orders table
        Schema::table('online_orders', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->change();
            $table->decimal('delivery_fee', 15, 2)->default(0)->change();
            $table->decimal('total', 15, 2)->change();
        });

        // Update online_order_items table
        Schema::table('online_order_items', function (Blueprint $table) {
            $table->decimal('price', 15, 2)->change();
            $table->decimal('total', 15, 2)->change();
        });

        // Update purchase_orders table
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->decimal('subtotal', 15, 2)->default(0)->change();
            $table->decimal('tax', 15, 2)->default(0)->change();
            $table->decimal('discount', 15, 2)->default(0)->change();
            $table->decimal('total', 15, 2)->default(0)->change();
        });

        // Update purchase_order_items table
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->decimal('unit_price', 15, 2)->change();
            $table->decimal('total', 15, 2)->change();
        });

        // Update products table
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('cost_price', 15, 2)->default(0)->change();
            $table->decimal('selling_price', 15, 2)->default(0)->change();
        });

        // Update accounting_entries table
        Schema::table('accounting_entries', function (Blueprint $table) {
            $table->decimal('amount', 15, 2)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert sales table
        Schema::table('sales', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->default(0)->change();
            $table->decimal('tax', 10, 2)->default(0)->change();
            $table->decimal('discount', 10, 2)->default(0)->change();
            $table->decimal('total', 10, 2)->default(0)->change();
            $table->decimal('paid', 10, 2)->default(0)->change();
            $table->decimal('change', 10, 2)->default(0)->change();
        });

        // Revert sale_items table
        Schema::table('sale_items', function (Blueprint $table) {
            $table->decimal('unit_price', 10, 2)->change();
            $table->decimal('discount', 10, 2)->default(0)->change();
            $table->decimal('total', 10, 2)->change();
        });

        // Revert online_orders table
        Schema::table('online_orders', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->change();
            $table->decimal('delivery_fee', 10, 2)->default(0)->change();
            $table->decimal('total', 10, 2)->change();
        });

        // Revert online_order_items table
        Schema::table('online_order_items', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->change();
            $table->decimal('total', 10, 2)->change();
        });

        // Revert purchase_orders table
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->decimal('subtotal', 10, 2)->default(0)->change();
            $table->decimal('tax', 10, 2)->default(0)->change();
            $table->decimal('discount', 10, 2)->default(0)->change();
            $table->decimal('total', 10, 2)->default(0)->change();
        });

        // Revert purchase_order_items table
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->decimal('unit_price', 10, 2)->change();
            $table->decimal('total', 10, 2)->change();
        });

        // Revert products table
        Schema::table('products', function (Blueprint $table) {
            $table->decimal('cost_price', 10, 2)->default(0)->change();
            $table->decimal('selling_price', 10, 2)->default(0)->change();
        });

        // Revert accounting_entries table
        Schema::table('accounting_entries', function (Blueprint $table) {
            $table->decimal('amount', 10, 2)->change();
        });
    }
};
