<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        // Vérifier si l'utilisateur est authentifié
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Vous devez être connecté pour accéder à cette page.');
        }

        // Vérifier si l'utilisateur a la permission requise
        if (!auth()->user()->can($permission)) {
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Vous n\'avez pas les permissions nécessaires pour effectuer cette action.',
                    'required_permission' => $permission
                ], 403);
            }

            return redirect()->route('dashboard')->with('error', 'Accès refusé : vous n\'avez pas les permissions nécessaires.');
        }

        return $next($request);
    }
}
