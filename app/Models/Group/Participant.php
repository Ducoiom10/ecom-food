<?php

namespace App\Models\Group;

use App\Models\Order\Order;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    protected $fillable = ['room_id', 'user_id', 'display_name', 'emoji', 'is_host', 'is_paid'];

    protected function casts(): array
    {
        return [
            'is_host' => 'boolean',
            'is_paid' => 'boolean',
        ];
    }

    public function room()   { return $this->belongsTo(GroupRoom::class, 'room_id'); }
    public function user()   { return $this->belongsTo(User::class); }
    public function orders() { return $this->hasMany(Order::class); }
}
