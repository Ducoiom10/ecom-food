<?php

namespace App\Models\System;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $fillable = ['role', 'permission', 'is_allowed'];

    protected function casts(): array
    {
        return [
            'is_allowed' => 'boolean',
        ];
    }

    /**
     * Lấy danh sách permissions cho một role.
     *
     * @return array<string, string[]>
     */
    public static function matrix(): array
    {
        $rows = static::where('is_allowed', true)->get();
        $matrix = [];

        foreach ($rows as $row) {
            $matrix[$row->role][] = $row->permission;
        }

        return $matrix;
    }

    /**
     * Kiểm tra role có quyền hay không.
     */
    public static function has(string $role, string $permission): bool
    {
        return static::where('role', $role)
            ->where('permission', $permission)
            ->where('is_allowed', true)
            ->exists();
    }
}

