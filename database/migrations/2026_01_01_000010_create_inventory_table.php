<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->string('sku')->index();
            $table->string('name');
            $table->string('unit'); // kg, lít, cái
            $table->decimal('current_qty', 10, 3)->default(0);
            $table->decimal('max_qty', 10, 3)->default(0);
            $table->decimal('min_threshold', 10, 3)->default(0);
            $table->index('branch_id');
            $table->timestamps();
        });

        Schema::create('inventory_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['sale', 'waste', 'import', 'manual']);
            $table->decimal('quantity', 10, 3);
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->enum('reference_type', ['order', 'manual', 'import', 'waste'])->nullable();
            $table->timestamp('created_at')->useCurrent();
        });

        Schema::create('product_ingredients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            $table->decimal('quantity_per_unit', 10, 3); // 0.5 kg mì cho 1 tô
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_ingredients');
        Schema::dropIfExists('inventory_transactions');
        Schema::dropIfExists('inventory_items');
    }
};
