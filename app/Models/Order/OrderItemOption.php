<?php

namespace App\Models\Order;

use App\Models\Catalog\ProductOptionValue;
use Illuminate\Database\Eloquent\Model;

class OrderItemOption extends Model
{
    protected $fillable = ['order_item_id', 'option_value_id'];

    public function orderItem()   { return $this->belongsTo(OrderItem::class); }
    public function optionValue() { return $this->belongsTo(ProductOptionValue::class, 'option_value_id'); }
}
