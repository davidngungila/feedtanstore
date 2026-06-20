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
        Schema::create('communication_profiles', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Profile name, e.g., "Main SMTP", "Backup SMS"
            $table->string('type')->default('email'); // email or sms
            $table->boolean('is_active')->default(false);
            
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
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('communication_profiles');
    }
};
