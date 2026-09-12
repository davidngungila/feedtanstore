<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('field_sales_orders', function (Blueprint $table) {
            if (!Schema::hasColumn('field_sales_orders', 'driver_code')) {
                $table->string('driver_code', 50)->nullable()->after('sales_rep_id')->index();
            }
            if (!Schema::hasColumn('field_sales_orders', 'driver_name')) {
                $table->string('driver_name', 100)->nullable()->after('driver_code');
            }
            if (!Schema::hasColumn('field_sales_orders', 'reference_code')) {
                $table->string('reference_code', 50)->nullable()->after('driver_name')->index();
            }
            if (!Schema::hasColumn('field_sales_orders', 'sales_date')) {
                $table->date('sales_date')->nullable()->after('offline_created_at')->index();
            }
            if (!Schema::hasColumn('field_sales_orders', 'delivery_rider_id')) {
                $table->foreignId('delivery_rider_id')->nullable()->after('driver_code')->constrained('delivery_riders')->nullOnDelete();
            }
        });

        Schema::table('sales', function (Blueprint $table) {
            if (!Schema::hasColumn('sales', 'driver_code')) {
                $table->string('driver_code', 50)->nullable()->after('sales_channel')->index();
            }
            if (!Schema::hasColumn('sales', 'reference_code')) {
                $table->string('reference_code', 50)->nullable()->after('driver_code')->index();
            }
            if (!Schema::hasColumn('sales', 'sales_date')) {
                $table->date('sales_date')->nullable()->after('created_at');
            }
        });

        // Daily driver reference tracker (optional, for quick lookup)
        if (!Schema::hasTable('driver_daily_references')) {
            Schema::create('driver_daily_references', function (Blueprint $table) {
                $table->id();
                $table->foreignId('sales_rep_id')->constrained('users')->cascadeOnDelete();
                $table->date('sales_date')->index();
                $table->string('driver_code', 50);
                $table->string('driver_name', 100)->nullable();
                $table->foreignId('delivery_rider_id')->nullable()->constrained('delivery_riders')->nullOnDelete();
                $table->text('notes')->nullable();
                $table->timestamps();
                $table->unique(['sales_rep_id', 'sales_date', 'driver_code'], 'driver_daily_unique');
            });
        }
    }

    public function down(): void
    {
        Schema::table('field_sales_orders', function (Blueprint $table) {
            $table->dropColumn(['driver_code','driver_name','reference_code','sales_date','delivery_rider_id']);
        });
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['driver_code','reference_code','sales_date']);
        });
        Schema::dropIfExists('driver_daily_references');
    }
};
