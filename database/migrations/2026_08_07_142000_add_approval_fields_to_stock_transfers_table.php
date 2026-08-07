<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->foreignId('requested_by')->nullable()->constrained('users')->onDelete('cascade')->after('notes');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->after('requested_by');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->timestamp('completed_at')->nullable()->after('approved_at');
        });
    }

    public function down(): void
    {
        Schema::table('stock_transfers', function (Blueprint $table) {
            $table->dropForeign(['requested_by']);
            $table->dropForeign(['approved_by']);
            $table->dropColumn(['requested_by', 'approved_by', 'approved_at', 'completed_at']);
        });
    }
};
