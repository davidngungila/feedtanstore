<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->timestamp('barcode_linked_at')->nullable()->after('barcode');
        });

        DB::table('products')
            ->whereNotNull('barcode')
            ->update(['barcode_linked_at' => DB::raw('COALESCE(updated_at, created_at, NOW())')]);
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('barcode_linked_at');
        });
    }
};