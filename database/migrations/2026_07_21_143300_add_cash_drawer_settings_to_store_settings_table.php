<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->boolean('cash_drawer_auto_open_after_cash_sale')->default(true);
            $table->boolean('cash_drawer_auto_open_for_cash_in')->default(true);
            $table->boolean('cash_drawer_auto_open_for_cash_out')->default(true);
            $table->boolean('cash_drawer_open_before_sale')->default(false);
        });
    }

    public function down()
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'cash_drawer_auto_open_after_cash_sale',
                'cash_drawer_auto_open_for_cash_in',
                'cash_drawer_auto_open_for_cash_out',
                'cash_drawer_open_before_sale'
            ]);
        });
    }
};
