<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->string('order_number')->unique();
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['placed','processing','completed','cancelled'])->default('placed');
            $table->text('shipping_address')->nullable();
            $table->text('notes')->nullable();
            $table->json('status_history')->nullable();
            $table->dateTime('order_date');
            $table->timestamps();
            $table->index(['owner_id', 'status']);
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->restrictOnDelete();
            $table->unsignedInteger('quantity');
            $table->decimal('price_each', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
