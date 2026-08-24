<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_prices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->decimal('price', 12, 2);
            $table->string('label')->nullable();
            $table->boolean('is_active')->default(false);
            $table->timestamp('activated_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['product_id', 'is_active']);
        });

        // Seed every product's current selling_price as its first active price entry
        $now = now();
        foreach (DB::table('products')->select('id', 'selling_price')->get() as $product) {
            DB::table('product_prices')->insert([
                'product_id' => $product->id,
                'price' => $product->selling_price ?? 0,
                'label' => 'Initial',
                'is_active' => true,
                'activated_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('product_prices');
    }
};
