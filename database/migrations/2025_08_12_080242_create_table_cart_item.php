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
        Schema::create('cart_item', function (Blueprint $table) {
            $table->id();
            // pk
            $table->unsignedBigInteger('cart_id');
            $table->unsignedBigInteger('product_variant_id');
            $table->foreign('cart_id')->references('id')->on('cart')->onDelete('cascade');
            $table->foreign('product_variant_id')->references('id')->on('product_variant')->onDelete('cascade');
            $table->integer('quantity');
            
            $table->timestamps();

            $table->unique(['cart_id', 'product_variant_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cart_item');
    }
};
