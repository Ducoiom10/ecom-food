<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Combo extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'description', 'combo_price', 'original_price', 'image', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function items()    { return $this->hasMany(ComboItem::class); }
    public function products() { return $this->belongsToMany(Product::class, 'combo_items')->withPivot('quantity'); }

    public function getSavingsAttribute(): int
    {
        return $this->original_price - $this->combo_price;
    }
}
