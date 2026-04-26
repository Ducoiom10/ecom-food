<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PushCampaign extends Model
{
    protected $fillable = ['title', 'body', 'segment', 'sent_count', 'created_by', 'sent_at'];

    protected function casts(): array
    {
        return ['sent_at' => 'datetime'];
    }

    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
}
