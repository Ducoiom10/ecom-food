<?php

namespace App\Models\Catalog;

use Illuminate\Database\Eloquent\Model;

class ProductOptionValue extends Model
{
    protected $fillable = ['option_id', 'label', 'extra_price'];

    public function option() { return $this->belongsTo(ProductOption::class, 'option_id'); }
}
