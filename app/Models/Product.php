<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'category_id', 'name', 'slug', 'base_price', 'image',
        'description', 'calories', 'is_new', 'is_best_seller', 'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_new'         => 'boolean',
            'is_best_seller' => 'boolean',
            'is_active'      => 'boolean',
        ];
    }

    public function category()    { return $this->belongsTo(Category::class); }
    public function options()     { return $this->hasMany(ProductOption::class); }
    public function ingredients() { return $this->hasMany(ProductIngredient::class); }
    public function combos()      { return $this->belongsToMany(Combo::class, 'combo_items')->withPivot('quantity'); }
}
