<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cash_drawer_sessions', function (Blueprint $table) {
            $table->decimal('cash_balance', 10, 2)->default(0)->after('opening_balance');
            $table->decimal('mobile_balance', 10, 2)->default(0)->after('cash_balance');
            $table->decimal('bank_balance', 10, 2)->default(0)->after('mobile_balance');
            $table->decimal('online_balance', 10, 2)->default(0)->after('bank_balance');
        });
    }

    public function down(): void
    {
        Schema::table('cash_drawer_sessions', function (Blueprint $table) {
            $table->dropColumn(['cash_balance', 'mobile_balance', 'bank_balance', 'online_balance']);
        });
    }
};
