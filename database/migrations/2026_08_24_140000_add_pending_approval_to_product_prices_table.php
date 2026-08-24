<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->boolean('pending_approval')->default(false)->after('is_active');
            $table->index(['product_id', 'pending_approval']);
        });
    }

    public function down(): void
    {
        Schema::table('product_prices', function (Blueprint $table) {
            $table->dropIndex(['product_id', 'pending_approval']);
            $table->dropColumn('pending_approval');
        });
    }
};
