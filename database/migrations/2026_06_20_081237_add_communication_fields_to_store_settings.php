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
        Schema::table('store_settings', function (Blueprint $table) {
            // Email Configuration
            $table->string('smtp_host')->nullable();
            $table->integer('smtp_port')->nullable();
            $table->string('smtp_username')->nullable();
            $table->string('smtp_password')->nullable();
            $table->string('smtp_encryption')->nullable()->default('tls'); // tls, ssl, null
            $table->string('email_from_address')->nullable();
            $table->string('email_from_name')->nullable();

            // SMS Configuration - common providers (Twilio, Nexmo, etc.)
            $table->string('sms_provider')->nullable(); // twilio, nexmo, plivo, etc.
            $table->string('sms_api_key')->nullable();
            $table->string('sms_api_secret')->nullable();
            $table->string('sms_from_number')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'smtp_host',
                'smtp_port',
                'smtp_username',
                'smtp_password',
                'smtp_encryption',
                'email_from_address',
                'email_from_name',
                'sms_provider',
                'sms_api_key',
                'sms_api_secret',
                'sms_from_number',
            ]);
        });
    }
};
