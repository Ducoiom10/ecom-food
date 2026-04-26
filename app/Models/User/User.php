<?php

namespace App\Models\User;

use App\Models\Order\Order;
use App\Models\Loyalty\UserChallengeProgress;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name', 'phone', 'email', 'password',
        'role', 'snack_points', 'tier', 'avatar_url', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    public function addresses()         { return $this->hasMany(UserAddress::class); }
    public function orders()            { return $this->hasMany(Order::class); }
    public function notifications()     { return $this->hasMany(Notification::class); }
    public function challengeProgress() { return $this->hasMany(UserChallengeProgress::class); }

    public function isAdmin(): bool      { return in_array($this->role, ['super_admin', 'branch_manager', 'coordinator', 'kitchen_staff', 'support']); }
    public function isSuperAdmin(): bool { return $this->role === 'super_admin'; }
}
