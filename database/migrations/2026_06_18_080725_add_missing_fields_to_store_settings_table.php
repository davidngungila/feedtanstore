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
            $table->string('tax_name')->nullable()->after('tax_rate');
            $table->boolean('tax_enabled')->default(false)->after('tax_name');
            $table->string('receipt_header')->nullable()->after('receipt_footer');
            $table->boolean('receipt_show_logo')->default(false)->after('receipt_header');
            $table->boolean('receipt_show_tax')->default(false)->after('receipt_show_logo');
            $table->string('barcode_type')->nullable()->default('CODE128')->after('enable_loyalty');
            $table->integer('barcode_width')->default(300)->after('barcode_type');
            $table->integer('barcode_height')->default(100)->after('barcode_width');
            $table->boolean('barcode_show_text')->default(true)->after('barcode_height');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('store_settings', function (Blueprint $table) {
            $table->dropColumn([
                'tax_name',
                'tax_enabled',
                'receipt_header',
                'receipt_show_logo',
                'receipt_show_tax',
                'barcode_type',
                'barcode_width',
                'barcode_height',
                'barcode_show_text'
            ]);
        });
    }
};
