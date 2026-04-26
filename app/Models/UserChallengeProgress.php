<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserChallengeProgress extends Model
{
    protected $fillable = ['user_id', 'challenge_id', 'current_count', 'completed_at'];

    protected function casts(): array
    {
        return ['completed_at' => 'datetime'];
    }

    public function user()      { return $this->belongsTo(User::class); }
    public function challenge() { return $this->belongsTo(LoyaltyChallenge::class, 'challenge_id'); }

    public function isCompleted(): bool { return !is_null($this->completed_at); }
}
