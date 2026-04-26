<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('smart_prep_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->foreignId('inventory_item_id')->constrained()->cascadeOnDelete();
            $table->decimal('predicted_qty', 10, 3);
            $table->string('weather_condition')->nullable(); // sunny, rainy, cloudy
            $table->decimal('temperature', 5, 2)->nullable();
            $table->integer('delivery_boost_pct')->nullable(); // % tăng đơn ship
            $table->text('action_text')->nullable(); // "Luộc sẵn 35 vắt mì ngay!"
            $table->enum('urgency', ['low', 'medium', 'high', 'critical'])->default('medium');
            $table->foreignId('acknowledged_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('acknowledged_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smart_prep_logs');
    }
};
