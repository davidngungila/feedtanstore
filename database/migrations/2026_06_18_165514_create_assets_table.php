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
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Asset name
            $table->string('type'); // Type: Equipment, Vehicle, Furniture, etc.
            $table->date('purchase_date');
            $table->decimal('cost', 10, 2);
            $table->decimal('current_value', 10, 2)->nullable();
            $table->string('status')->default('Active'); // Active, Sold, Disposed
            $table->text('description')->nullable();
            $table->foreignId('user_id')->constrained(); // Who added it
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
