<?php

namespace App\Http\Middleware;

use App\Enums\UserRole;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserHasRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isActive()) {
            abort(403, 'Akun Anda tidak aktif.');
        }

        if (empty($roles)) {
            return $next($request);
        }

        $allowed = array_map(fn($r) => UserRole::from($r), $roles);

        if (! $user->hasRole(...$allowed)) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}