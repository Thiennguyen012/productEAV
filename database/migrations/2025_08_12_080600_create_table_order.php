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
        Schema::create('order', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('shipping_address');
            $table->string('note')->nullable();
            $table->decimal('total');
            $table->enum('status',['pending', 'confirmed', 'processing', 'shipping', 'delivered', 'completed', 'cancelled'])->default('pending');
            $table->enum('payment_method', ['offline', 'online'])->default('offline');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order');
    }
};
