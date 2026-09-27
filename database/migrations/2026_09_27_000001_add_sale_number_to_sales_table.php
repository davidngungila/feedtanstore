<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // sale_number is written by CashierController@completeSale and read by
        // CashierController@lookupSale, but the column was never created.
        if (!Schema::hasColumn('sales', 'sale_number')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->string('sale_number')->nullable()->unique()->after('invoice_number');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('sales', 'sale_number')) {
            Schema::table('sales', function (Blueprint $table) {
                $table->dropColumn('sale_number');
            });
        }
    }
};
