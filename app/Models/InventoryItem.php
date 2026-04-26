<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = ['branch_id', 'sku', 'name', 'unit', 'current_qty', 'max_qty', 'min_threshold'];

    public function branch()       { return $this->belongsTo(Branch::class); }
    public function transactions() { return $this->hasMany(InventoryTransaction::class); }
    public function ingredients()  { return $this->hasMany(ProductIngredient::class); }
    public function smartPrepLogs(){ return $this->hasMany(SmartPrepLog::class); }

    public function isLow(): bool { return $this->current_qty <= $this->min_threshold; }
}
