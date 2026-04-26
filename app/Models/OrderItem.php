<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = ['order_id', 'participant_id', 'product_id', 'quantity', 'price', 'note'];

    public function order()       { return $this->belongsTo(Order::class); }
    public function product()     { return $this->belongsTo(Product::class); }
    public function participant() { return $this->belongsTo(Participant::class); }
    public function options()     { return $this->hasMany(OrderItemOption::class); }

    public function getSubtotalAttribute(): int
    {
        return $this->price * $this->quantity;
    }
}
