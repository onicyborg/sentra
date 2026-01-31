<?php

namespace App\Http\Middleware;

use Closure;

class CheckPermission
{
    public function handle($request, Closure $next, string $permission)
    {
        abort_unless(auth()->user()?->hasPermission($permission), 403);
        return $next($request);
    }
}
