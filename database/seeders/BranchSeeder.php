<?php

namespace Database\Seeders;

use App\Models\System\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        foreach (
            [
                ['name' => 'Chi nhánh Hoàn Kiếm', 'address' => '123 Đinh Tiên Hoàng, Hoàn Kiếm, Hà Nội',  'lat' => 21.0285, 'lng' => 105.8542],
                ['name' => 'Chi nhánh Đống Đa',    'address' => '45 Chùa Bộc, Đống Đa, Hà Nội',          'lat' => 21.0245, 'lng' => 105.8412],
                ['name' => 'Chi nhánh Cầu Giấy',   'address' => '88 Xuân Thủy, Cầu Giấy, Hà Nội',        'lat' => 21.0358, 'lng' => 105.7938],
            ] as $b
        ) {
            Branch::create(array_merge($b, ['status' => 'open', 'is_active' => true]));
        }
    }
}
