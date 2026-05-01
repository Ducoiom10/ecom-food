<?php

namespace App\Models\User;

use App\Models\Promotion\Voucher;
use Illuminate\Database\Eloquent\Model;

class CartVoucher extends Model
{
    protected $fillable = [
        'user_id',
        'voucher_id',
        'session_key',
    ];

    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
