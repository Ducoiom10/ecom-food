<?php

namespace Database\Seeders;

use App\Models\Promotion\Voucher;
use Illuminate\Database\Seeder;

class VoucherSeeder extends Seeder
{
    public function run(): void
    {
        Voucher::insert([
            ['code' => 'WELCOME20', 'type' => 'percent',  'value' => 20,    'min_order' => 50000,  'max_discount' => 30000, 'max_uses' => 100, 'used_count' => 12, 'is_active' => true,  'expires_at' => now()->addDays(30), 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'FREESHIP',  'type' => 'shipping', 'value' => 0,     'min_order' => 80000,  'max_discount' => 25000, 'max_uses' => 50,  'used_count' => 8,  'is_active' => true,  'expires_at' => now()->addDays(15), 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'FLAT30K',   'type' => 'flat',     'value' => 30000, 'min_order' => 100000, 'max_discount' => 30000, 'max_uses' => 30,  'used_count' => 30, 'is_active' => false, 'expires_at' => now()->subDay(),    'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
