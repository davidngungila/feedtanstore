<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Enhance users table for attendance MVP - add employee fields if not exist
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'employee_id')) {
                $table->string('employee_id')->nullable()->unique()->after('id');
            }
            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable()->after('phone');
            }
            if (!Schema::hasColumn('users', 'position')) {
                $table->string('position')->nullable()->after('department');
            }
        });

        // Enhance attendances table
        Schema::table('attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('attendances', 'check_in_latitude')) {
                $table->decimal('check_in_latitude', 10, 7)->nullable()->after('check_in');
            }
            if (!Schema::hasColumn('attendances', 'check_in_longitude')) {
                $table->decimal('check_in_longitude', 10, 7)->nullable()->after('check_in_latitude');
            }
            if (!Schema::hasColumn('attendances', 'check_out_latitude')) {
                $table->decimal('check_out_latitude', 10, 7)->nullable()->after('check_out');
            }
            if (!Schema::hasColumn('attendances', 'check_out_longitude')) {
                $table->decimal('check_out_longitude', 10, 7)->nullable()->after('check_out_latitude');
            }
            if (!Schema::hasColumn('attendances', 'check_in_photo')) {
                $table->string('check_in_photo')->nullable()->after('check_out_longitude');
            }
            if (!Schema::hasColumn('attendances', 'check_out_photo')) {
                $table->string('check_out_photo')->nullable()->after('check_in_photo');
            }
            if (!Schema::hasColumn('attendances', 'total_hours')) {
                $table->decimal('total_hours', 5, 2)->nullable()->after('check_out_photo');
            }
            if (!Schema::hasColumn('attendances', 'check_in_address')) {
                $table->string('check_in_address')->nullable()->after('total_hours');
            }
            if (!Schema::hasColumn('attendances', 'check_out_address')) {
                $table->string('check_out_address')->nullable()->after('check_in_address');
            }
        });

        // Update status to support more values - ensure it's string (already is)
        // No need to modify, but ensure index on user_id + date
        Schema::table('attendances', function (Blueprint $table) {
            try {
                $table->unique(['user_id', 'date']);
            } catch (\Throwable $e) {
                // Unique may already exist, ignore
            }
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Drop unique if exists
            try {
                $table->dropUnique(['user_id', 'date']);
            } catch (\Throwable $e) {}
        });

        Schema::table('attendances', function (Blueprint $table) {
            $columns = ['check_in_latitude','check_in_longitude','check_out_latitude','check_out_longitude','check_in_photo','check_out_photo','total_hours','check_in_address','check_out_address'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('attendances', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'employee_id')) $table->dropColumn('employee_id');
            if (Schema::hasColumn('users', 'department')) $table->dropColumn('department');
            if (Schema::hasColumn('users', 'position')) $table->dropColumn('position');
        });
    }
};
