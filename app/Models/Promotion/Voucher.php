<?php

namespace App\Models\Promotion;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Voucher extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code', 'type', 'value', 'min_order', 'max_discount',
        'max_uses', 'used_count', 'expires_at', 'is_active', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'is_active'  => 'boolean',
        ];
    }

    public function usages()    { return $this->hasMany(VoucherUsage::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }

    public function isValid(): bool
    {
        if (!$this->is_active) return false;
        if ($this->expires_at && $this->expires_at->isPast()) return false;
        if ($this->max_uses && $this->used_count >= $this->max_uses) return false;
        return true;
    }
}
