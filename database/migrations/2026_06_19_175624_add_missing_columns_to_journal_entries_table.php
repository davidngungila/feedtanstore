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
        Schema::table('journal_entries', function (Blueprint $table) {
            if (!Schema::hasColumn('journal_entries', 'journal_number')) {
                $table->string('journal_number')->unique()->after('id');
            }
            if (!Schema::hasColumn('journal_entries', 'entry_date')) {
                $table->date('entry_date')->after('journal_number');
            }
            if (!Schema::hasColumn('journal_entries', 'description')) {
                $table->string('description')->after('entry_date');
            }
            if (!Schema::hasColumn('journal_entries', 'reference_type')) {
                $table->string('reference_type')->nullable()->after('description');
            }
            if (!Schema::hasColumn('journal_entries', 'reference_id')) {
                $table->unsignedBigInteger('reference_id')->nullable()->after('reference_type');
            }
            if (!Schema::hasColumn('journal_entries', 'is_manual')) {
                $table->boolean('is_manual')->default(false)->after('reference_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('journal_entries', function (Blueprint $table) {
            $table->dropColumn(['journal_number', 'entry_date', 'description', 'reference_type', 'reference_id', 'is_manual']);
        });
    }
};
