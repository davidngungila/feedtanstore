<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->string('tra_receipt_number')->nullable()->after('status');
            $table->text('tra_verification_link')->nullable()->after('tra_receipt_number');
            $table->text('tra_qr_code')->nullable()->after('tra_verification_link');
            $table->string('tra_status')->nullable()->default('pending')->after('tra_qr_code');
        });
    }

    public function down(): void
    {
        Schema::table('sales', function (Blueprint $table) {
            $table->dropColumn(['tra_receipt_number', 'tra_verification_link', 'tra_qr_code', 'tra_status']);
        });
    }
};
