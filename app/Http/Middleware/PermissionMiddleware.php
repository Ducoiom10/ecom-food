<?php

namespace App\Http\Middleware;

use App\Models\System\RolePermission;
use Closure;
use Illuminate\Http\Request;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, string $permission): mixed
    {
        $user = $request->user();

        if (!$user) {
            abort(403, 'Không có quyền truy cập.');
        }

        // super_admin luôn có mọi quyền
        if ($user->role === 'super_admin') {
            return $next($request);
        }

        if (!RolePermission::has($user->role, $permission)) {
            abort(403, 'Không có quyền thực hiện hành động này.');
        }

        return $next($request);
    }
}
