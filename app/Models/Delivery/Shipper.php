<?php

namespace App\Models\Delivery;

use App\Models\Order\Order;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;

class Shipper extends Model
{
    protected $fillable = ['user_id', 'name', 'phone', 'current_lat', 'current_lng', 'status', 'active_order_count'];

    public function user()   { return $this->belongsTo(User::class); }
    public function orders() { return $this->hasMany(Order::class); }

    public function isFree(): bool { return $this->status === 'free'; }
}
