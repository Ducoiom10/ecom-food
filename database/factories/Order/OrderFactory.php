<?php

namespace Database\Factories\Order;

use App\Models\Order\Order;
use App\Models\System\Branch;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    public function definition(): array
    {
        $branch = Branch::factory();
        $subtotal = fake()->numberBetween(50000, 500000);
        $shipping = fake()->randomElement([0, 15000]);

        return [
            'order_number'     => 'BAE-' . strtoupper(fake()->bothify('??######')),
            'user_id'          => User::factory(),
            'branch_id'        => $branch,
            'status'           => 'pending',
            'delivery_mode'    => fake()->randomElement(['pickup', 'delivery']),
            'payment_method'   => fake()->randomElement(['momo', 'bank', 'cod', 'zalopay']),
            'subtotal'         => $subtotal,
            'discount_amount'  => 0,
            'shipping_fee'     => $shipping,
            'grand_total'      => $subtotal + $shipping,
            'delivery_address' => fake()->optional()->address(),
        ];
    }
}
