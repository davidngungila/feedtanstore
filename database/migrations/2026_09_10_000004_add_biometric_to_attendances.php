<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'check_in_biometric_verified')) {
                $table->boolean('check_in_biometric_verified')->default(false)->after('check_in_photo');
            }
            if (!Schema::hasColumn('attendances', 'check_out_biometric_verified')) {
                $table->boolean('check_out_biometric_verified')->default(false)->after('check_out_photo');
            }
            if (!Schema::hasColumn('attendances', 'check_in_biometric_type')) {
                $table->string('check_in_biometric_type')->nullable()->after('check_in_biometric_verified'); // fingerprint, face, iris, none
            }
            if (!Schema::hasColumn('attendances', 'check_out_biometric_type')) {
                $table->string('check_out_biometric_type')->nullable()->after('check_out_biometric_verified');
            }
            if (!Schema::hasColumn('attendances', 'device_info')) {
                $table->json('device_info')->nullable()->after('check_out_biometric_type'); // {platform, model, device_id, app_version}
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $cols = ['check_in_biometric_verified','check_out_biometric_verified','check_in_biometric_type','check_out_biometric_type','device_info'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('attendances', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
