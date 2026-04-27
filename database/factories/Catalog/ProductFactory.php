<?php

namespace Database\Factories\Catalog;

use App\Models\Catalog\Category;
use App\Models\Catalog\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = fake()->words(2, true);
        return [
            'category_id'   => Category::factory(),
            'name'          => $name,
            'slug'          => Str::slug($name) . '-' . fake()->unique()->numerify('###'),
            'base_price'    => fake()->numberBetween(20000, 120000),
            'image'         => null,
            'description'   => fake()->sentence(),
            'calories'      => fake()->numberBetween(200, 800),
            'is_new'        => fake()->boolean(20),
            'is_best_seller' => fake()->boolean(10),
            'is_active'     => true,
        ];
    }
}
