<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('delivery_riders', 'profile_image')) {
            Schema::table('delivery_riders', function (Blueprint $table) {
                $table->string('profile_image')->nullable()->after('address');
            });
        }
    }

    public function down(): void
    {
        Schema::table('delivery_riders', function (Blueprint $table) {
            if (Schema::hasColumn('delivery_riders', 'profile_image')) {
                $table->dropColumn('profile_image');
            }
        });
    }
};
