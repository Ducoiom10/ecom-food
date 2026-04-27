<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;

class ProductOption extends Model
{
    protected $fillable = ['product_id', 'name', 'type'];

    public function product() { return $this->belongsTo(Product::class); }
    public function values()  { return $this->hasMany(ProductOptionValue::class, 'option_id'); }
}
