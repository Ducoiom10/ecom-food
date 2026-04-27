<?php

namespace Tests\Feature;

use App\Models\Catalog\Product;
use App\Models\Promotion\Voucher;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CartTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_guest_can_add_item_to_cart(): void
    {
        $product = Product::factory()->create([
            'base_price' => 45000,
            'is_active'  => true,
        ]);

        $res = $this->postJson(route('client.cart.add'), [
            'product_id' => $product->id,
            'quantity'   => 2,
        ]);

        $res->assertOk()
            ->assertJson(['ok' => true, 'count' => 1]);
    }

    public function test_authenticated_user_can_view_cart(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['base_price' => 50000, 'is_active' => true]);

        $this->actingAs($user)
            ->postJson(route('client.cart.add'), [
                'product_id' => $product->id,
                'quantity'   => 1,
            ]);

        $res = $this->actingAs($user)->get(route('client.cart'));
        $res->assertOk()
            ->assertSee($product->name)
            ->assertSee(number_format(50000));
    }

    public function test_user_can_update_cart_quantity(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create(['base_price' => 30000, 'is_active' => true]);
        $cartKey = $product->id . '__';

        $this->actingAs($user)
            ->postJson(route('client.cart.add'), [
                'product_id' => $product->id,
                'quantity'   => 1,
            ]);

        $res = $this->actingAs($user)
            ->patchJson(route('client.cart.update', $cartKey), ['quantity' => 3]);

        $res->assertOk()->assertJson(['ok' => true]);
    }

    public function test_user_can_apply_valid_voucher(): void
    {
        $user = User::factory()->create();
        $product = Product::factory()->create(['base_price' => 100000, 'is_active' => true]);
        $voucher = Voucher::factory()->create([
            'code'      => 'SAVE20K',
            'type'      => 'flat',
            'value'     => 20000,
            'min_order' => 50000,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->postJson(route('client.cart.add'), [
                'product_id' => $product->id,
                'quantity'   => 1,
            ]);

        $res = $this->actingAs($user)
            ->postJson(route('client.cart.voucher'), ['code' => 'SAVE20K']);

        $res->assertOk()
            ->assertJson(['ok' => true, 'code' => 'SAVE20K', 'discount' => 20000]);
    }

    public function test_user_can_remove_cart_item(): void
    {
        $user    = User::factory()->create();
        $product = Product::factory()->create(['base_price' => 30000, 'is_active' => true]);
        $cartKey = $product->id . '__';

        $this->actingAs($user)
            ->postJson(route('client.cart.add'), [
                'product_id' => $product->id,
                'quantity'   => 1,
            ]);

        $res = $this->actingAs($user)
            ->delete(route('client.cart.remove', $cartKey));

        $res->assertRedirect();
    }
}
