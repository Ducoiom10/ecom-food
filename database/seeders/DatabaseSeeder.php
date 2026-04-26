<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            BranchSeeder::class,
            ProductSeeder::class,
            VoucherSeeder::class,
            ShipperSeeder::class,
            InventorySeeder::class,
            LoyaltySeeder::class,
        ]);
    }
}
