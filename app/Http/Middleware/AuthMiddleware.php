<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'utilisateur est authentifié via Passport
        if (!Auth::guard('api')->check()) {
            return response()->json([
                'success' => false,
                'message' => 'Accès non autorisé. Token manquant ou invalide.',
                'error' => 'UNAUTHORIZED'
            ], 401);
        }

        return $next($request);
    }
}
