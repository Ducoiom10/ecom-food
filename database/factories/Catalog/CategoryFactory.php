<?php

namespace Database\Factories\Catalog;

use App\Models\Catalog\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        $name = fake()->unique()->word();
        return [
            'name'     => $name,
            'slug'     => Str::slug($name),
            'icon'     => fake()->emoji(),
            'priority' => fake()->numberBetween(0, 100),
        ];
    }
}
