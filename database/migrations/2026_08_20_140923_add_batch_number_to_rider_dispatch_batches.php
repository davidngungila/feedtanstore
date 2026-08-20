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
        Schema::table('rider_dispatch_batches', function (Blueprint $table) {
            $table->string('batch_number', 30)->unique()->nullable()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('rider_dispatch_batches', function (Blueprint $table) {
            $table->dropColumn('batch_number');
        });
    }
};
