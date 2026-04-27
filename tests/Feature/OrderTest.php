<?php

namespace Tests\Feature;

use App\Models\Catalog\Product;
use App\Models\Order\Order;
use App\Models\Order\OrderStatusLog;
use App\Models\System\Branch;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_authenticated_user_can_place_order(): void
    {
        $user    = User::factory()->create();
        $branch  = Branch::factory()->create();
        $product = Product::factory()->create(['base_price' => 60000, 'is_active' => true]);

        // Add to cart
        $this->actingAs($user)
            ->postJson(route('client.cart.add'), [
                'product_id' => $product->id,
                'quantity'   => 2,
            ]);

        $res = $this->actingAs($user)
            ->post(route('client.checkout.post'), [
                'payment_method' => 'cod',
                'delivery_mode'  => 'pickup',
                'branch_id'      => $branch->id,
            ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'user_id'    => $user->id,
            'branch_id'  => $branch->id,
            'status'     => 'pending',
            'subtotal'   => 120000,
            'grand_total' => 120000,
        ]);
    }

    public function test_order_status_log_created_on_order_placement(): void
    {
        $user    = User::factory()->create();
        $branch  = Branch::factory()->create();
        $product = Product::factory()->create(['base_price' => 50000, 'is_active' => true]);

        $this->actingAs($user)
            ->postJson(route('client.cart.add'), [
                'product_id' => $product->id,
                'quantity'   => 1,
            ]);

        $this->actingAs($user)
            ->post(route('client.checkout.post'), [
                'payment_method' => 'momo',
                'delivery_mode'  => 'delivery',
                'branch_id'      => $branch->id,
                'delivery_address' => '123 Test St',
            ]);

        $order = Order::first();
        $this->assertDatabaseHas('order_status_logs', [
            'order_id' => $order->id,
            'status'   => 'pending',
        ]);
    }

    public function test_guest_can_track_order_by_number_and_phone(): void
    {
        $user   = User::factory()->create(['phone' => '0912345678']);
        $branch = Branch::factory()->create();
        $order  = Order::factory()->create([
            'user_id'      => $user->id,
            'branch_id'    => $branch->id,
            'order_number' => 'BAE-Q1-20260101-001',
            'status'       => 'confirmed',
        ]);

        $res = $this->post(route('client.track-order.post'), [
            'order_number' => 'BAE-Q1-20260101-001',
            'phone'        => '0912345678',
        ]);

        $res->assertOk()
            ->assertSee('BAE-Q1-20260101-001')
            ->assertSee('confirmed');
    }

    public function test_guest_cannot_track_with_wrong_phone(): void
    {
        $user   = User::factory()->create(['phone' => '0912345678']);
        $branch = Branch::factory()->create();
        $order  = Order::factory()->create([
            'user_id'      => $user->id,
            'branch_id'    => $branch->id,
            'order_number' => 'BAE-Q1-20260101-002',
        ]);

        $res = $this->post(route('client.track-order.post'), [
            'order_number' => 'BAE-Q1-20260101-002',
            'phone'        => '0999999999',
        ]);

        $res->assertRedirect()
            ->assertSessionHasErrors();
    }

    public function test_kds_move_creates_status_log(): void
    {
        $admin  = User::factory()->create(['role' => 'kitchen_staff']);
        $branch = Branch::factory()->create();
        $order  = Order::factory()->create([
            'branch_id'    => $branch->id,
            'status'       => 'confirmed',
            'confirmed_at' => now(),
        ]);

        $this->actingAs($admin)
            ->post(route('admin.kds.move', $order->id));

        $this->assertDatabaseHas('order_status_logs', [
            'order_id' => $order->id,
            'status'   => 'preparing',
        ]);
    }
}
