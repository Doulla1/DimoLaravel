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
     * @param $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role): mixed
    {
        try {
            $user = Auth::user();

            if($user) {
                // Vérifie si l'utilisateur a le rôle requis ou s'il est administrateur
                if ($user->hasRole($role) || $user->hasRole('admin')) {
                    return $next($request);
                }
                else{
                    return response()->json(['message' => 'Unauthorized'], 401);
                }
            }
            else{
                return response()->json(['message' => 'You are not authenticated'], 401);
            }
        }
        catch (\Exception $e) {
            return response()->json(['message' => 'An authentication issue occured'], 401);
        }
    }
}
