<?php

namespace App\Models\System;

use App\Models\Group\GroupRoom;
use App\Models\Inventory\InventoryItem;
use App\Models\Order\Order;
use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $fillable = ['name', 'address', 'lat', 'lng', 'status'];

    public function orders()         { return $this->hasMany(Order::class); }
    public function inventoryItems() { return $this->hasMany(InventoryItem::class); }
    public function groupRooms()     { return $this->hasMany(GroupRoom::class); }
}
