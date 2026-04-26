<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    public $timestamps = false;

    protected $fillable = ['inventory_item_id', 'type', 'quantity', 'reference_id', 'reference_type'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function inventoryItem() { return $this->belongsTo(InventoryItem::class); }
}
