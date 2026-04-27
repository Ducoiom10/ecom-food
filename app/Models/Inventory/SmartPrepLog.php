<?php

namespace App\Models\Inventory;

use App\Models\System\Branch;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;

class SmartPrepLog extends Model
{
    protected $fillable = [
        'branch_id', 'inventory_item_id', 'predicted_qty',
        'weather_condition', 'temperature', 'delivery_boost_pct',
        'action_text', 'urgency', 'acknowledged_by', 'acknowledged_at',
    ];

    protected function casts(): array
    {
        return ['acknowledged_at' => 'datetime'];
    }

    public function branch()         { return $this->belongsTo(Branch::class); }
    public function inventoryItem()  { return $this->belongsTo(InventoryItem::class); }
    public function acknowledgedBy() { return $this->belongsTo(User::class, 'acknowledged_by'); }

    public function isPending(): bool { return is_null($this->acknowledged_at); }
}
