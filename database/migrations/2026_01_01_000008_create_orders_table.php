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
            $table->string('order_number')->unique(); // BAE-Q1-20260101-001
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('branch_id')->constrained()->restrictOnDelete();
            $table->foreignId('group_room_id')->nullable()->constrained('group_rooms')->nullOnDelete();
            $table->foreignId('participant_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->foreignId('voucher_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('shipper_id')->nullable()->constrained('shippers')->nullOnDelete();

            $table->enum('status', ['pending','confirmed','preparing','ready','delivering','completed','cancelled'])->default('pending')->index();
            $table->enum('delivery_mode', ['pickup', 'delivery'])->default('pickup');
            $table->enum('payment_method', ['momo','bank','cod','zalopay'])->default('cod');
            $table->enum('priority', ['normal', 'high'])->default('normal');

            // Tài chính
            $table->decimal('subtotal', 12, 0);
            $table->decimal('discount_amount', 12, 0)->default(0);
            $table->decimal('shipping_fee', 12, 0)->default(0);
            $table->decimal('grand_total', 12, 0);

            // Giao hàng
            $table->text('delivery_address')->nullable();
            $table->decimal('delivery_lat', 10, 8)->nullable();
            $table->decimal('delivery_lng', 11, 8)->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->integer('estimated_eta')->nullable(); // phút
            $table->text('cancelled_reason')->nullable();

            // Timestamp tracking cho KDS
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('preparing_at')->nullable();
            $table->timestamp('ready_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            $table->index(['branch_id', 'status']);
            $table->index('created_at');
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('participant_id')->nullable()->constrained('participants')->nullOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->integer('quantity');
            $table->decimal('price', 12, 0); // giá tại thời điểm đặt
            $table->text('note')->nullable();
            $table->timestamps();
        });

        Schema::create('order_item_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('option_value_id')->constrained('product_option_values')->restrictOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_item_options');
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};
