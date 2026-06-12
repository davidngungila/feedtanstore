<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up() {
        Schema::create('accounting_entries', function (Blueprint $table) {
            $table->id();
            $table->string('reference_number');
            $table->string('reference_type');
            $table->string('account');
            $table->enum('type', ['debit', 'credit']);
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down() {
        Schema::dropIfExists('accounting_entries');
    }
};
