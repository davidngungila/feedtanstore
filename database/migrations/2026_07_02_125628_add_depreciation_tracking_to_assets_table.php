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
        Schema::table('assets', function (Blueprint $table) {
            $table->date('depreciation_start_date')->nullable()->after('purchase_date');
            $table->decimal('accumulated_depreciation', 15, 2)->default(0)->after('salvage_value');
            $table->date('last_depreciation_date')->nullable()->after('accumulated_depreciation');
            $table->string('manufacturer')->nullable()->after('serial_number');
            $table->string('model')->nullable()->after('manufacturer');
            $table->date('warranty_expiry')->nullable()->after('model');
            $table->string('assigned_to')->nullable()->after('warranty_expiry');
            $table->text('maintenance_notes')->nullable()->after('assigned_to');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            $table->dropColumn([
                'depreciation_start_date',
                'accumulated_depreciation',
                'last_depreciation_date',
                'manufacturer',
                'model',
                'warranty_expiry',
                'assigned_to',
                'maintenance_notes'
            ]);
        });
    }
};
