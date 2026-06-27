<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('store_settings', function (Blueprint $table) {
            if (!Schema::hasColumn('store_settings', 'vfd_protocol')) {
                $table->string('vfd_protocol')->default('esc_at')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn('vfd_protocol');
        });
    }
};
