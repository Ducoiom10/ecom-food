<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoyaltyChallenge extends Model
{
    protected $fillable = ['title', 'description', 'points_reward', 'target_count', 'type', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function userProgress() { return $this->hasMany(UserChallengeProgress::class, 'challenge_id'); }
}
