<?php

namespace Database\Factories\Promotion;

use App\Models\Promotion\Voucher;
use Illuminate\Database\Eloquent\Factories\Factory;

class VoucherFactory extends Factory
{
    protected $model = Voucher::class;

    public function definition(): array
    {
        return [
            'code'        => strtoupper(fake()->unique()->bothify('???###')),
            'type'        => fake()->randomElement(['flat', 'percent', 'shipping']),
            'value'       => fake()->numberBetween(5000, 50000),
            'min_order'   => fake()->numberBetween(0, 100000),
            'max_discount' => fake()->optional()->numberBetween(20000, 100000),
            'max_uses'    => fake()->optional()->numberBetween(10, 1000),
            'used_count'  => 0,
            'expires_at'  => fake()->optional()->dateTimeBetween('+1 week', '+3 months'),
            'is_active'   => true,
        ];
    }
}
