<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'user_id', 'branch_id', 'group_room_id', 'participant_id',
        'voucher_id', 'shipper_id', 'status', 'delivery_mode', 'payment_method', 'priority',
        'subtotal', 'discount_amount', 'shipping_fee', 'grand_total',
        'delivery_address', 'delivery_lat', 'delivery_lng',
        'scheduled_at', 'estimated_eta', 'cancelled_reason',
        'confirmed_at', 'preparing_at', 'ready_at', 'completed_at', 'cancelled_at',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at'  => 'datetime',
            'confirmed_at'  => 'datetime',
            'preparing_at'  => 'datetime',
            'ready_at'      => 'datetime',
            'completed_at'  => 'datetime',
            'cancelled_at'  => 'datetime',
        ];
    }

    public function user()        { return $this->belongsTo(User::class); }
    public function branch()      { return $this->belongsTo(Branch::class); }
    public function groupRoom()   { return $this->belongsTo(GroupRoom::class); }
    public function participant()  { return $this->belongsTo(Participant::class); }
    public function voucher()     { return $this->belongsTo(Voucher::class); }
    public function shipper()     { return $this->belongsTo(Shipper::class); }
    public function items()       { return $this->hasMany(OrderItem::class); }
    public function voucherUsage(){ return $this->hasOne(VoucherUsage::class); }

    public function getElapsedMinutesAttribute(): int
    {
        $from = $this->confirmed_at ?? $this->created_at;
        return (int) $from->diffInMinutes(now());
    }

    public static function generateOrderNumber(string $branchCode): string
    {
        $date = now()->format('Ymd');
        $seq  = static::whereDate('created_at', today())->count() + 1;
        return "BAE-{$branchCode}-{$date}-" . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }
}
