<?php

namespace Database\Factories\System;

use App\Models\System\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    protected $model = Branch::class;

    public function definition(): array
    {
        return [
            'name'    => fake()->streetName(),
            'address' => fake()->address(),
            'lat'     => fake()->latitude(10.7, 10.85),
            'lng'     => fake()->longitude(106.6, 106.75),
            'status'  => fake()->randomElement(['open', 'closed']),
        ];
    }
}
