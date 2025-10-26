<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::guard('api')->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Utilisateur non authentifié.',
                'error' => 'UNAUTHENTICATED'
            ], 401);
        }

        // Récupérer le rôle depuis les claims du token JWT
        $token = $request->user()->token();
        $role = $token ? $token->getClaim('role') : null;

        if (!$role || !in_array($role, $roles)) {
            return response()->json([
                'success' => false,
                'message' => 'Accès refusé. Permissions insuffisantes.',
                'error' => 'FORBIDDEN',
                'required_roles' => $roles,
                'user_role' => $role
            ], 403);
        }

        return $next($request);
    }
}
