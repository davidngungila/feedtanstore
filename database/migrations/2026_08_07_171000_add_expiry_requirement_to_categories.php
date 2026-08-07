<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->boolean('requires_expiry_date')->default(false)->after('description');
            $table->integer('default_shelf_life_days')->nullable()->after('requires_expiry_date');
        });
    }

    public function down(): void
    {
        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn(['requires_expiry_date', 'default_shelf_life_days']);
        });
    }
};
