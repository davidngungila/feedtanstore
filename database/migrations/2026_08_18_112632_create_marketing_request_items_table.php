<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_request_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('marketing_request_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained();
            $table->integer('quantity_requested');
            $table->integer('quantity_provided')->nullable();
            $table->decimal('unit_price', 15, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_request_items');
    }
};
