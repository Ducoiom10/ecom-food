<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cart_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->text('options')->nullable(); // JSON as text
            $table->text('option_labels')->nullable(); // JSON as text
            $table->integer('quantity')->default(1);
            $table->integer('price'); // price at time of add
            $table->string('note')->nullable();
            $table->string('session_key')->nullable(); // for guest carts
            $table->timestamps();

            $table->index(['user_id', 'product_id', 'session_key']);
        });

        Schema::create('cart_vouchers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('voucher_id')->constrained()->onDelete('cascade');
            $table->string('session_key')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'session_key'], 'cart_voucher_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cart_items');
        Schema::dropIfExists('cart_vouchers');
    }
};
