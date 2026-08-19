<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->unsignedInteger('tra_gc_used')->nullable()->after('tra_status');
            $table->unsignedInteger('tra_dc_used')->nullable()->after('tra_gc_used');
            $table->string('tra_znum_used')->nullable()->after('tra_dc_used');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['tra_gc_used', 'tra_dc_used', 'tra_znum_used']);
        });
    }
};
