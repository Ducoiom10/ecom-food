<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('group_rooms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->restrictOnDelete();
            $table->string('room_code', 10)->unique()->index();
            $table->boolean('is_locked')->default(false);
            $table->enum('status', ['active', 'completed'])->default('active');
            $table->timestamps();
        });

        Schema::create('participants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('room_id')->constrained('group_rooms')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('display_name', 100);
            $table->string('emoji', 10)->nullable();
            $table->boolean('is_host')->default(false);
            $table->boolean('is_paid')->default(false);
            $table->timestamps();
        });

        // Thêm host_id vào group_rooms sau khi participants đã tồn tại
        Schema::table('group_rooms', function (Blueprint $table) {
            $table->foreignId('host_id')->nullable()->after('branch_id')->constrained('participants')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('group_rooms', function (Blueprint $table) {
            $table->dropForeign(['host_id']);
            $table->dropColumn('host_id');
        });
        Schema::dropIfExists('participants');
        Schema::dropIfExists('group_rooms');
    }
};
