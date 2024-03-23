<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Exceptions\UnauthorizedException;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param mixed ...$roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles): mixed
    {
        $user = Auth::user();

        // Vérifie si l'utilisateur a au moins l'un des rôles requis
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        throw UnauthorizedException::forRoles($roles);
    }
}
