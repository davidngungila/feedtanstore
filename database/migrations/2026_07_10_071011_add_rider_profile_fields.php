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
        Schema::table('delivery_riders', function (Blueprint $table) {
            $table->date('date_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->text('address')->nullable();
            $table->string('nid_number')->nullable();
            $table->string('driving_license_number')->nullable();
            $table->date('license_expiry_date')->nullable();
            $table->string('vehicle_model')->nullable();
            $table->string('vehicle_color')->nullable();
            $table->string('vehicle_year')->nullable();
            $table->string('insurance_number')->nullable();
            $table->date('insurance_expiry_date')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('bank_account_number')->nullable();
            $table->string('bank_account_name')->nullable();
            $table->string('bank_branch')->nullable();
            $table->string('mobile_money_number')->nullable();
            $table->string('mobile_money_provider')->nullable();
            $table->integer('total_deliveries')->default(0);
            $table->integer('total_earnings')->default(0);
            $table->integer('rating')->default(0);
            $table->integer('total_reviews')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('delivery_riders', function (Blueprint $table) {
            $table->dropColumn([
                'date_of_birth', 'gender', 'address',
                'nid_number', 'driving_license_number', 'license_expiry_date',
                'vehicle_model', 'vehicle_color', 'vehicle_year',
                'insurance_number', 'insurance_expiry_date',
                'bank_name', 'bank_account_number', 'bank_account_name', 'bank_branch',
                'mobile_money_number', 'mobile_money_provider',
                'total_deliveries', 'total_earnings', 'rating', 'total_reviews'
            ]);
        });
    }
};
