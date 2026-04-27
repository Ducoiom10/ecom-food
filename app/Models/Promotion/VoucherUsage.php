<?php

namespace App\Models\Promotion;

use App\Models\Order\Order;
use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;

class VoucherUsage extends Model
{
    public $timestamps = false;

    protected $fillable = ['voucher_id', 'user_id', 'order_id', 'discount_applied', 'used_at'];

    protected function casts(): array
    {
        return ['used_at' => 'datetime'];
    }

    public function voucher() { return $this->belongsTo(Voucher::class); }
    public function user()    { return $this->belongsTo(User::class); }
    public function order()   { return $this->belongsTo(Order::class); }
}
