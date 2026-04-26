<?php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run(): void
    {
        $branches = [
            ['name' => 'Chi nhánh Quận 1',     'address' => '123 Nguyễn Huệ, Q.1, TP.HCM',          'lat' => 10.7769, 'lng' => 106.7009],
            ['name' => 'Chi nhánh Quận 3',     'address' => '45 Võ Văn Tần, Q.3, TP.HCM',           'lat' => 10.7756, 'lng' => 106.6917],
            ['name' => 'Chi nhánh Bình Thạnh', 'address' => '88 Đinh Bộ Lĩnh, Bình Thạnh, TP.HCM',  'lat' => 10.8031, 'lng' => 106.7143],
        ];

        foreach ($branches as $b) {
            Branch::create(array_merge($b, ['status' => 'open']));
        }
    }
}
