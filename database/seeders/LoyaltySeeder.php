<?php

namespace Database\Seeders;

use App\Models\Loyalty\LoyaltyChallenge;
use Illuminate\Database\Seeder;

class LoyaltySeeder extends Seeder
{
    public function run(): void
    {
        LoyaltyChallenge::insert([
            ['title' => 'Mua 5 đơn liên tiếp',  'description' => 'Đặt 5 đơn liên tiếp không bỏ lỡ',      'points_reward' => 50,  'target_count' => 5, 'type' => 'order_streak', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Đặt vào giờ trưa',      'description' => 'Đặt đơn trong khung 11h-13h tuần này', 'points_reward' => 30,  'target_count' => 5, 'type' => 'lunch_order',  'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Thử thực đơn mới',      'description' => 'Order món có tag NEW lần đầu tiên',     'points_reward' => 20,  'target_count' => 1, 'type' => 'try_new',      'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Giới thiệu bạn bè',     'description' => 'Mời 1 bạn đăng ký và đặt đơn',         'points_reward' => 100, 'target_count' => 1, 'type' => 'referral',     'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
