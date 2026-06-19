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
        Schema::table('products', function (Blueprint $table) {
            $table->text('specifications')->nullable()->after('description');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->foreignId('created_by')->nullable()->constrained('users')->after('status');
            $table->foreignId('approved_by')->nullable()->constrained('users')->after('created_by');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->string('approval_status')->default('pending')->after('approved_at'); // pending, approved, rejected
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('specifications');
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropForeign(['created_by']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['created_by', 'approved_by', 'approved_at', 'approval_status']);
        });
    }
};
