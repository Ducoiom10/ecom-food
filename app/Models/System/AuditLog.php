<?php

namespace App\Models\System;

use App\Models\User\User;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['user_id', 'action', 'table_name', 'row_id', 'old_values', 'new_values', 'ip_address'];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function user() { return $this->belongsTo(User::class); }

    public static function record(string $action, string $table, int $rowId, ?array $old = null, ?array $new = null): void
    {
        static::create([
            'user_id'    => auth()->id(),
            'action'     => $action,
            'table_name' => $table,
            'row_id'     => $rowId,
            'old_values' => $old,
            'new_values' => $new,
            'ip_address' => request()->ip(),
        ]);
    }
}
