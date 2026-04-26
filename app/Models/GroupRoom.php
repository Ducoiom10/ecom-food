<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GroupRoom extends Model
{
    protected $fillable = ['branch_id', 'host_id', 'room_code', 'is_locked', 'status'];

    protected function casts(): array
    {
        return ['is_locked' => 'boolean'];
    }

    public function branch()       { return $this->belongsTo(Branch::class); }
    public function host()         { return $this->belongsTo(Participant::class, 'host_id'); }
    public function participants() { return $this->hasMany(Participant::class, 'room_id'); }
    public function orders()       { return $this->hasMany(Order::class); }
}
