<?php

namespace Database\Seeders;

use App\Data\MockData;
use App\Models\Category;
use App\Models\Combo;
use App\Models\ComboItem;
use App\Models\Product;
use App\Models\ProductOption;
use App\Models\ProductOptionValue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // ── Categories ─────────────────────────────────────────
        $catMap = [];
        foreach ([
            ['name' => 'Mì & Phở', 'slug' => 'noodles', 'icon' => '🍜', 'priority' => 1],
            ['name' => 'Cơm',      'slug' => 'rice',    'icon' => '🍚', 'priority' => 2],
            ['name' => 'Ăn vặt',   'slug' => 'snacks',  'icon' => '🍗', 'priority' => 3],
            ['name' => 'Đồ uống',  'slug' => 'drinks',  'icon' => '🧋', 'priority' => 4],
        ] as $c) {
            $catMap[$c['slug']] = Category::create($c);
        }

        // ── Products ───────────────────────────────────────────
        $productMap = [];
        foreach (MockData::menuItems() as $item) {
            $cat     = $catMap[$item['category']] ?? $catMap['snacks'];
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

            if (!empty($item['sizes'])) {
                $opt = ProductOption::create(['product_id' => $product->id, 'name' => 'Size', 'type' => 'required']);
                foreach ($item['sizes'] as $s) {
                    ProductOptionValue::create(['option_id' => $opt->id, 'label' => $s['name'], 'extra_price' => $s['price']]);
                }
            }

            if (!empty($item['toppings'])) {
                $opt = ProductOption::create(['product_id' => $product->id, 'name' => 'Topping', 'type' => 'optional']);
                foreach ($item['toppings'] as $t) {
                    ProductOptionValue::create(['option_id' => $opt->id, 'label' => $t['name'], 'extra_price' => $t['price']]);
                }
            }

            $productMap[$item['id']] = $product;
        }

        // ── Combos ─────────────────────────────────────────────
        foreach ([
            ['name' => 'Combo Văn phòng A', 'description' => 'Mì trộn + Trà sữa M',        'combo_price' => 65000, 'original_price' => 80000,  'image' => 'https://images.unsplash.com/photo-1658706117692-f80a370adde3?w=300&q=80', 'items' => ['1','2']],
            ['name' => 'Combo Bựa B',       'description' => 'Gà rán 4 miếng + Trà sữa L', 'combo_price' => 90000, 'original_price' => 115000, 'image' => 'https://images.unsplash.com/photo-1765360024320-b2ab819c6f75?w=300&q=80', 'items' => ['4','2']],
            ['name' => 'Combo Phở Deluxe',  'description' => 'Phở bò + Sinh tố xoài',       'combo_price' => 85000, 'original_price' => 105000, 'image' => 'https://images.unsplash.com/photo-1677011454858-8ecb6d4e6ce0?w=300&q=80', 'items' => ['6','5']],
        ] as $c) {
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
                    ComboItem::create(['combo_id' => $combo->id, 'product_id' => $productMap[$mockId]->id, 'quantity' => 1]);
                }
            }
        }
    }
}
