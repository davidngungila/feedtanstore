<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->unsignedInteger('tra_gc')->default(1)->after('tra_licence');
            $table->unsignedInteger('tra_dc')->default(1)->after('tra_gc');
            $table->string('tra_znum')->default(date('Ymd'))->after('tra_dc');
            $table->date('tra_dc_date')->nullable()->after('tra_znum');
        });
    }

    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn(['tra_gc', 'tra_dc', 'tra_znum', 'tra_dc_date']);
        });
    }
};
