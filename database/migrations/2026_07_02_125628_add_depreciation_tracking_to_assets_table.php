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
            $columns = [
                'depreciation_start_date' => fn () => $table->date('depreciation_start_date')->nullable(),
                'accumulated_depreciation' => fn () => $table->decimal('accumulated_depreciation', 15, 2)->default(0),
                'last_depreciation_date' => fn () => $table->date('last_depreciation_date')->nullable(),
                'manufacturer' => fn () => $table->string('manufacturer')->nullable(),
                'model' => fn () => $table->string('model')->nullable(),
                'warranty_expiry' => fn () => $table->date('warranty_expiry')->nullable(),
                'assigned_to' => fn () => $table->string('assigned_to')->nullable(),
                'maintenance_notes' => fn () => $table->text('maintenance_notes')->nullable(),
            ];

            foreach ($columns as $column => $add) {
                if (! Schema::hasColumn('assets', $column)) {
                    $add();
                }
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assets', function (Blueprint $table) {
            foreach ([
                'depreciation_start_date',
                'accumulated_depreciation',
                'last_depreciation_date',
                'manufacturer',
                'model',
                'warranty_expiry',
                'assigned_to',
                'maintenance_notes'
            ] as $column) {
                if (Schema::hasColumn('assets', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
