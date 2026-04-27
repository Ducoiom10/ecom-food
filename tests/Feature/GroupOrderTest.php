<?php

namespace Tests\Feature;

use App\Models\Catalog\Product;
use App\Models\Group\GroupRoom;
use App\Models\System\Branch;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GroupOrderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->artisan('migrate');
    }

    public function test_authenticated_user_can_create_group_room(): void
    {
        $user   = User::factory()->create();
        $branch = Branch::factory()->create();

        $res = $this->actingAs($user)
            ->post(route('client.group-order.create'), [
                'branch_id' => $branch->id,
            ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('group_rooms', [
            'host_id'    => $user->id,
            'branch_id'  => $branch->id,
            'is_locked'  => false,
            'status'     => 'active',
        ]);
    }

    public function test_user_can_join_group_room_by_code(): void
    {
        $host   = User::factory()->create();
        $guest  = User::factory()->create();
        $branch = Branch::factory()->create();
        $room   = GroupRoom::factory()->create([
            'host_id'    => $host->id,
            'branch_id'  => $branch->id,
            'room_code'  => 'ABC123',
            'is_locked'  => false,
        ]);

        $res = $this->actingAs($guest)
            ->get(route('client.group-order.room', 'ABC123'));

        $res->assertOk()
            ->assertSee('ABC123');
    }

    public function test_user_can_add_item_to_group_order(): void
    {
        $host    = User::factory()->create();
        $branch  = Branch::factory()->create();
        $product = Product::factory()->create(['base_price' => 45000, 'is_active' => true]);
        $room    = GroupRoom::factory()->create([
            'host_id'    => $host->id,
            'branch_id'  => $branch->id,
            'room_code'  => 'XYZ789',
            'is_locked'  => false,
        ]);

        $res = $this->actingAs($host)
            ->post(route('client.group-order.item', 'XYZ789'), [
                'product_id' => $product->id,
                'quantity'   => 2,
            ]);

        $res->assertRedirect();
        $this->assertDatabaseHas('order_items', [
            'product_id' => $product->id,
            'quantity'   => 2,
            'price'      => 45000,
        ]);
    }

    public function test_host_can_lock_group_room(): void
    {
        $host   = User::factory()->create();
        $branch = Branch::factory()->create();
        $room   = GroupRoom::factory()->create([
            'host_id'    => $host->id,
            'branch_id'  => $branch->id,
            'room_code'  => 'LOCK01',
            'is_locked'  => false,
        ]);

        $res = $this->actingAs($host)
            ->post(route('client.group-order.lock', 'LOCK01'));

        $res->assertRedirect();
        $this->assertDatabaseHas('group_rooms', [
            'id'        => $room->id,
            'is_locked' => true,
        ]);
    }

    public function test_non_host_cannot_lock_group_room(): void
    {
        $host   = User::factory()->create();
        $guest  = User::factory()->create();
        $branch = Branch::factory()->create();
        $room   = GroupRoom::factory()->create([
            'host_id'    => $host->id,
            'branch_id'  => $branch->id,
            'room_code'  => 'NOLOCK',
            'is_locked'  => false,
        ]);

        $res = $this->actingAs($guest)
            ->post(route('client.group-order.lock', 'NOLOCK'));

        $res->assertForbidden();
    }
}
