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
        if (!Schema::hasTable('journal_entries')) {
            Schema::create('journal_entries', function (Blueprint $table) {
                $table->id();
                $table->string('journal_number')->unique();
                $table->date('entry_date');
                $table->string('description');
                $table->string('reference_type')->nullable();
                $table->unsignedBigInteger('reference_id')->nullable();
                $table->boolean('is_manual')->default(false);
                $table->timestamps();
            });
        }

        if (!Schema::hasColumn('accounting_entries', 'journal_entry_id')) {
            Schema::table('accounting_entries', function (Blueprint $table) {
                $table->foreignId('journal_entry_id')->nullable()->constrained()->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('accounting_entries', function (Blueprint $table) {
            $table->dropForeign(['journal_entry_id']);
            $table->dropColumn('journal_entry_id');
        });
        
        Schema::dropIfExists('journal_entries');
    }
};
