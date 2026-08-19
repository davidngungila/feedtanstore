<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->string('tra_api_endpoint')->nullable()->after('vfd_protocol');
            $table->string('tra_api_username')->nullable()->after('tra_api_endpoint');
            $table->string('tra_api_password')->nullable()->after('tra_api_username');
            $table->string('tra_tin_number')->nullable()->after('tra_api_password');
            $table->string('tra_vfd_serial')->nullable()->after('tra_tin_number');
            $table->text('tra_licence')->nullable()->after('tra_vfd_serial');
        });
    }

    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'tra_api_endpoint',
                'tra_api_username',
                'tra_api_password',
                'tra_tin_number',
                'tra_vfd_serial',
                'tra_licence',
            ]);
        });
    }
};
