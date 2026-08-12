<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_devices', function (Blueprint $table) {
            if (! Schema::hasColumn('user_devices', 'fcm_token')) {
                $table->string('fcm_token', 512)->nullable()->unique()->after('browser');
            }
            if (! Schema::hasColumn('user_devices', 'app_version')) {
                $table->string('app_version')->nullable()->after('fcm_token');
            }
            if (! Schema::hasColumn('user_devices', 'last_used_at')) {
                $table->timestamp('last_used_at')->nullable()->after('app_version');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_devices', function (Blueprint $table) {
            $table->dropColumn(['fcm_token', 'app_version', 'last_used_at']);
        });
    }
};
