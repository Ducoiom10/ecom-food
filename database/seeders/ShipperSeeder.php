<?php

namespace Database\Seeders;

use App\Models\Delivery\Shipper;
use Illuminate\Database\Seeder;

class ShipperSeeder extends Seeder
{
    public function run(): void
    {
        Shipper::insert([
            ['name' => 'Nguyễn Văn A', 'phone' => '0911111111', 'status' => 'free', 'active_order_count' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Trần Thị B',   'phone' => '0922222222', 'status' => 'busy', 'active_order_count' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lê Văn C',     'phone' => '0933333333', 'status' => 'free', 'active_order_count' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
