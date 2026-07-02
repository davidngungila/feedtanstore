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
            $table->date('depreciation_start_date')->nullable();
            $table->decimal('accumulated_depreciation', 15, 2)->default(0);
            $table->date('last_depreciation_date')->nullable();
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->string('assigned_to')->nullable();
            $table->text('maintenance_notes')->nullable();
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
