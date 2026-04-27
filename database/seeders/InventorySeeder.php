<?php

namespace Database\Seeders;

use App\Models\Inventory\InventoryItem;
use App\Models\Inventory\SmartPrepLog;
use Illuminate\Database\Seeder;

class InventorySeeder extends Seeder
{
    public function run(): void
    {
        foreach ([
            ['sku' => 'INV-001', 'name' => 'Thịt bò',       'unit' => 'kg',  'current_qty' => 2,  'min_threshold' => 5],
            ['sku' => 'INV-002', 'name' => 'Bánh phở',      'unit' => 'kg',  'current_qty' => 8,  'min_threshold' => 10],
            ['sku' => 'INV-003', 'name' => 'Gà nguyên con', 'unit' => 'con', 'current_qty' => 3,  'min_threshold' => 8],
            ['sku' => 'INV-004', 'name' => 'Trà sữa base',  'unit' => 'lít', 'current_qty' => 15, 'min_threshold' => 10],
            ['sku' => 'INV-005', 'name' => 'Rau sống',      'unit' => 'kg',  'current_qty' => 1,  'min_threshold' => 3],
        ] as $inv) {
            InventoryItem::create(array_merge($inv, ['branch_id' => 1]));
        }

        $invItems = InventoryItem::all();
        foreach ([
            ['urgency' => 'critical', 'item_name' => 'Thịt bò',       'predicted_qty' => 15, 'action' => 'Đặt thêm 13kg ngay'],
            ['urgency' => 'high',     'item_name' => 'Gà nguyên con',  'predicted_qty' => 12, 'action' => 'Chuẩn bị thêm 9 con'],
            ['urgency' => 'high',     'item_name' => 'Bánh phở',       'predicted_qty' => 20, 'action' => 'Đặt thêm 12kg'],
            ['urgency' => 'medium',   'item_name' => 'Rau sống',       'predicted_qty' => 5,  'action' => 'Mua thêm 4kg'],
        ] as $sp) {
            $inv = $invItems->firstWhere('name', $sp['item_name']);
            SmartPrepLog::create([
                'inventory_item_id'  => $inv?->id ?? $invItems->first()->id,
                'branch_id'          => 1,
                'urgency'            => $sp['urgency'],
                'predicted_qty'      => $sp['predicted_qty'],
                'action_text'        => $sp['action'],
                'weather_condition'  => 'rainy',
                'temperature'        => 24,
                'delivery_boost_pct' => 85,
            ]);
        }
    }
}
