<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->boolean('vfd_enabled')->default(false)->after('messaging_sender_id');
            $table->string('vfd_port')->default('COM3')->after('vfd_enabled');
            $table->integer('vfd_baud')->default(9600)->after('vfd_port');
            $table->integer('vfd_data_bits')->default(8)->after('vfd_baud');
            $table->integer('vfd_stop_bits')->default(1)->after('vfd_data_bits');
            $table->string('vfd_parity')->default('none')->after('vfd_stop_bits');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'vfd_enabled',
                'vfd_port',
                'vfd_baud',
                'vfd_data_bits',
                'vfd_stop_bits',
                'vfd_parity'
            ]);
        });
    }
};
