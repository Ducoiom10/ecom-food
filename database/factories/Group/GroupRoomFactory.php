<?php

namespace Database\Factories\Group;

use App\Models\Group\GroupRoom;
use App\Models\System\Branch;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GroupRoomFactory extends Factory
{
    protected $model = GroupRoom::class;

    public function definition(): array
    {
        return [
            'branch_id' => Branch::factory(),
            'host_id'   => User::factory(),
            'room_code' => strtoupper(Str::random(6)),
            'is_locked' => false,
            'status'    => 'active',
        ];
    }
}
