<?php

namespace Database\Seeders;

use App\Data\MockData;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Combo;
use App\Models\ComboItem;
use App\Models\InventoryItem;
use App\Models\LoyaltyChallenge;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use App\Models\Shipper;
use App\Models\SmartPrepLog;
use App\Models\User;
use App\Models\Voucher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Users ──────────────────────────────────────────────
        User::create([
            'name'     => 'Super Admin',
            'phone'    => '0900000001',
            'email'    => 'admin@baanh.vn',
            'password' => Hash::make('password'),
            'role'     => 'super_admin',
        ]);

        User::create([
            'name'     => 'Minh Tuấn',
            'phone'    => '0901234567',
            'email'    => 'minhtuan@email.com',
            'password' => Hash::make('password'),
            'role'     => 'customer',
        ]);

        // ── Branches ───────────────────────────────────────────
        $branches = [
            ['name' => 'Chi nhánh Quận 1',  'address' => '123 Nguyễn Huệ, Q.1, TP.HCM',       'lat' => 10.7769, 'lng' => 106.7009],
            ['name' => 'Chi nhánh Quận 3',  'address' => '45 Võ Văn Tần, Q.3, TP.HCM',        'lat' => 10.7756, 'lng' => 106.6917],
            ['name' => 'Chi nhánh Bình Thạnh', 'address' => '88 Đinh Bộ Lĩnh, Bình Thạnh, TP.HCM', 'lat' => 10.8031, 'lng' => 106.7143],
        ];

        foreach ($branches as $b) {
            Branch::create(array_merge($b, ['status' => 'open']));
        }

        // ── Categories ─────────────────────────────────────────
        $categories = [
            ['name' => 'Mì & Phở', 'slug' => 'noodles', 'icon' => '🍜', 'priority' => 1],
            ['name' => 'Cơm',      'slug' => 'rice',    'icon' => '🍚', 'priority' => 2],
            ['name' => 'Ăn vặt',   'slug' => 'snacks',  'icon' => '🍗', 'priority' => 3],
            ['name' => 'Đồ uống',  'slug' => 'drinks',  'icon' => '🧋', 'priority' => 4],
        ];

        $catMap = [];
        foreach ($categories as $c) {
            $catMap[$c['slug']] = Category::create($c);
        }

        // ── Products từ MockData ────────────────────────────────
        $productMap = []; // mock_id => Product

        foreach (MockData::menuItems() as $item) {
            $cat = $catMap[$item['category']] ?? $catMap['snacks'];

            $product = Product::create([
                'category_id'    => $cat->id,
                'name'           => $item['name'],
                'slug'           => Str::slug($item['name']) . '-' . $item['id'],
                'base_price'     => $item['price'],
                'image'          => $item['image'],
                'description'    => $item['description'],
                'calories'       => $item['calories'],
                'is_new'         => $item['isNew'],
                'is_best_seller' => $item['isBestSeller'],
                'is_active'      => true,
            ]);

            // Sizes
            if (!empty($item['sizes'])) {
                $sizeOption = ProductOption::create([
                    'product_id' => $product->id,
                    'name'       => 'Size',
                    'type'       => 'required',
                ]);
                foreach ($item['sizes'] as $s) {
                    ProductOptionValue::create([
                        'option_id'   => $sizeOption->id,
                        'label'       => $s['name'],
                        'extra_price' => $s['price'],
                    ]);
                }
            }

            // Toppings
            if (!empty($item['toppings'])) {
                $toppingOption = ProductOption::create([
                    'product_id' => $product->id,
                    'name'       => 'Topping',
                    'type'       => 'optional',
                ]);
                foreach ($item['toppings'] as $t) {
                    ProductOptionValue::create([
                        'option_id'   => $toppingOption->id,
                        'label'       => $t['name'],
                        'extra_price' => $t['price'],
                    ]);
                }
            }

            $productMap[$item['id']] = $product;
        }

        // ── Combos ─────────────────────────────────────────────
        $comboData = [
            ['id' => 'c1', 'name' => 'Combo Văn phòng A', 'description' => 'Mì trộn + Trà sữa M',       'combo_price' => 65000,  'original_price' => 80000,  'image' => 'https://images.unsplash.com/photo-1658706117692-f80a370adde3?w=300&q=80', 'items' => ['1', '2']],
            ['id' => 'c2', 'name' => 'Combo Bựa B',       'description' => 'Gà rán 4 miếng + Trà sữa L', 'combo_price' => 90000,  'original_price' => 115000, 'image' => 'https://images.unsplash.com/photo-1765360024320-b2ab819c6f75?w=300&q=80', 'items' => ['4', '2']],
            ['id' => 'c3', 'name' => 'Combo Phở Deluxe',  'description' => 'Phở bò + Sinh tố xoài',      'combo_price' => 85000,  'original_price' => 105000, 'image' => 'https://images.unsplash.com/photo-1677011454858-8ecb6d4e6ce0?w=300&q=80', 'items' => ['6', '5']],
        ];

        foreach ($comboData as $c) {
            $combo = Combo::create([
                'name'           => $c['name'],
                'description'    => $c['description'],
                'combo_price'    => $c['combo_price'],
                'original_price' => $c['original_price'],
                'image'          => $c['image'],
                'is_active'      => true,
            ]);

            foreach ($c['items'] as $mockId) {
                if (isset($productMap[$mockId])) {
                    ComboItem::create([
                        'combo_id'   => $combo->id,
                        'product_id' => $productMap[$mockId]->id,
                        'quantity'   => 1,
                    ]);
                }
            }
        }

        // ── Vouchers ───────────────────────────────────────────
        Voucher::insert([
            ['code' => 'WELCOME20', 'type' => 'percent', 'value' => 20, 'min_order' => 50000,  'max_discount' => 30000, 'max_uses' => 100, 'used_count' => 12, 'is_active' => true, 'expires_at' => now()->addDays(30), 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'FREESHIP',  'type' => 'shipping','value' => 0,  'min_order' => 80000,  'max_discount' => 25000, 'max_uses' => 50,  'used_count' => 8,  'is_active' => true, 'expires_at' => now()->addDays(15), 'created_at' => now(), 'updated_at' => now()],
            ['code' => 'FLAT30K',   'type' => 'flat',    'value' => 30000, 'min_order' => 100000, 'max_discount' => 30000, 'max_uses' => 30,  'used_count' => 30, 'is_active' => false,'expires_at' => now()->subDay(),    'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── Shippers ───────────────────────────────────────────
        Shipper::insert([
            ['name' => 'Nguyễn Văn A', 'phone' => '0911111111', 'status' => 'free', 'active_order_count' => 0, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Trần Thị B',   'phone' => '0922222222', 'status' => 'busy', 'active_order_count' => 2, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Lê Văn C',     'phone' => '0933333333', 'status' => 'free', 'active_order_count' => 0, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── Inventory Items ────────────────────────────────────
        $inventoryItems = [
            ['branch_id' => 1, 'sku' => 'INV-001', 'name' => 'Thịt bò',      'unit' => 'kg',  'current_qty' => 2,  'min_threshold' => 5,  'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 1, 'sku' => 'INV-002', 'name' => 'Bánh phở',     'unit' => 'kg',  'current_qty' => 8,  'min_threshold' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 1, 'sku' => 'INV-003', 'name' => 'Gà nguyên con', 'unit' => 'con', 'current_qty' => 3,  'min_threshold' => 8,  'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 1, 'sku' => 'INV-004', 'name' => 'Trà sữa base',  'unit' => 'lít', 'current_qty' => 15, 'min_threshold' => 10, 'created_at' => now(), 'updated_at' => now()],
            ['branch_id' => 1, 'sku' => 'INV-005', 'name' => 'Rau sống',      'unit' => 'kg',  'current_qty' => 1,  'min_threshold' => 3,  'created_at' => now(), 'updated_at' => now()],
        ];
        foreach ($inventoryItems as $inv) {
            InventoryItem::create($inv);
        }

        // ── Loyalty Challenges ─────────────────────────────────────
        LoyaltyChallenge::insert([
            ['title' => 'Mua 5 đơn liên tiếp',      'description' => 'Đặt 5 đơn liên tiếp không bỏ lỡ', 'points_reward' => 50,  'target_count' => 5, 'type' => 'order_streak', 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Đặt vào giờ trưa',           'description' => 'Đặt đơn trong khung 11h-13h tuần này',  'points_reward' => 30,  'target_count' => 5, 'type' => 'lunch_order',  'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Thử thực đơn mới',           'description' => 'Order món có tag NEW lần đầu tiên',     'points_reward' => 20,  'target_count' => 1, 'type' => 'try_new',     'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['title' => 'Giới thiệu bạn bè',          'description' => 'Mời 1 bạn đăng ký và đặt đơn',       'points_reward' => 100, 'target_count' => 1, 'type' => 'referral',    'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // ── SmartPrep Logs ─────────────────────────────────────
        $invItems = InventoryItem::all();
        $smartPreps = [
            ['urgency' => 'critical', 'item_name' => 'Thịt bò',       'predicted_qty' => 15, 'current_qty' => 2,  'action' => 'Đặt thêm 13kg ngay'],
            ['urgency' => 'high',     'item_name' => 'Gà nguyên con',  'predicted_qty' => 12, 'current_qty' => 3,  'action' => 'Chuẩn bị thêm 9 con'],
            ['urgency' => 'high',     'item_name' => 'Bánh phở',       'predicted_qty' => 20, 'current_qty' => 8,  'action' => 'Đặt thêm 12kg'],
            ['urgency' => 'medium',   'item_name' => 'Rau sống',       'predicted_qty' => 5,  'current_qty' => 1,  'action' => 'Mua thêm 4kg'],
        ];
        foreach ($smartPreps as $sp) {
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
