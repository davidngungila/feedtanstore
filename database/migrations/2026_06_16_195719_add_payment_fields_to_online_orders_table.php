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
            $table->string('payment_transaction_id')->nullable()->after('payment_status');
            $table->string('payment_order_reference')->nullable()->after('payment_transaction_id');
            $table->string('clickpesa_status')->nullable()->after('payment_order_reference');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('online_orders', function (Blueprint $table) {
            $table->dropColumn(['payment_transaction_id', 'payment_order_reference', 'clickpesa_status']);
        });
    }
};
