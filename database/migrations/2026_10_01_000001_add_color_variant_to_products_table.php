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
        Schema::table('products', function (Blueprint $table) {
            if (! Schema::hasColumn('products', 'color')) {
                $table->string('color', 100)->nullable()->after('description');
            }
            if (! Schema::hasColumn('products', 'variant')) {
                $table->string('variant', 100)->nullable()->after('color');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'variant')) {
                $table->dropColumn('variant');
            }
            if (Schema::hasColumn('products', 'color')) {
                $table->dropColumn('color');
            }
        });
    }
};
