<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->boolean('online_market_enabled')->default(true);
        });
    }

    public function down()
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn('online_market_enabled');
        });
    }
};
