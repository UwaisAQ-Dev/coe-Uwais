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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete(); // Milik order mana (FK ke orders)
            $table->foreignId('product_id')->constrained()->cascadeOnDelete(); // Produk apa (FK ke products)
            $table->integer('quantity')->default(1); // Bilangan unit
            $table->decimal('unit_price', 10, 2); // Snapshot harga ketika beli
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
